<?php

namespace App\Models;

use App\Enums\AppoinmentStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    public const TYPE_LABELS = [
        'consultation' => 'Consulta',
        'vaccination' => 'Vacunación',
        'surgery' => 'Cirugía',
        'grooming' => 'Peluquería',
    ];

    public const SERVICES_BY_TYPE = [
        'consultation' => [
            'general_consultation' => 'Consulta general',
        ],
        'vaccination' => [
            'annual_vaccination' => 'Vacunación anual',
            'puppy_kitten_scheme' => 'Plan cachorro/gatito',
            'antirabies_vaccination' => 'Vacuna antirrábica',
        ],
        'surgery' => [
            'sterilization' => 'Esterilización',
            'soft_tissue_surgery' => 'Cirugía de tejidos blandos',
            'traumatology_surgery' => 'Cirugía traumatológica',
        ],
        'grooming' => [
            'bath_and_brushing' => 'Baño y cepillado',
            'haircut' => 'Corte de pelo',
            'nail_and_ear_care' => 'Uñas y limpieza de oídos',
        ],
    ];

    protected $fillable = [
        'user_id',
        'pet_id',
        'appointment_date',
        'type',
        'service',
        'description',
        'status',
        'notes',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
    ];

    public static function schedulingRules(): array
    {
        return [
            'slot_minutes' => (int) config('appointments.slot_minutes', 30),
            'daily_limit' => (int) config('appointments.daily_limit', 20),
            'slot_limit' => (int) config('appointments.slot_limit', 2),
            'workday_start' => (string) config('appointments.workday_start', '09:00'),
            'workday_end' => (string) config('appointments.workday_end', '19:00'),
        ];
    }

    public static function activeStatuses(): array
    {
        return [
            AppoinmentStatus::Pending->value,
            AppoinmentStatus::Confirmed->value,
        ];
    }

    public static function autoCompleteDueAppointments(): void
    {
        static::query()
            ->whereIn('status', [AppoinmentStatus::Pending->value, AppoinmentStatus::Confirmed->value])
            ->where('appointment_date', '<=', Carbon::now())
            ->update(['status' => AppoinmentStatus::Completed->value]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public static function typeValues(): array
    {
        return array_keys(self::TYPE_LABELS);
    }

    public static function serviceOptionsByType(?string $type = null): array
    {
        if ($type === null) {
            return self::SERVICES_BY_TYPE;
        }

        return self::SERVICES_BY_TYPE[$type] ?? [];
    }

    public static function serviceValues(?string $type = null): array
    {
        return array_keys(self::serviceOptions($type));
    }

    public static function serviceOptions(?string $type = null): array
    {
        if ($type === null) {
            return collect(self::SERVICES_BY_TYPE)
                ->flatMap(fn ($options) => $options)
                ->all();
        }

        return self::SERVICES_BY_TYPE[$type] ?? [];
    }

    public static function isValidServiceForType(string $type, string $service): bool
    {
        return array_key_exists($service, self::serviceOptions($type));
    }

    public static function hasScheduleConflict(int $petId, mixed $appointmentDate, ?int $ignoreAppointmentId = null): bool
    {
        return static::query()
            ->where('pet_id', $petId)
            ->whereIn('status', self::activeStatuses())
            ->where('appointment_date', $appointmentDate)
            ->when($ignoreAppointmentId, fn ($query) => $query->where('id', '!=', $ignoreAppointmentId))
            ->exists();
    }

    public static function hasCapacityForDateTime(mixed $appointmentDate, ?int $ignoreAppointmentId = null): bool
    {
        $date = Carbon::parse($appointmentDate)->setSecond(0);
        $rules = self::schedulingRules();

        if ($date->isPast()) {
            return false;
        }

        $workdayStart = Carbon::parse($date->format('Y-m-d') . ' ' . $rules['workday_start']);
        $workdayEnd = Carbon::parse($date->format('Y-m-d') . ' ' . $rules['workday_end']);

        if ($date->lt($workdayStart) || $date->gte($workdayEnd)) {
            return false;
        }

        $query = self::query()
            ->whereDate('appointment_date', $date->toDateString())
            ->whereIn('status', self::activeStatuses())
            ->when($ignoreAppointmentId, fn ($builder) => $builder->where('id', '!=', $ignoreAppointmentId));

        $dailyCount = (clone $query)->count();
        if ($dailyCount >= $rules['daily_limit']) {
            return false;
        }

        $slotCount = (clone $query)
            ->where('appointment_date', $date->format('Y-m-d H:i:s'))
            ->count();

        return $slotCount < $rules['slot_limit'];
    }

    public static function availableSlotsForDate(Carbon $date): array
    {
        $rules = self::schedulingRules();
        $dayStart = Carbon::parse($date->format('Y-m-d') . ' ' . $rules['workday_start']);
        $dayEnd = Carbon::parse($date->format('Y-m-d') . ' ' . $rules['workday_end']);
        $slotMinutes = max(5, $rules['slot_minutes']);

        $existing = self::query()
            ->whereDate('appointment_date', $date->toDateString())
            ->whereIn('status', self::activeStatuses())
            ->get(['appointment_date']);

        $countsByDateTime = $existing
            ->groupBy(fn ($item) => Carbon::parse($item->appointment_date)->format('Y-m-d H:i:s'))
            ->map(fn ($group) => $group->count());

        $dailyCount = $existing->count();
        $dailyAvailable = $dailyCount < $rules['daily_limit'];

        $slots = [];
        for ($current = $dayStart->copy(); $current->lt($dayEnd); $current->addMinutes($slotMinutes)) {
            $key = $current->format('Y-m-d H:i:s');
            $booked = (int) ($countsByDateTime[$key] ?? 0);
            $inPast = $current->isPast();
            $isAvailable = !$inPast && $dailyAvailable && $booked < $rules['slot_limit'];

            $slots[] = [
                'value' => $current->format('Y-m-d H:i:s'),
                'time' => $current->format('H:i'),
                'booked' => $booked,
                'capacity' => $rules['slot_limit'],
                'available' => $isAvailable,
            ];
        }

        return [
            'date' => $date->toDateString(),
            'daily_limit' => $rules['daily_limit'],
            'daily_booked' => $dailyCount,
            'daily_available' => $dailyAvailable,
            'slots' => $slots,
        ];
    }

    public function typeLabel(): string
    {
        return self::TYPE_LABELS[$this->type] ?? ucfirst((string) $this->type);
    }

    public function serviceLabel(): string
    {
        return self::serviceOptions($this->type)[$this->service]
            ?? self::serviceOptions()[$this->service]
            ?? 'Servicio general';
    }
}

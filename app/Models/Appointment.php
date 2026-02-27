<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'pet_id',
        'appointment_date',
        'type',
        'description',
        'status',
        'notes',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
    ];

    public static function autoCompleteDueAppointments(): void
    {
        static::query()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('appointment_date', '<=', Carbon::now())
            ->update(['status' => 'completed']);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}

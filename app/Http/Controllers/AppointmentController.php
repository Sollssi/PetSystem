<?php

namespace App\Http\Controllers;

use App\Enums\AppoinmentStatus;
use App\Mail\AppointmentCreatedMail;
use App\Models\Appointment;
use App\Models\Pet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AppointmentController extends Controller
{
    public function index()
    {
        Appointment::autoCompleteDueAppointments();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403, 'No autorizado');
        }

        $appointments = $user->appointments()
            ->with(['pet'])
            ->whereIn('status', Appointment::activeStatuses())
            ->where('appointment_date', '>=', now())
            ->orderBy('appointment_date', 'asc')
            ->paginate(10);

        return view('appointments.index', [
            'appointments' => $appointments,
        ]);
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403, 'No autorizado');
        }

        $pets = $user->pets()->get();

        return view('appointments.create', [
            'pets' => $pets,
            'schedulingRules' => Appointment::schedulingRules(),
        ]);
    }

    public function availability(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
        ]);

        $date = Carbon::parse($validated['date']);
        if ($date->lt(Carbon::today())) {
            return response()->json([
                'message' => 'La fecha debe ser hoy o posterior.',
                'data' => null,
            ], 422);
        }

        return response()->json([
            'data' => Appointment::availableSlotsForDate($date),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'appointment_date' => 'required|date|after:now',
            'type' => 'required|in:' . implode(',', Appointment::typeValues()),
            'service' => 'required|string',
            'description' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        if (!Appointment::isValidServiceForType($validated['type'], $validated['service'])) {
            throw ValidationException::withMessages([
                'service' => 'El servicio seleccionado no corresponde a la categoría elegida.',
            ]);
        }

        $pet = Pet::findOrFail($validated['pet_id']);
        if ($pet->user_id !== Auth::id()) {
            abort(403, 'Esta mascota no te pertenece');
        }

        if (Appointment::hasScheduleConflict((int) $validated['pet_id'], $validated['appointment_date'])) {
            throw ValidationException::withMessages([
                'appointment_date' => 'Esta mascota ya tiene un turno agendado en ese mismo horario. Elegí otro día u hora.',
            ]);
        }

        $appointment = DB::transaction(function () use ($validated) {
            $appointmentDate = Carbon::parse($validated['appointment_date'])->setSecond(0)->format('Y-m-d H:i:s');

            if (!Appointment::hasCapacityForDateTime($appointmentDate)) {
                throw ValidationException::withMessages([
                    'appointment_date' => 'Ese horario ya no está disponible. Elegí otro turno.',
                ]);
            }

            return Appointment::create([
                'user_id' => Auth::id(),
                'pet_id' => $validated['pet_id'],
                'appointment_date' => $appointmentDate,
                'type' => $validated['type'],
                'service' => $validated['service'],
                'description' => $validated['description'] ?? null,
                'status' => AppoinmentStatus::Confirmed->value,
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        $appointment->load(['user', 'pet']);
        Mail::to($appointment->user->email)->send(new AppointmentCreatedMail($appointment));

        return redirect()
            ->route('appointments.show', $appointment)
            ->with('success', 'Cita agendada correctamente.');
    }

    public function show(Appointment $appointment)
    {
        Appointment::autoCompleteDueAppointments();
        $appointment->refresh();

        if ($appointment->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        return view('appointments.show', [
            'appointment' => $appointment->load(['pet'])
        ]);
    }

    public function edit(Appointment $appointment)
    {
        Appointment::autoCompleteDueAppointments();
        $appointment->refresh();

        if ($appointment->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        if (!in_array($appointment->status, Appointment::activeStatuses(), true)) {
            return redirect()->back()->with('error', 'Solo puedes editar citas activas.');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403, 'No autorizado');
        }

        return view('appointments.edit', [
            'appointment' => $appointment,
            'pets' => $user->pets()->get(),
        ]);
    }

    public function update(Request $request, Appointment $appointment)
    {
        Appointment::autoCompleteDueAppointments();
        $appointment->refresh();

        if ($appointment->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        if (!in_array($appointment->status, Appointment::activeStatuses(), true)) {
            return redirect()
                ->route('appointments.show', $appointment)
                ->with('error', 'Solo puedes editar citas activas.');
        }

        $validated = $request->validate([
            'appointment_date' => 'required|date|after:now',
            'type' => 'required|in:' . implode(',', Appointment::typeValues()),
            'service' => 'required|string',
            'description' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        if (!Appointment::isValidServiceForType($validated['type'], $validated['service'])) {
            throw ValidationException::withMessages([
                'service' => 'El servicio seleccionado no corresponde a la categoría elegida.',
            ]);
        }

        if (Appointment::hasScheduleConflict((int) $appointment->pet_id, $validated['appointment_date'], (int) $appointment->id)) {
            throw ValidationException::withMessages([
                'appointment_date' => 'Esta mascota ya tiene un turno agendado en ese mismo horario. Elegí otro día u hora.',
            ]);
        }

        $appointmentDate = Carbon::parse($validated['appointment_date'])->setSecond(0)->format('Y-m-d H:i:s');
        if (!Appointment::hasCapacityForDateTime($appointmentDate, (int) $appointment->id)) {
            throw ValidationException::withMessages([
                'appointment_date' => 'Ese horario ya no está disponible. Elegí otro turno.',
            ]);
        }

        $appointment->update([
            ...$validated,
            'appointment_date' => $appointmentDate,
            'status' => AppoinmentStatus::Confirmed->value,
        ]);

        return redirect()
            ->route('appointments.show', $appointment)
            ->with('success', 'Cita actualizada.');
    }

    public function cancel(Appointment $appointment)
    {
        Appointment::autoCompleteDueAppointments();
        $appointment->refresh();

        if ($appointment->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        if (in_array($appointment->status, [AppoinmentStatus::Completed->value, AppoinmentStatus::Cancelled->value], true)) {
            return redirect()
                ->route('appointments.show', $appointment)
                ->with('error', 'No puedes cancelar esta cita.');
        }

        $appointment->update(['status' => AppoinmentStatus::Cancelled->value]);

        return redirect()
            ->route('appointments.show', $appointment)
            ->with('success', 'Cita cancelada.');
    }
}

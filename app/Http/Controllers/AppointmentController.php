<?php

namespace App\Http\Controllers;

use App\Enums\AppoinmentStatus;
use App\Mail\AppointmentCreatedMail;
use App\Models\Appointment;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'appointment_date' => 'required|date|after:now',
            'type' => 'required|in:consultation,vaccination,surgery,grooming,other',
            'description' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        $pet = Pet::find($validated['pet_id']);
        if ($pet->user_id !== Auth::id()) {
            abort(403, 'Esta mascota no te pertenece');
        }

        $appointment = Appointment::create([
            'user_id' => Auth::id(),
            'pet_id' => $validated['pet_id'],
            'appointment_date' => $validated['appointment_date'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'status' => AppoinmentStatus::Pending->value,
            'notes' => $validated['notes'] ?? null,
        ]);

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

        if ($appointment->status !== AppoinmentStatus::Pending->value) {
            return redirect()->back()->with('error', 'Solo puedes editar citas pendientes');
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

        if ($appointment->status !== AppoinmentStatus::Pending->value) {
            return redirect()
                ->route('appointments.show', $appointment)
                ->with('error', 'Solo puedes editar citas pendientes.');
        }

        $validated = $request->validate([
            'appointment_date' => 'required|date|after:now',
            'type' => 'required|in:consultation,vaccination,surgery,grooming,other',
            'description' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        $appointment->update($validated);

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

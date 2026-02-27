<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AppointmentCreatedMail;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use App\Enums\PetStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    public function index()
    {
        Appointment::autoCompleteDueAppointments();

        $appointments = Appointment::query()
            ->with(['user', 'pet'])
            ->orderBy('appointment_date')
            ->paginate(15);

        return view('admin.appointments.index', [
            'appointments' => $appointments,
        ]);
    }

    public function create()
    {
        return view('admin.appointments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'pet_name' => 'required|string|max:255',
            'species' => 'required|string|max:120',
            'breed' => 'required|string|max:120',
            'age' => 'required|integer|min:0|max:60',
            'appointment_date' => 'required|date',
            'type' => 'required|in:consultation,vaccination,surgery,grooming,other',
            'description' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $user = User::firstOrCreate(
            ['email' => $validated['client_email']],
            [
                'name' => $validated['client_name'],
                'password' => Hash::make(Str::random(32)),
            ]
        );

        if ($user->name !== $validated['client_name']) {
            $user->name = $validated['client_name'];
            $user->save();
        }

        $pet = Pet::create([
            'user_id' => $user->id,
            'name' => $validated['pet_name'],
            'species' => $validated['species'],
            'breed' => $validated['breed'],
            'age' => (int) $validated['age'],
            'status' => PetStatus::Available,
        ]);

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'pet_id' => $pet->id,
            'appointment_date' => $validated['appointment_date'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $appointment->load(['user', 'pet']);
        Mail::to($appointment->user->email)->send(new AppointmentCreatedMail($appointment));

        return redirect()
            ->route('admin.appointments.index')
            ->with('success', 'Turno registrado por administración.');
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        Appointment::autoCompleteDueAppointments();
        $appointment->refresh();

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $appointment->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.appointments.index')
            ->with('success', 'Estado de turno actualizado.');
    }
}

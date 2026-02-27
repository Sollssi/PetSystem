<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppoinmentStatus;
use App\Http\Controllers\Controller;
use App\Mail\AppointmentCreatedMail;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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
            'service' => 'required|in:' . implode(',', Appointment::serviceValues()),
            'description' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
            'status' => 'nullable|in:' . implode(',', [
                AppoinmentStatus::Confirmed->value,
                AppoinmentStatus::Cancelled->value,
            ]),
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
            'status' => 'active',
        ]);

        $appointmentDate = Carbon::parse($validated['appointment_date'])->setSecond(0)->format('Y-m-d H:i:s');
        if (!Appointment::hasCapacityForDateTime($appointmentDate)) {
            $pet->delete();

            throw ValidationException::withMessages([
                'appointment_date' => 'Ese horario ya no está disponible. Elegí otro turno.',
            ]);
        }

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'pet_id' => $pet->id,
            'appointment_date' => $appointmentDate,
            'type' => $validated['type'],
            'service' => $validated['service'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? AppoinmentStatus::Confirmed->value,
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
            'status' => 'required|in:' . implode(',', [
                AppoinmentStatus::Confirmed->value,
                AppoinmentStatus::Cancelled->value,
            ]),
        ]);

        $appointment->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.appointments.index')
            ->with('success', 'Estado de turno actualizado.');
    }
}

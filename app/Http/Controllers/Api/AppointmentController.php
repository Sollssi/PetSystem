<?php

namespace App\Http\Controllers\Api;

use App\Enums\AppoinmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Pet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $appointments = $request->user()
            ->appointments()
            ->with('pet')
            ->orderBy('appointment_date')
            ->get();

        return response()->json(['data' => $appointments]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pet_id' => ['required', 'exists:pets,id'],
            'appointment_date' => ['required', 'date', 'after:now'],
            'type' => ['required', 'in:consultation,vaccination,surgery,grooming,other'],
            'description' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $pet = Pet::findOrFail($validated['pet_id']);
        if ($pet->user_id !== $request->user()->id) {
            abort(403, 'Esta mascota no te pertenece');
        }

        $appointment = Appointment::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'status' => AppoinmentStatus::Pending->value,
        ]);

        return response()->json([
            'message' => 'Cita creada',
            'data' => $appointment,
        ], 201);
    }

    public function show(Request $request, Appointment $appointment): JsonResponse
    {
        if ($appointment->user_id !== $request->user()->id) {
            abort(403, 'No autorizado');
        }

        return response()->json([
            'data' => $appointment->load('pet'),
        ]);
    }

    public function update(Request $request, Appointment $appointment): JsonResponse
    {
        if ($appointment->user_id !== $request->user()->id) {
            abort(403, 'No autorizado');
        }

        $validated = $request->validate([
            'appointment_date' => ['required', 'date', 'after:now'],
            'type' => ['required', 'in:consultation,vaccination,surgery,grooming,other'],
            'description' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:500'],
            'status' => ['sometimes', 'in:' . implode(',', AppoinmentStatus::values())],
        ]);

        $appointment->update($validated);

        return response()->json([
            'message' => 'Cita actualizada',
            'data' => $appointment,
        ]);
    }

    public function destroy(Request $request, Appointment $appointment): JsonResponse
    {
        if ($appointment->user_id !== $request->user()->id) {
            abort(403, 'No autorizado');
        }

        $appointment->delete();

        return response()->json([
            'message' => 'Cita eliminada',
        ]);
    }
}

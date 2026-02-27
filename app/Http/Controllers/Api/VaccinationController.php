<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\VaccinationRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VaccinationController extends Controller
{
    public function index(Request $request, Pet $pet): JsonResponse
    {
        if ($pet->user_id !== $request->user()->id) {
            abort(403, 'No autorizado');
        }

        $records = $pet->vaccinationRecords()
            ->orderByDesc('application_date')
            ->get();

        return response()->json(['data' => $records]);
    }

    public function store(Request $request, Pet $pet): JsonResponse
    {
        if ($pet->user_id !== $request->user()->id) {
            abort(403, 'No autorizado');
        }

        $validated = $request->validate([
            'vaccine_name' => ['required', 'string', 'max:200'],
            'application_date' => ['required', 'date', 'before_or_equal:today'],
            'next_due_date' => ['nullable', 'date', 'after:application_date'],
            'veterinarian' => ['nullable', 'string', 'max:200'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $record = VaccinationRecord::create([
            ...$validated,
            'pet_id' => $pet->id,
        ]);

        return response()->json([
            'message' => 'Registro de vacunación creado',
            'data' => $record,
        ], 201);
    }

    public function destroy(Request $request, Pet $pet, VaccinationRecord $record): JsonResponse
    {
        if ($pet->user_id !== $request->user()->id) {
            abort(403, 'No autorizado');
        }

        if ($record->pet_id !== $pet->id) {
            abort(404, 'Registro no encontrado para esta mascota');
        }

        $record->delete();

        return response()->json([
            'message' => 'Registro de vacunación eliminado',
        ]);
    }
}

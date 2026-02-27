<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $pets = $request->user()->pets()->latest()->get();

        return response()->json(['data' => $pets]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'species' => ['required', 'string', 'max:80'],
            'breed' => ['required', 'string', 'max:120'],
            'age' => ['required', 'integer', 'min:0', 'max:40'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        if (!array_key_exists('status', $validated) || $validated['status'] === null) {
            $validated['status'] = 'active';
        }

        $pet = $request->user()->pets()->create($validated);

        return response()->json([
            'message' => 'Mascota creada',
            'data' => $pet,
        ], 201);
    }

    public function show(Request $request, Pet $pet): JsonResponse
    {
        if ($pet->user_id !== $request->user()->id) {
            abort(403, 'No autorizado');
        }

        return response()->json(['data' => $pet]);
    }

    public function update(Request $request, Pet $pet): JsonResponse
    {
        if ($pet->user_id !== $request->user()->id) {
            abort(403, 'No autorizado');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'species' => ['required', 'string', 'max:80'],
            'breed' => ['required', 'string', 'max:120'],
            'age' => ['required', 'integer', 'min:0', 'max:40'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        if (!array_key_exists('status', $validated) || $validated['status'] === null) {
            $validated['status'] = $pet->status;
        }

        $pet->update($validated);

        return response()->json([
            'message' => 'Mascota actualizada',
            'data' => $pet,
        ]);
    }

    public function destroy(Request $request, Pet $pet): JsonResponse
    {
        if ($pet->user_id !== $request->user()->id) {
            abort(403, 'No autorizado');
        }

        $pet->delete();

        return response()->json([
            'message' => 'Mascota eliminada',
        ]);
    }
}

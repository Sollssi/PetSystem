<?php

namespace App\Http\Controllers;

use App\Models\VaccinationRecord;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VaccinationController extends Controller
{
    public function index(Pet $pet)
    {
        if ($pet->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $vaccinations = $pet->vaccinationRecords()
            ->orderBy('application_date', 'desc')
            ->get();

        $today = now()->startOfDay();
        $todayString = $today->toDateString();
        $limitString = $today->copy()->addDays(30)->toDateString();

        $summary = [
            'total' => $vaccinations->count(),
            'due_soon' => $vaccinations->filter(function ($record) use ($todayString, $limitString) {
                if (!$record->next_due_date) {
                    return false;
                }

                $nextDueDate = substr((string) $record->next_due_date, 0, 10);

                return $nextDueDate >= $todayString && $nextDueDate <= $limitString;
            })->count(),
            'expired' => $vaccinations->filter(function ($record) use ($todayString) {
                if (!$record->next_due_date) {
                    return false;
                }

                return substr((string) $record->next_due_date, 0, 10) < $todayString;
            })->count(),
        ];

        return view('vaccinations.index', [
            'pet' => $pet,
            'vaccinations' => $vaccinations,
            'summary' => $summary,
            'today' => $today,
        ]);
    }

    public function create(Pet $pet)
    {
        if ($pet->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        return view('vaccinations.create', [
            'pet' => $pet,
        ]);
    }

    public function store(Request $request, Pet $pet)
    {
        if ($pet->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $validated = $request->validate([
            'vaccine_name' => 'required|string|max:200',
            'application_date' => 'required|date|before_or_equal:today',
            'next_due_date' => 'nullable|date|after:application_date',
            'veterinarian' => 'nullable|string|max:200',
            'notes' => 'nullable|string|max:500',
        ]);

        $record = VaccinationRecord::create([
            'pet_id' => $pet->id,
            'vaccine_name' => $validated['vaccine_name'],
            'application_date' => $validated['application_date'],
            'next_due_date' => $validated['next_due_date'] ?? null,
            'veterinarian' => $validated['veterinarian'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('vaccinations.index', $pet)
            ->with('success', 'Registro de vacunación agregado.');
    }

    public function delete(Pet $pet, VaccinationRecord $record)
    {
        if ($record->pet_id !== $pet->id) {
            abort(404, 'Registro de vacunación no corresponde a la mascota');
        }

        if ($pet->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $record->delete();

        return redirect()
            ->route('vaccinations.index', $pet)
            ->with('success', 'Registro eliminado.');
    }

    public function downloadCertificate(Pet $pet, VaccinationRecord $record)
    {
        if ($pet->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        if ($record->pet_id !== $pet->id) {
            abort(404, 'Registro no encontrado para esta mascota');
        }

        // Aquí se podría generar un PDF con el carnet de vacunación
        // Por ahora retornamos JSON con los datos del registro

        return response()->json([
            'pet' => $pet,
            'vaccination' => $record,
        ]);
    }
}

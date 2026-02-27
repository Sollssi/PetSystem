<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePetRequest;
use App\Http\Requests\UpdatePetRequest;
use App\Models\Pet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PetController extends Controller
{
    public function index(Request $request): View
    {
        $pets = $request->user()
            ->pets()
            ->latest()
            ->get();

        return view('pets.index', compact('pets'));
    }

    public function create(): View
    {
        return view('pets.create');
    }

    public function store(StorePetRequest $request): RedirectResponse
    {
        $petData = $request->validated();
        $petData['status'] = 'active';

        $pet = $request->user()->pets()->create($petData);

        return redirect()
            ->route('pets.show', $pet)
            ->with('status', 'Mascota creada exitosamente.');
    }

    public function show(Pet $pet): View
    {
        if ($pet->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $pet->load([]);

        return view('pets.show', compact('pet'));
    }

    public function edit(Pet $pet): View
    {
        if ($pet->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        return view('pets.edit', compact('pet'));
    }

    public function update(UpdatePetRequest $request, Pet $pet): RedirectResponse
    {
        if ($pet->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $petData = $request->validated();
        unset($petData['status']);

        $pet->update($petData);

        return redirect()
            ->route('pets.show', $pet)
            ->with('status', 'Mascota actualizada correctamente.');
    }

    public function destroy(Pet $pet): RedirectResponse
    {
        if ($pet->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $pet->delete();

        return redirect()
            ->route('pets.index')
            ->with('status', 'Mascota eliminada.');
    }
}

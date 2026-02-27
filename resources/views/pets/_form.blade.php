@php
    $petName = isset($pet) ? $pet->name : '';
    $petSpecies = isset($pet) ? $pet->species : '';
    $petBreed = isset($pet) ? $pet->breed : '';
    $petAge = isset($pet) ? $pet->age : '';
    $petDescription = isset($pet) ? $pet->description : '';
@endphp

<div class="grid gap-4">
    <div>
        <label class="block text-sm text-slate-600" for="name">Nombre</label>
        <input class="w-full rounded-lg border border-slate-300 px-3 py-2" type="text" name="name" id="name" value="{{ old('name', $petName) }}" required>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm text-slate-600" for="species">Especie</label>
            <input class="w-full rounded-lg border border-slate-300 px-3 py-2" type="text" name="species" id="species" value="{{ old('species', $petSpecies) }}" required>
        </div>
        <div>
            <label class="block text-sm text-slate-600" for="breed">Raza</label>
            <input class="w-full rounded-lg border border-slate-300 px-3 py-2" type="text" name="breed" id="breed" value="{{ old('breed', $petBreed) }}" required>
        </div>
    </div>
    <div>
        <label class="block text-sm text-slate-600" for="age">Edad</label>
        <input class="w-full rounded-lg border border-slate-300 px-3 py-2" type="number" name="age" id="age" value="{{ old('age', $petAge) }}" min="0" max="40" required>
    </div>
    <div>
        <label class="block text-sm text-slate-600" for="description">Descripción</label>
        <textarea class="w-full rounded-lg border border-slate-300 px-3 py-2" name="description" id="description" rows="4">{{ old('description', $petDescription) }}</textarea>
    </div>
    <div class="flex items-center gap-3">
        <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white" type="submit">Guardar</button>
        <a class="text-sm text-slate-600" href="{{ route('pets.index') }}">Cancelar</a>
    </div>
</div>

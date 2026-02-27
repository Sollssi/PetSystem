@extends('layouts.app')

@section('content')
    <div class="max-w-2xl">
        <h2 class="text-2xl font-semibold mb-4">Nueva cita</h2>
        <form method="POST" action="{{ route('appointments.store') }}" class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-slate-600 mb-1" for="pet_id">Mascota</label>
                <select name="pet_id" id="pet_id" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                    <option value="">Selecciona una mascota</option>
                    @foreach($pets as $pet)
                        <option value="{{ $pet->id }}" @selected(old('pet_id') == $pet->id)>{{ $pet->name }} ({{ $pet->species }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1" for="type">Tipo</label>
                <select name="type" id="type" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                    <option value="consultation" @selected(old('type') === 'consultation')>Consulta</option>
                    <option value="vaccination" @selected(old('type') === 'vaccination')>Vacunación</option>
                    <option value="surgery" @selected(old('type') === 'surgery')>Cirugía</option>
                    <option value="grooming" @selected(old('type') === 'grooming')>Estética</option>
                    <option value="other" @selected(old('type') === 'other')>Otro</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1" for="appointment_date">Fecha y hora</label>
                <input type="datetime-local" name="appointment_date" id="appointment_date" class="w-full rounded-lg border border-slate-300 px-3 py-2" value="{{ old('appointment_date') }}" min="{{ now()->format('Y-m-d\TH:i') }}" required>
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1" for="description">Descripción</label>
                <textarea name="description" id="description" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1" for="notes">Notas</label>
                <textarea name="notes" id="notes" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('notes') }}</textarea>
            </div>
            <div class="flex items-center gap-2">
                <button class="rounded-lg bg-slate-900 text-white px-4 py-2 text-sm font-semibold">Guardar</button>
                <a href="{{ route('appointments.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold">Cancelar</a>
            </div>
        </form>
    </div>
@endsection

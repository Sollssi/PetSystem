@extends('layouts.app')

@section('content')
    <div class="max-w-2xl">
        <h2 class="text-2xl font-semibold mb-1">Nueva entrada en carnet · {{ $pet->name }}</h2>
        <p class="text-sm text-slate-600 mb-4">Guarda la vacuna aplicada y la fecha en la que debe renovarse.</p>
        <form method="POST" action="{{ route('vaccinations.store', $pet) }}" class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-slate-600 mb-1" for="vaccine_name">Vacuna</label>
                <input type="text" name="vaccine_name" id="vaccine_name" value="{{ old('vaccine_name') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1" for="application_date">Fecha de aplicación</label>
                <input type="date" name="application_date" id="application_date" value="{{ old('application_date') }}" max="{{ now()->format('Y-m-d') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1" for="next_due_date">Fecha de renovación / próxima dosis</label>
                <input type="date" name="next_due_date" id="next_due_date" value="{{ old('next_due_date') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1" for="veterinarian">Veterinario</label>
                <input type="text" name="veterinarian" id="veterinarian" value="{{ old('veterinarian') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1" for="notes">Notas</label>
                <textarea name="notes" id="notes" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('notes') }}</textarea>
            </div>
            <div class="flex items-center gap-2">
                <button class="rounded-lg bg-slate-900 text-white px-4 py-2 text-sm font-semibold">Guardar</button>
                <a href="{{ route('vaccinations.index', $pet) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold">Volver</a>
            </div>
        </form>
    </div>
@endsection

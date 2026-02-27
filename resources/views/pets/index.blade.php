@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold">Mascotas registradas</h2>
            <p class="text-sm text-slate-600">Gestiona los datos de tus mascotas.</p>
        </div>
        <a class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white" href="{{ route('pets.create') }}">Nueva mascota</a>
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($pets as $pet)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">{{ $pet->name }}</h3>
                </div>
                <p class="mt-2 text-sm text-slate-600">{{ $pet->species }} · {{ $pet->breed }}</p>
                <p class="text-sm text-slate-600">Edad: {{ $pet->age }} años</p>
                <p class="mt-3 text-sm text-slate-700">{{ $pet->description ?? 'Sin descripción.' }}</p>
                <div class="mt-4 flex items-center justify-between text-sm">
                    <span class="text-slate-500">&nbsp;</span>
                    <a class="text-slate-900 font-semibold" href="{{ route('pets.show', $pet) }}">Ver ficha</a>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
                Aun no hay mascotas registradas.
            </div>
        @endforelse
    </div>
@endsection

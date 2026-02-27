@extends('layouts.app')

@section('content')
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm uppercase tracking-wide text-slate-500">Ficha completa</p>
                <h2 class="text-2xl font-semibold">{{ $pet->name }}</h2>
            </div>
            <div class="flex items-center gap-2">
                <a class="rounded-lg border border-slate-300 px-4 py-2 text-sm" href="{{ route('pets.edit', $pet) }}">Editar</a>
                <form method="POST" action="{{ route('pets.destroy', $pet) }}">
                    @csrf
                    @method('DELETE')
                    <button class="rounded-lg bg-rose-600 px-4 py-2 text-sm text-white" type="submit">Eliminar</button>
                </form>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-1">
            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <h3 class="text-lg font-semibold mb-4">Detalles</h3>
                <dl class="grid gap-4 text-sm text-slate-600">
                    <div>
                        <dt class="uppercase text-xs text-slate-400">Especie</dt>
                        <dd class="text-slate-900">{{ $pet->species }}</dd>
                    </div>
                    <div>
                        <dt class="uppercase text-xs text-slate-400">Raza</dt>
                        <dd class="text-slate-900">{{ $pet->breed }}</dd>
                    </div>
                    <div>
                        <dt class="uppercase text-xs text-slate-400">Edad</dt>
                        <dd class="text-slate-900">{{ $pet->age }} años</dd>
                    </div>
                    <div>
                        <dt class="uppercase text-xs text-slate-400">Descripción</dt>
                        <dd class="text-slate-900">{{ $pet->description ?? 'Sin descripción.' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
@endsection

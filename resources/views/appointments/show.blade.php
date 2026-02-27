@extends('layouts.app')

@section('content')
    <div class="max-w-3xl">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-semibold">Detalle de cita</h2>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ ucfirst($appointment->status) }}</span>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
            <div>
                <p class="text-sm text-slate-500">Mascota</p>
                <p class="font-semibold">{{ $appointment->pet->name }} · {{ $appointment->pet->species }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Fecha y hora</p>
                <p class="font-semibold">{{ $appointment->appointment_date->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Tipo</p>
                <p class="font-semibold">{{ ucfirst($appointment->type) }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Descripción</p>
                <p>{{ $appointment->description ?: 'Sin descripción' }}</p>
            </div>
            @if($appointment->notes)
                <div>
                    <p class="text-sm text-slate-500">Notas</p>
                    <p>{{ $appointment->notes }}</p>
                </div>
            @endif

            <div class="flex items-center gap-2 pt-2">
                @if($appointment->status === 'pending')
                    <a href="{{ route('appointments.edit', $appointment) }}" class="rounded-lg bg-slate-900 text-white px-4 py-2 text-sm font-semibold">Editar</a>
                    <form method="POST" action="{{ route('appointments.cancel', $appointment) }}">
                        @csrf
                        @method('PATCH')
                        <button class="rounded-lg border border-rose-300 text-rose-700 px-4 py-2 text-sm font-semibold" onclick="return confirm('¿Cancelar esta cita?')">Cancelar</button>
                    </form>
                @endif
                <a href="{{ route('appointments.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold">Volver</a>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold">Mis citas</h2>
            <p class="text-sm text-slate-600">Controla consultas, exámenes, laboratorio, peluquería y sus estados.</p>
        </div>
        <a href="{{ route('appointments.create') }}" class="rounded-lg bg-slate-900 text-white px-4 py-2 text-sm font-semibold">Nueva cita</a>
    </div>

    @if($appointments->isNotEmpty())
        <div class="space-y-3">
            @foreach($appointments as $appointment)
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $appointment->pet->name }}</p>
                            <p class="text-sm text-slate-600">{{ $appointment->appointment_date->format('d/m/Y H:i') }} · {{ $appointment->typeLabel() }} · {{ $appointment->serviceLabel() }}</p>
                            <p class="text-sm text-slate-700 mt-1">{{ $appointment->description ?: 'Sin descripción' }}</p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ ucfirst($appointment->status) }}</span>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <a href="{{ route('appointments.show', $appointment) }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-semibold">Ver</a>
                        @if($appointment->status === 'pending')
                            <form method="POST" action="{{ route('appointments.cancel', $appointment) }}">
                                @csrf
                                @method('PATCH')
                                <button class="rounded-lg border border-rose-300 text-rose-700 px-3 py-1.5 text-sm font-semibold" onclick="return confirm('¿Cancelar esta cita?')">Cancelar</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $appointments->links() }}</div>
    @else
        <div class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
            Aún no tienes citas registradas.
        </div>
    @endif
@endsection

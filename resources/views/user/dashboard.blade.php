@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-slate-900">Panel principal</h2>
        <p class="text-slate-600">
            Hola {{ $user->name }},
            @role('admin')
                este es el resumen general del sistema.
            @else
                este es el resumen de tu cuenta.
            @endrole
        </p>
    </div>

    @role('admin')
        <div class="grid gap-4 md:grid-cols-2 mb-8">
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <p class="text-sm text-slate-500">Citas totales</p>
                <p class="text-3xl font-bold mt-1">{{ $stats['appointments'] }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <p class="text-sm text-slate-500">Pendientes por aceptar</p>
                <p class="text-3xl font-bold mt-1">{{ $stats['pending_appointments'] }}</p>
            </div>
        </div>
    @endrole

    @role('admin')
        <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-6 mb-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-indigo-900">Solicitudes de turnos (Admin)</h3>
                    <p class="text-sm text-indigo-800 mt-1">Tienes <span class="font-bold">{{ $adminPendingAppointments }}</span> solicitudes pendientes de revisión.</p>
                </div>
                <a href="{{ route('admin.appointments.index') }}" class="rounded-lg bg-indigo-900 text-white px-4 py-2 text-sm font-semibold">Ver solicitudes</a>
            </div>
        </div>
    @endrole

    @unlessrole('admin')
        <div class="rounded-xl border border-slate-200 bg-white p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Carnet de vacunación</h3>
                <a href="{{ route('pets.index') }}" class="text-sm font-semibold text-slate-700">Gestionar mascotas</a>
            </div>
            @if($petsForCarnet->isNotEmpty())
                <div class="space-y-3">
                    @foreach($petsForCarnet as $pet)
                        <div class="rounded-lg border border-slate-200 p-4 flex items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $pet->name }}</p>
                                <p class="text-sm text-slate-600">Entradas en carnet: {{ $pet->vaccination_records_count }}</p>
                            </div>
                            <a href="{{ route('vaccinations.index', $pet) }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-semibold">Ver carnet</a>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-slate-500">Aún no tienes mascotas registradas para crear su carnet.</p>
            @endif
        </div>

        <div class="rounded-xl border border-amber-200 bg-amber-50 p-6 mb-6">
            <h3 class="text-lg font-semibold text-amber-900">Recordatorios de vacunación próximos</h3>
            @if($upcomingVaccinations->isNotEmpty())
                <div class="mt-3 space-y-2">
                    @foreach($upcomingVaccinations as $record)
                        <div class="rounded-lg border border-amber-200 bg-white px-4 py-3 text-sm text-slate-700">
                            <span class="font-semibold">{{ $record->pet->name }}</span>
                            · {{ $record->vaccine_name }}
                            · vence {{ $record->next_due_date->format('d/m/Y') }}
                        </div>
                    @endforeach
                </div>
            @else
                <p class="mt-2 text-sm text-amber-800">No hay vacunas próximas para los próximos 7 días.</p>
            @endif
        </div>
    @endunlessrole

    <div class="rounded-xl border border-slate-200 bg-white p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">
                @role('admin')
                    Turnos del sistema
                @else
                    Próximas citas
                @endrole
            </h3>
            @role('admin')
                <a href="{{ route('admin.appointments.create') }}" class="rounded-lg bg-slate-900 text-white px-3 py-2 text-sm font-semibold">Nuevo turno (admin)</a>
            @else
                <a href="{{ route('appointments.create') }}" class="rounded-lg bg-slate-900 text-white px-3 py-2 text-sm font-semibold">Nueva cita</a>
            @endrole
        </div>

        @if($appointments->isNotEmpty())
            <div class="space-y-3">
                @foreach($appointments as $appointment)
                    <div class="rounded-lg border border-slate-200 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $appointment->pet->name }}</p>
                                <p class="text-sm text-slate-600">{{ $appointment->appointment_date->format('d/m/Y H:i') }} · {{ ucfirst($appointment->type) }}</p>
                                @role('admin')
                                    <p class="text-xs text-slate-500">Cliente: {{ $appointment->user->name }} · {{ $appointment->user->email }}</p>
                                @endrole
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ ucfirst($appointment->status) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('appointments.index') }}" class="inline-block mt-4 text-sm font-semibold text-slate-700">Ver todas →</a>
        @else
            <p class="text-slate-500">
                @role('admin')
                    No hay turnos cargados todavía.
                @else
                    No tienes citas registradas todavía.
                @endrole
            </p>
        @endif
    </div>
@endsection

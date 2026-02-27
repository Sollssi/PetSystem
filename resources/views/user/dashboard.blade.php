@extends('layouts.app')

@section('content')
    <div class="mt-2 mb-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-xs uppercase tracking-wide text-slate-500">VetClinic</p>
        <h2 class="text-3xl font-bold text-slate-900 mt-1">Panel principal</h2>
        <p class="text-slate-600 mt-2">
            Hola <span class="font-semibold text-slate-900">{{ $user->name }}</span>,
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
                <p class="text-sm text-slate-500">Turnos confirmados</p>
                <p class="text-3xl font-bold mt-1">{{ $stats['confirmed_appointments'] }}</p>
            </div>
        </div>
    @endrole

    @unlessrole('admin')
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-6 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-emerald-900">¿Necesitas un turno?</h3>
                    <p class="text-sm text-emerald-800 mt-1">Agendá una cita para consulta, laboratorio, diagnóstico por imágenes, cardiología, peluquería y más.</p>
                </div>
                <a href="{{ route('appointments.create') }}" class="rounded-lg bg-emerald-700 text-white px-4 py-2 text-sm font-semibold">Agendar cita</a>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Próximos turnos</h3>
                <a href="{{ route('appointments.index') }}" class="text-sm font-semibold text-slate-700">Ver agenda</a>
            </div>

            @if($appointments->isNotEmpty())
                <div class="overflow-x-auto rounded-lg border border-slate-200">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="text-left px-4 py-3">Turno</th>
                                <th class="text-left px-4 py-3">Servicio</th>
                                <th class="text-left px-4 py-3">Día y hora</th>
                                <th class="text-left px-4 py-3">Mascota</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($appointments as $appointment)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ $appointment->typeLabel() }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $appointment->serviceLabel() }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $appointment->appointment_date->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $appointment->pet->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-slate-500">No tienes turnos próximos agendados.</p>
            @endif
        </div>

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

    @role('admin')
        <div class="rounded-xl border border-slate-200 bg-white p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Turnos del sistema</h3>
                <a href="{{ route('admin.appointments.create') }}" class="rounded-lg bg-slate-900 text-white px-3 py-2 text-sm font-semibold">Nuevo turno (admin)</a>
            </div>

            @if($appointments->isNotEmpty())
                <div class="space-y-3">
                    @foreach($appointments as $appointment)
                        <div class="rounded-lg border border-slate-200 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $appointment->pet->name }}</p>
                                    <p class="text-sm text-slate-600">{{ $appointment->appointment_date->format('d/m/Y H:i') }} · {{ $appointment->typeLabel() }} · {{ $appointment->serviceLabel() }}</p>
                                    <p class="text-xs text-slate-500">Cliente: {{ $appointment->user->name }} · {{ $appointment->user->email }}</p>
                                </div>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ ucfirst($appointment->status) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('admin.appointments.index') }}" class="inline-block mt-4 text-sm font-semibold text-slate-700">Ver todos →</a>
            @else
                <p class="text-slate-500">No hay turnos cargados todavía.</p>
            @endif
        </div>
    @endrole
@endsection

@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold">Turnos · Administración</h2>
            <p class="text-sm text-slate-600">Gestiona estados y aceptación de turnos.</p>
        </div>
        <a href="{{ route('admin.appointments.create') }}" class="rounded-lg bg-slate-900 text-white px-4 py-2 text-sm font-semibold">Nuevo turno (admin)</a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="text-left px-4 py-3">Cliente</th>
                    <th class="text-left px-4 py-3">Mascota</th>
                    <th class="text-left px-4 py-3">Fecha</th>
                    <th class="text-left px-4 py-3">Tipo</th>
                    <th class="text-left px-4 py-3">Estado</th>
                    <th class="text-left px-4 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($appointments as $appointment)
                    <tr>
                        <td class="px-4 py-3">{{ $appointment->user->name }}<br><span class="text-xs text-slate-500">{{ $appointment->user->email }}</span></td>
                        <td class="px-4 py-3">{{ $appointment->pet->name }}</td>
                        <td class="px-4 py-3">{{ $appointment->appointment_date->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ ucfirst($appointment->type) }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold
                                @if($appointment->status === 'pending') bg-amber-100 text-amber-800
                                @elseif($appointment->status === 'confirmed') bg-emerald-100 text-emerald-800
                                @elseif($appointment->status === 'completed') bg-blue-100 text-blue-800
                                @else bg-rose-100 text-rose-800 @endif">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($appointment->status === 'completed')
                                <span class="text-xs font-semibold text-slate-500">-</span>
                            @elseif($appointment->status === 'cancelled')
                                <span class="text-xs font-semibold text-slate-500">-</span>
                            @else
                                <div class="flex flex-wrap gap-2">
                                    <form method="POST" action="{{ route('admin.appointments.status', $appointment) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="confirmed">
                                        <button class="rounded-lg border border-emerald-300 px-3 py-1.5 text-xs font-semibold text-emerald-700">Aceptar</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.appointments.status', $appointment) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button class="rounded-lg border border-rose-300 px-3 py-1.5 text-xs font-semibold text-rose-700">Cancelar</button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-500">No hay turnos cargados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $appointments->links() }}</div>
@endsection

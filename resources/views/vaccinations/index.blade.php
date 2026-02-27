@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold">Carnet de vacunación · {{ $pet->name }}</h2>
            <p class="text-sm text-slate-600">Registro del usuario con fechas de aplicación y renovación.</p>
        </div>
        <a href="{{ route('vaccinations.create', $pet) }}" class="rounded-lg bg-slate-900 text-white px-4 py-2 text-sm font-semibold">Nueva entrada</a>
    </div>

    <div class="grid gap-4 md:grid-cols-3 mb-6">
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-xs uppercase tracking-wide text-slate-500">Dosis registradas</p>
            <p class="text-3xl font-bold mt-1">{{ $summary['total'] }}</p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
            <p class="text-xs uppercase tracking-wide text-amber-700">Próximas a renovar (30 días)</p>
            <p class="text-3xl font-bold mt-1 text-amber-800">{{ $summary['due_soon'] }}</p>
        </div>
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4">
            <p class="text-xs uppercase tracking-wide text-rose-700">Vencidas</p>
            <p class="text-3xl font-bold mt-1 text-rose-800">{{ $summary['expired'] }}</p>
        </div>
    </div>

    @if($vaccinations->isNotEmpty())
        <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-left">Vacuna</th>
                        <th class="px-4 py-3 text-left">Aplicada</th>
                        <th class="px-4 py-3 text-left">Renovar</th>
                        <th class="px-4 py-3 text-left">Estado</th>
                        <th class="px-4 py-3 text-left">Veterinario</th>
                        <th class="px-4 py-3 text-left">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($vaccinations as $vaccination)
                        @php
                            $statusLabel = 'Sin fecha de renovación';
                            $statusClass = 'bg-slate-100 text-slate-700';

                            if ($vaccination->next_due_date) {
                                if ($vaccination->next_due_date->lt($today)) {
                                    $statusLabel = 'Vencida';
                                    $statusClass = 'bg-rose-100 text-rose-700';
                                } elseif ($vaccination->next_due_date->eq($today)) {
                                    $statusLabel = 'Vence hoy';
                                    $statusClass = 'bg-amber-100 text-amber-800';
                                } elseif ($vaccination->next_due_date->lte($today->copy()->addDays(30))) {
                                    $statusLabel = 'Próxima a vencer';
                                    $statusClass = 'bg-amber-100 text-amber-800';
                                } else {
                                    $statusLabel = 'Vigente';
                                    $statusClass = 'bg-emerald-100 text-emerald-700';
                                }
                            }
                        @endphp
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $vaccination->vaccine_name }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $vaccination->application_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $vaccination->next_due_date ? $vaccination->next_due_date->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $vaccination->veterinarian ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('vaccinations.delete', [$pet, $vaccination]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg border border-rose-300 text-rose-700 px-3 py-1.5 text-xs font-semibold" onclick="return confirm('¿Eliminar entrada del carnet?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
            Este carnet todavía no tiene entradas. Registra la primera vacuna para comenzar.
        </div>
    @endif
@endsection

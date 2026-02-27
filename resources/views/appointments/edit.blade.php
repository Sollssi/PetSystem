@extends('layouts.app')

@section('content')
    <div class="max-w-2xl">
        <h2 class="text-2xl font-semibold mb-4">Editar cita</h2>
        <form method="POST" action="{{ route('appointments.update', $appointment) }}" class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm text-slate-600 mb-1" for="type">Categoría</label>
                <select name="type" id="type" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                    <option value="consultation" @selected(old('type', $appointment->type) === 'consultation')>Consulta</option>
                    <option value="vaccination" @selected(old('type', $appointment->type) === 'vaccination')>Vacunación</option>
                    <option value="surgery" @selected(old('type', $appointment->type) === 'surgery')>Cirugía</option>
                    <option value="grooming" @selected(old('type', $appointment->type) === 'grooming')>Peluquería</option>
                    <option value="other" @selected(old('type', $appointment->type) === 'other')>Otro</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1" for="service">Servicio</label>
                <select name="service" id="service" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                    @foreach(\App\Models\Appointment::serviceOptions() as $value => $label)
                        <option value="{{ $value }}" @selected(old('service', $appointment->service) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1" for="appointment_date">Fecha y hora</label>
                <input type="datetime-local" name="appointment_date" id="appointment_date" class="w-full rounded-lg border border-slate-300 px-3 py-2" value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d\\TH:i')) }}" min="{{ now()->format('Y-m-d\\TH:i') }}" required>
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1" for="description">Descripción</label>
                <textarea name="description" id="description" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('description', $appointment->description) }}</textarea>
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1" for="notes">Notas</label>
                <textarea name="notes" id="notes" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('notes', $appointment->notes) }}</textarea>
            </div>
            <div class="flex items-center gap-2">
                <button class="rounded-lg bg-slate-900 text-white px-4 py-2 text-sm font-semibold">Guardar cambios</button>
                <a href="{{ route('appointments.show', $appointment) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold">Volver</a>
            </div>
        </form>
    </div>
@endsection

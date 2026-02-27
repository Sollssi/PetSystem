@extends('layouts.app')

@section('content')
    <div class="max-w-3xl">
        <h2 class="text-2xl font-semibold mb-4">Nuevo turno (Administración)</h2>
        <form method="POST" action="{{ route('admin.appointments.store') }}" class="rounded-xl border border-slate-200 bg-white p-6 space-y-5">
            @csrf

            <div class="rounded-lg border border-slate-200 p-4 space-y-3">
                <h3 class="font-semibold">Carga manual (cliente y mascota)</h3>
                <div class="grid md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm text-slate-600 mb-1" for="client_name">Nombre cliente</label>
                        <input type="text" name="client_name" id="client_name" value="{{ old('client_name') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm text-slate-600 mb-1" for="client_email">Email cliente</label>
                        <input type="email" name="client_email" id="client_email" value="{{ old('client_email') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm text-slate-600 mb-1" for="pet_name">Nombre mascota</label>
                        <input type="text" name="pet_name" id="pet_name" value="{{ old('pet_name') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm text-slate-600 mb-1" for="species">Especie</label>
                        <input type="text" name="species" id="species" value="{{ old('species') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="Perro, gato..." required>
                    </div>
                    <div>
                        <label class="block text-sm text-slate-600 mb-1" for="breed">Raza</label>
                        <input type="text" name="breed" id="breed" value="{{ old('breed') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm text-slate-600 mb-1" for="age">Edad</label>
                        <input type="number" name="age" id="age" value="{{ old('age') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" min="0" max="60" required>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm text-slate-600 mb-1" for="type">Categoría</label>
                    <select name="type" id="type" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                        <option value="consultation" @selected(old('type') === 'consultation')>Consulta</option>
                        <option value="vaccination" @selected(old('type') === 'vaccination')>Vacunación</option>
                        <option value="surgery" @selected(old('type') === 'surgery')>Cirugía</option>
                        <option value="grooming" @selected(old('type') === 'grooming')>Peluquería</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-slate-600 mb-1" for="service">Servicio</label>
                    <select name="service" id="service" class="w-full rounded-lg border border-slate-300 px-3 py-2" required></select>
                </div>
                <div>
                    <label class="block text-sm text-slate-600 mb-1" for="status">Estado inicial</label>
                    <select name="status" id="status" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                        <option value="confirmed" @selected(old('status', 'confirmed') === 'confirmed')>Confirmado</option>
                        <option value="cancelled" @selected(old('status') === 'cancelled')>Cancelado</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm text-slate-600 mb-1" for="appointment_date">Fecha y hora</label>
                <input type="datetime-local" name="appointment_date" id="appointment_date" value="{{ old('appointment_date') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm text-slate-600 mb-1" for="description">Descripción</label>
                <textarea name="description" id="description" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm text-slate-600 mb-1" for="notes">Notas internas</label>
                <textarea name="notes" id="notes" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center gap-2">
                <button class="rounded-lg bg-slate-900 text-white px-4 py-2 text-sm font-semibold">Guardar turno</button>
                <a href="{{ route('admin.appointments.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        const typeSelect = document.getElementById('type');
        const serviceSelect = document.getElementById('service');
        const servicesByType = @json(\App\Models\Appointment::serviceOptionsByType());
        const oldService = "{{ old('service', 'general_consultation') }}";

        const fillServiceOptions = () => {
            const selectedType = typeSelect.value;
            const options = servicesByType[selectedType] || {};
            const entries = Object.entries(options);

            serviceSelect.innerHTML = '';
            entries.forEach(([value, label]) => {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = label;
                serviceSelect.appendChild(option);
            });

            if (entries.some(([value]) => value === oldService)) {
                serviceSelect.value = oldService;
            } else if (entries.length) {
                serviceSelect.value = entries[0][0];
            }
        };

        typeSelect.addEventListener('change', fillServiceOptions);
        fillServiceOptions();
    </script>
@endsection

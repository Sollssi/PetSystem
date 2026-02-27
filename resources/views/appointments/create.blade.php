@extends('layouts.app')

@section('content')
    <div class="max-w-2xl">
        <h2 class="text-2xl font-semibold mb-4">Nueva cita</h2>
        <form method="POST" action="{{ route('appointments.store') }}" class="rounded-xl border border-slate-200 bg-white p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm text-slate-600 mb-1" for="pet_id">Mascota</label>
                <select name="pet_id" id="pet_id" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                    <option value="">Selecciona una mascota</option>
                    @foreach($pets as $pet)
                        <option value="{{ $pet->id }}" @selected(old('pet_id') == $pet->id)>{{ $pet->name }} ({{ $pet->species }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1" for="type">Categoría</label>
                <select name="type" id="type" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                    <option value="consultation" @selected(old('type') === 'consultation')>Consulta</option>
                    <option value="vaccination" @selected(old('type') === 'vaccination')>Vacunación</option>
                    <option value="surgery" @selected(old('type') === 'surgery')>Cirugía</option>
                    <option value="grooming" @selected(old('type') === 'grooming')>Peluquería</option>
                    <option value="other" @selected(old('type') === 'other')>Otro</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1" for="service">Servicio</label>
                <select name="service" id="service" class="w-full rounded-lg border border-slate-300 px-3 py-2" required>
                    @foreach(\App\Models\Appointment::serviceOptions() as $value => $label)
                        <option value="{{ $value }}" @selected(old('service', 'general_consultation') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1" for="appointment_day">Día</label>
                <input type="date" id="appointment_day" class="w-full rounded-lg border border-slate-300 px-3 py-2" min="{{ now()->toDateString() }}" value="{{ old('appointment_date') ? \Carbon\Carbon::parse(old('appointment_date'))->toDateString() : now()->toDateString() }}" required>
                <p class="mt-2 text-xs text-slate-500">Agenda disponible de {{ $schedulingRules['workday_start'] }} a {{ $schedulingRules['workday_end'] }} · turnos cada {{ $schedulingRules['slot_minutes'] }} min.</p>
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-2">Horario disponible</label>
                <div id="slots-wrapper" class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                    <p class="text-sm text-slate-500">Selecciona un día para ver horarios.</p>
                </div>
                <input type="hidden" name="appointment_date" id="appointment_date" value="{{ old('appointment_date') }}" required>
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1" for="description">Descripción</label>
                <textarea name="description" id="description" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1" for="notes">Notas</label>
                <textarea name="notes" id="notes" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('notes') }}</textarea>
            </div>
            <div class="flex items-center gap-2">
                <button class="rounded-lg bg-slate-900 text-white px-4 py-2 text-sm font-semibold">Guardar</button>
                <a href="{{ route('appointments.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        const dayInput = document.getElementById('appointment_day');
        const hiddenDateInput = document.getElementById('appointment_date');
        const slotsWrapper = document.getElementById('slots-wrapper');
        const availabilityUrl = "{{ route('appointments.availability') }}";
        const preselectedDateTime = "{{ old('appointment_date') }}";

        const renderSlots = (data) => {
            const slots = data?.slots ?? [];

            if (!data?.daily_available) {
                slotsWrapper.innerHTML = '<p class="text-sm text-rose-700">Se alcanzó el cupo diario. Elige otro día.</p>';
                hiddenDateInput.value = '';
                return;
            }

            const availableSlots = slots.filter(slot => slot.available);
            if (!availableSlots.length) {
                slotsWrapper.innerHTML = '<p class="text-sm text-slate-500">No hay horarios disponibles para ese día.</p>';
                hiddenDateInput.value = '';
                return;
            }

            const selectedValue = hiddenDateInput.value || preselectedDateTime;
            const buttons = availableSlots.map(slot => {
                const isSelected = selectedValue === slot.value;
                return `
                    <button
                        type="button"
                        data-slot-value="${slot.value}"
                        class="slot-btn rounded-lg border px-3 py-2 text-sm font-semibold ${isSelected ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-300 hover:border-slate-500'}"
                    >
                        ${slot.time}
                    </button>
                `;
            }).join('');

            slotsWrapper.innerHTML = `
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2">${buttons}</div>
                <p class="mt-3 text-xs text-slate-500">Turnos ocupados del día: ${data.daily_booked}/${data.daily_limit}</p>
            `;

            slotsWrapper.querySelectorAll('.slot-btn').forEach(button => {
                button.addEventListener('click', () => {
                    hiddenDateInput.value = button.dataset.slotValue;
                    slotsWrapper.querySelectorAll('.slot-btn').forEach(item => {
                        item.classList.remove('bg-slate-900', 'text-white', 'border-slate-900');
                        item.classList.add('bg-white', 'text-slate-700', 'border-slate-300');
                    });
                    button.classList.remove('bg-white', 'text-slate-700', 'border-slate-300');
                    button.classList.add('bg-slate-900', 'text-white', 'border-slate-900');
                });
            });

            if (!hiddenDateInput.value && availableSlots.length) {
                hiddenDateInput.value = availableSlots[0].value;
            }
        };

        const loadAvailability = async () => {
            const date = dayInput.value;
            if (!date) {
                slotsWrapper.innerHTML = '<p class="text-sm text-slate-500">Selecciona un día para ver horarios.</p>';
                hiddenDateInput.value = '';
                return;
            }

            slotsWrapper.innerHTML = '<p class="text-sm text-slate-500">Cargando horarios...</p>';

            try {
                const response = await fetch(`${availabilityUrl}?date=${encodeURIComponent(date)}`);
                const payload = await response.json();

                if (!response.ok) {
                    slotsWrapper.innerHTML = `<p class="text-sm text-rose-700">${payload.message ?? 'No se pudo cargar la disponibilidad.'}</p>`;
                    hiddenDateInput.value = '';
                    return;
                }

                renderSlots(payload.data);
            } catch (error) {
                slotsWrapper.innerHTML = '<p class="text-sm text-rose-700">Error al consultar disponibilidad. Intenta nuevamente.</p>';
                hiddenDateInput.value = '';
            }
        };

        dayInput.addEventListener('change', () => {
            hiddenDateInput.value = '';
            loadAvailability();
        });

        loadAvailability();
    </script>
@endsection

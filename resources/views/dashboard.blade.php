@extends('layouts.guest')

@section('content')
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-8 md:p-10 mb-8">
        <p class="text-sm text-slate-500 mb-2">VetClinic</p>
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-3">Cuidá la salud de tus mascotas en un solo lugar</h1>
        <p class="text-slate-600 max-w-3xl">Gestioná turnos, registrá tus mascotas y consultá su carnet de vacunación desde una experiencia simple, profesional y segura.</p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('login') }}" class="rounded-lg bg-slate-900 text-white px-4 py-2 text-sm font-semibold">Iniciar sesión</a>
        </div>
    </section>

    <section class="grid gap-4 md:grid-cols-3 mb-8">
        <article class="rounded-xl bg-white border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-slate-900">Pedir turno</h2>
            <p class="text-sm text-slate-600 mt-2">Solicitá turnos para consulta, laboratorio, diagnóstico por imágenes, cardiología, peluquería y más.</p>
        </article>
        <article class="rounded-xl bg-white border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-slate-900">Registrar mascotas</h2>
            <p class="text-sm text-slate-600 mt-2">Guardá el perfil de cada mascota con sus datos clínicos para gestionar todo más rápido.</p>
        </article>
        <article class="rounded-xl bg-white border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-slate-900">Carnet de vacunación</h2>
            <p class="text-sm text-slate-600 mt-2">Consultá y mantené actualizado el historial de vacunas, con alertas de próximos refuerzos.</p>
        </article>
    </section>

    <section class="mb-8">
        <h2 class="text-xl font-semibold text-slate-900 mb-4">Un espacio pensado para su bienestar</h2>
        <div class="grid gap-4 md:grid-cols-3">
            <article class="rounded-xl overflow-hidden bg-white border border-slate-200 shadow-sm">
                <img src="{{ asset('images/home/banio-mascotas.jpg') }}" alt="Perro en sesión de baño" class="h-52 w-full object-cover" loading="lazy">
                <div class="p-4">
                    <h3 class="text-base font-semibold text-slate-900">Baño y cuidado</h3>
                    <p class="text-sm text-slate-600 mt-1">Higiene profesional para que tu mascota se sienta cómoda y feliz.</p>
                </div>
            </article>
            <article class="rounded-xl overflow-hidden bg-white border border-slate-200 shadow-sm">
                <img src="{{ asset('images/home/peluqueria-mascotas.jpg') }}" alt="Gato en sesión de peluquería" class="h-52 w-full object-cover" loading="lazy">
                <div class="p-4">
                    <h3 class="text-base font-semibold text-slate-900">Peluquería</h3>
                    <p class="text-sm text-slate-600 mt-1">Corte y mantenimiento del pelaje adaptado a cada raza y necesidad.</p>
                </div>
            </article>
            <article class="rounded-xl overflow-hidden bg-white border border-slate-200 shadow-sm">
                <img src="{{ asset('images/home/atencion-veterinaria.jpg') }}" alt="Perro y gato en clínica veterinaria" class="h-52 w-full object-cover" loading="lazy">
                <div class="p-4">
                    <h3 class="text-base font-semibold text-slate-900">Atención integral</h3>
                    <p class="text-sm text-slate-600 mt-1">Acompañamiento clínico y preventivo para su salud en cada etapa.</p>
                </div>
            </article>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 md:p-8 mb-8">
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <h2 class="text-2xl font-bold text-slate-900">Servicios y especialidades</h2>
                <p class="text-sm text-slate-600 mt-2">Atención médica integral con tecnología diagnóstica y profesionales especializados.</p>

                <div class="grid gap-6 sm:grid-cols-2 mt-6">
                    <article>
                        <h3 class="text-base font-semibold text-slate-900">Servicios principales</h3>
                        <ul class="mt-3 space-y-2 text-sm text-slate-600">
                            <li>Laboratorio propio</li>
                            <li>Estudios cardiológicos</li>
                            <li>Diagnóstico por imágenes</li>
                            <li>Especialistas por área</li>
                            <li>Productos veterinarios y farmacia</li>
                            <li>Alimentos balanceados</li>
                        </ul>
                    </article>
                    <article>
                        <h3 class="text-base font-semibold text-slate-900">Especialidades</h3>
                        <ul class="mt-3 grid grid-cols-1 gap-2 text-sm text-slate-600">
                            <li>Cirugía y clínica quirúrgica</li>
                            <li>Clínica médica</li>
                            <li>Radiología y ecografía</li>
                            <li>Dermatología y endocrinología</li>
                            <li>Traumatología y neurología</li>
                            <li>Oncología, hematología y endoscopía</li>
                            <li>Ecocardiología y electrocardiografía</li>
                            <li>Mascotas no convencionales</li>
                        </ul>
                    </article>
                </div>
            </div>

            <article class="rounded-xl overflow-hidden border border-slate-200 bg-slate-50">
                <img src="{{ asset('images/home/servicios-especialidades.jpg') }}" alt="Médicos veterinarios en consulta" class="h-48 w-full object-cover" loading="lazy">
                <div class="p-4">
                    <h3 class="text-base font-semibold text-slate-900">Equipo especializado</h3>
                    <p class="text-sm text-slate-600 mt-2">Un enfoque interdisciplinario para cada caso clínico, desde prevención hasta tratamientos complejos.</p>
                </div>
            </article>
        </div>
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 md:p-8 mb-8">
        <h2 class="text-2xl font-bold text-slate-900">Ubicación y teléfonos</h2>
        <div class="grid gap-6 md:grid-cols-2 mt-5 text-sm text-slate-700">
            <article class="rounded-xl border border-slate-200 p-4">
                <h3 class="font-semibold text-slate-900 mb-3">Canales de contacto</h3>
                <ul class="space-y-2">
                    <li><span class="font-medium">WhatsApp:</span> 264 533-7760</li>
                    <li><span class="font-medium">Administración:</span> (264) 5263-8681</li>
                    <li><span class="font-medium">Correo general:</span> info@vetclinic-sj.com.ar</li>
                    <li><span class="font-medium">Director / Cirujano:</span> Dr. Ignacio Cerverizzo</li>
                    <li><span class="font-medium">Tel. profesional:</span> (264) 4400-8178</li>
                    <li><span class="font-medium">Correo profesional:</span> icerverizzo@vetclinic-sj.com.ar</li>
                    <li><span class="font-medium">Sugerencias:</span> calidad@vetclinic-sj.com.ar</li>
                </ul>
            </article>
            <article class="rounded-xl border border-slate-200 p-4">
                <h3 class="font-semibold text-slate-900 mb-3">Dónde estamos</h3>
                <p>Av. Libertador General San Martín 1487, Capital, San Juan, Argentina (CP 5400).</p>
                <p class="mt-3 text-slate-600">Horario de atención: lunes a viernes de 9:00 a 20:00 y sábados de 9:00 a 13:00.</p>
                <p class="mt-3 text-slate-600">Seguinos en redes sociales para novedades, campañas de vacunación y consejos de cuidado.</p>
            </article>
        </div>
    </section>

@endsection

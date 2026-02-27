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

    <section class="grid gap-4 md:grid-cols-3">
        <article class="rounded-xl bg-white border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-slate-900">Pedir turno</h2>
            <p class="text-sm text-slate-600 mt-2">Solicitá turnos para consulta, vacunación o control y recibí confirmación por correo electrónico.</p>
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
@endsection

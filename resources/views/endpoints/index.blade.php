@extends('layouts.guest')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Endpoints del sistema</h1>
            <p class="mt-2 text-slate-600">Referencia rápida de rutas web y API v1 para demo e integración.</p>
        </div>

        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-semibold text-slate-900 mb-4">Web (Blade + sesión)</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">Método</th>
                            <th class="px-3 py-2 text-left font-semibold">Ruta</th>
                            <th class="px-3 py-2 text-left font-semibold">Descripción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <tr>
                            <td class="px-3 py-2 font-mono">GET</td>
                            <td class="px-3 py-2 font-mono">/</td>
                            <td class="px-3 py-2">Landing principal</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-mono">GET</td>
                            <td class="px-3 py-2 font-mono">/login</td>
                            <td class="px-3 py-2">Formulario de acceso</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-mono">POST</td>
                            <td class="px-3 py-2 font-mono">/login</td>
                            <td class="px-3 py-2">Autenticación por sesión</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-mono">POST</td>
                            <td class="px-3 py-2 font-mono">/logout</td>
                            <td class="px-3 py-2">Cerrar sesión</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-mono">GET</td>
                            <td class="px-3 py-2 font-mono">/dashboard</td>
                            <td class="px-3 py-2">Dashboard del usuario</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-mono">GET/POST/PUT/DELETE</td>
                            <td class="px-3 py-2 font-mono">/pets, /appointments, /pets/{pet}/vaccinations</td>
                            <td class="px-3 py-2">Módulos principales de gestión</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-semibold text-slate-900 mb-4">API v1 (JSON + Sanctum)</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">Método</th>
                            <th class="px-3 py-2 text-left font-semibold">Endpoint</th>
                            <th class="px-3 py-2 text-left font-semibold">Auth</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <tr>
                            <td class="px-3 py-2 font-mono">GET</td>
                            <td class="px-3 py-2 font-mono">/api/ping</td>
                            <td class="px-3 py-2">No</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-mono">POST</td>
                            <td class="px-3 py-2 font-mono">/api/v1/auth/login</td>
                            <td class="px-3 py-2">No</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-mono">POST</td>
                            <td class="px-3 py-2 font-mono">/api/v1/auth/logout</td>
                            <td class="px-3 py-2">Sí</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-mono">GET</td>
                            <td class="px-3 py-2 font-mono">/api/v1/auth/me</td>
                            <td class="px-3 py-2">Sí</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-mono">REST</td>
                            <td class="px-3 py-2 font-mono">/api/v1/pets</td>
                            <td class="px-3 py-2">Sí</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-mono">REST</td>
                            <td class="px-3 py-2 font-mono">/api/v1/appointments</td>
                            <td class="px-3 py-2">Sí</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2 font-mono">GET/POST/DELETE</td>
                            <td class="px-3 py-2 font-mono">/api/v1/pets/{pet}/vaccinations</td>
                            <td class="px-3 py-2">Sí</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="mt-4 text-xs text-slate-500">Para endpoints protegidos, usar token Bearer emitido por <span class="font-mono">/api/v1/auth/login</span>.</p>
        </section>
    </div>
@endsection

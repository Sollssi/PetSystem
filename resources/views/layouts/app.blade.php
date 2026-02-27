<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VetClinic</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-900">
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between gap-4">
            <h1 class="text-xl sm:text-2xl font-semibold">VetClinic</h1>
            <nav class="flex items-center gap-4 text-sm">
                <a class="text-slate-600 hover:text-slate-900" href="{{ route('user.dashboard') }}">Inicio</a>
                @unlessrole('admin')
                    <a class="text-slate-600 hover:text-slate-900" href="{{ route('pets.index') }}">Mascotas</a>
                @endunlessrole
                <a class="text-slate-600 hover:text-slate-900" href="{{ route('appointments.index') }}">Citas</a>
                @role('admin')
                    <a class="text-slate-600 hover:text-slate-900" href="{{ route('admin.appointments.index') }}">Admin turnos</a>
                @endrole
                <a class="rounded-lg bg-slate-900 px-3 py-1.5 text-white hover:bg-slate-700" href="{{ route('logout') }}">Salir</a>
            </nav>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-8">
        @unless(request()->routeIs('user.dashboard'))
            <div class="mb-4">
                <button
                    type="button"
                    onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href='{{ route('user.dashboard') }}'; }"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                >
                    <span aria-hidden="true">←</span>
                    Volver atrás
                </button>
            </div>
        @endunless

        @if (session('status') || session('success'))
            <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') ?? session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <p class="font-semibold">Revisa los datos:</p>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>

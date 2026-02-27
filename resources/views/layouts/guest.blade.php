<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VetClinic</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-b from-slate-100 to-slate-200 text-slate-900 min-h-screen">
    <header class="bg-white/90 backdrop-blur border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="font-semibold text-xl">VetClinic</a>
            <nav class="flex items-center gap-4 text-sm">
                <a class="rounded-lg border border-slate-300 px-3 py-1.5 text-slate-700" href="{{ route('register') }}">Registrarse</a>
                <a class="rounded-lg bg-slate-900 px-3 py-1.5 text-white" href="{{ route('login') }}">Iniciar sesión</a>
            </nav>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-10">
        @unless(request()->routeIs('home'))
            <div class="mb-4">
                <button
                    type="button"
                    onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href='{{ route('home') }}'; }"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                >
                    <span aria-hidden="true">←</span>
                    Volver atrás
                </button>
            </div>
        @endunless

        @if (session('status'))
            <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>

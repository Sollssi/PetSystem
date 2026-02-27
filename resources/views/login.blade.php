@extends('layouts.guest')

@section('content')
    <div class="max-w-md mx-auto rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-900">Iniciar sesión</h1>
        <p class="text-sm text-slate-500 mt-1 mb-6">Accede al panel para gestionar mascotas, citas y vacunación.</p>

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('authenticate') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-300" placeholder="user@email.com">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Contraseña</label>
                <input id="password" name="password" type="password" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-300" placeholder="••••••••">
            </div>

            <button type="submit" class="w-full rounded-lg bg-slate-900 text-white py-2.5 text-sm font-semibold hover:bg-slate-700">Entrar</button>
        </form>

        <p class="mt-4 text-sm text-slate-600 text-center">
            ¿No tienes cuenta?
            <a href="{{ route('register') }}" class="font-semibold text-slate-900 hover:underline">Regístrate</a>
        </p>

        <div class="mt-5 rounded-lg bg-slate-50 border border-slate-200 p-3 text-xs text-slate-600">
            <p class="font-semibold mb-1">Credenciales de prueba</p>
            <p>Email: user@email.com</p>
            <p>Contraseña: password</p>
        </div>
    </div>
@endsection

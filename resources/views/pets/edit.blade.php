@extends('layouts.app')

@section('content')
    <div class="max-w-2xl">
        <h2 class="text-2xl font-semibold mb-4">Editar mascota</h2>
        <form method="POST" action="{{ route('pets.update', $pet) }}" class="rounded-2xl border border-slate-200 bg-white p-6">
            @csrf
            @method('PUT')
            @include('pets._form', ['pet' => $pet])
        </form>
    </div>
@endsection

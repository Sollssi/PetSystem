@extends('layouts.app')

@section('content')
    <div class="max-w-2xl">
        <h2 class="text-2xl font-semibold mb-4">Registrar nueva mascota</h2>
        <form method="POST" action="{{ route('pets.store') }}" class="rounded-2xl border border-slate-200 bg-white p-6">
            @csrf
            @include('pets._form')
        </form>
    </div>
@endsection

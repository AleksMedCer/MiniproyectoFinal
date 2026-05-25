@extends('layouts.app')

@section('title', 'Iniciar sesión')

@section('content')
<div class="grid min-h-[640px] overflow-hidden rounded bg-white shadow-xl lg:grid-cols-[0.95fr_1.05fr]">
    <section class="relative hidden bg-slate-950 text-white lg:block">
        <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=1100&q=80" alt="Compra segura en línea" class="absolute inset-0 h-full w-full object-cover opacity-75">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/80 to-slate-950/20"></div>
        <div class="relative flex h-full flex-col justify-end p-10">
            <p class="text-sm font-black uppercase text-yellow-300">Acceso seguro</p>
            <h1 class="mt-3 text-4xl font-black leading-tight">Administra compras, tickets y ventas desde una sola cuenta</h1>
            <p class="mt-4 text-slate-200">El inicio de sesión usa validación 2FA para completar el acceso.</p>
        </div>
    </section>

    <section class="flex items-center justify-center p-6 md:p-10">
        <div class="w-full max-w-md">
            <div class="mb-8">
                <p class="text-sm font-black uppercase text-cyan-700">MarketPro</p>
                <h2 class="mt-2 text-3xl font-black text-slate-950">Iniciar sesión</h2>
                <p class="mt-2 text-slate-500">Entra con tu correo y contraseña para recibir el código de seguridad.</p>
            </div>

            @if($errors->any())
                <div class="mb-6 rounded border border-red-200 bg-red-50 p-4">
                    <p class="text-sm text-red-700">{{ $errors->first() }}</p>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-black text-slate-700">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email', app()->environment('local') ? 'admin@netehis.com' : '') }}" class="mt-1 block w-full rounded border border-slate-300 px-4 py-3 outline-none focus:border-cyan-600" required>
                </div>

                <div>
                    <label class="block text-sm font-black text-slate-700">Contraseña</label>
                    <input type="password" name="password" value="{{ app()->environment('local') ? 'password' : '' }}" class="mt-1 block w-full rounded border border-slate-300 px-4 py-3 outline-none focus:border-cyan-600" required>
                </div>

                <button type="submit" class="w-full rounded bg-yellow-400 px-4 py-3 font-black text-slate-950 shadow-sm hover:bg-yellow-300">
                    Continuar
                </button>
            </form>
            <p class="mt-6 text-center text-sm text-slate-500">
                ¿No tienes cuenta?
                <a href="{{ route('register') }}" class="font-black text-cyan-700 hover:underline">Crea una cuenta</a>
            </p>
        </div>
    </section>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Registro')

@section('content')
<div class="mx-auto grid max-w-5xl overflow-hidden rounded bg-white shadow-xl lg:grid-cols-[0.9fr_1.1fr]">
    <section class="relative hidden bg-slate-950 text-white lg:block">
        <img src="https://images.unsplash.com/photo-1556740758-90de374c12ad?auto=format&fit=crop&w=1100&q=80" alt="Cliente comprando en línea" class="absolute inset-0 h-full w-full object-cover opacity-75">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/80 to-slate-950/20"></div>
        <div class="relative flex h-full flex-col justify-end p-10">
            <p class="text-sm font-black uppercase text-yellow-300">Cuenta de comprador</p>
            <h1 class="mt-3 text-4xl font-black leading-tight">Compra, guarda tickets y consulta el estado de tus pedidos</h1>
        </div>
    </section>

    <section class="p-6 md:p-10">
        <div class="mb-8">
            <p class="text-sm font-black uppercase text-cyan-700">Registro</p>
            <h2 class="mt-2 text-3xl font-black text-slate-950">Crear cuenta</h2>
            <p class="mt-2 text-slate-500">Tu cuenta se crea como comprador para usar el carrito y finalizar pedidos.</p>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded border border-red-200 bg-red-50 p-4">
                <p class="text-sm text-red-700">{{ $errors->first() }}</p>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-black text-slate-700">Nombre</label>
                <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full rounded border border-slate-300 px-4 py-3 outline-none focus:border-cyan-600" required>
            </div>

            <div>
                <label class="block text-sm font-black text-slate-700">Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full rounded border border-slate-300 px-4 py-3 outline-none focus:border-cyan-600" required>
            </div>

            <div>
                <label class="block text-sm font-black text-slate-700">Teléfono</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="mt-1 block w-full rounded border border-slate-300 px-4 py-3 outline-none focus:border-cyan-600">
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-black text-slate-700">Contraseña</label>
                    <input type="password" name="password" class="mt-1 block w-full rounded border border-slate-300 px-4 py-3 outline-none focus:border-cyan-600" required>
                </div>
                <div>
                    <label class="block text-sm font-black text-slate-700">Confirmar</label>
                    <input type="password" name="password_confirmation" class="mt-1 block w-full rounded border border-slate-300 px-4 py-3 outline-none focus:border-cyan-600" required>
                </div>
            </div>

            <button type="submit" class="w-full rounded bg-yellow-400 px-4 py-3 font-black text-slate-950 shadow-sm hover:bg-yellow-300">
                Crear cuenta
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" class="font-black text-cyan-700 hover:underline">Inicia sesión</a>
        </p>
    </section>
</div>
@endsection

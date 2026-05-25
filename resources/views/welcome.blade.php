@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="space-y-8">
    <section class="relative min-h-[520px] overflow-hidden rounded bg-slate-950 text-white shadow-xl">
        <img src="https://images.unsplash.com/photo-1607082349566-187342175e2f?auto=format&fit=crop&w=1500&q=80" alt="Compras en línea" class="absolute inset-0 h-full w-full object-cover opacity-75">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/75 to-slate-950/10"></div>
        <div class="relative flex min-h-[520px] max-w-3xl flex-col justify-center px-6 py-12 md:px-12">
            <p class="text-sm font-black uppercase text-yellow-300">MarketPro</p>
            <h1 class="mt-3 text-5xl font-black leading-tight md:text-6xl">Una tienda en línea lista para vender</h1>
            <p class="mt-5 max-w-2xl text-lg text-slate-200">Explora productos con fotos reales, tiendas verificadas, carrito funcional, tickets de compra y administración con reportes.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('compras.index') }}" class="rounded bg-yellow-400 px-6 py-3 font-black text-slate-950 shadow-lg hover:bg-yellow-300">Ver catálogo</a>
                @guest
                    <a href="{{ route('register') }}" class="rounded border border-white/40 px-6 py-3 font-black text-white hover:bg-white/10">Crear cuenta</a>
                @endguest
            </div>
        </div>
    </section>

    <section class="grid gap-4 md:grid-cols-4">
        <a href="{{ route('compras.index', ['q' => 'audio']) }}" class="rounded border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md">
            <p class="text-sm font-black uppercase text-cyan-700">Tecnología</p>
            <h2 class="mt-2 text-xl font-black text-slate-950">Audio, gadgets y electrónica</h2>
        </a>
        <a href="{{ route('compras.index', ['q' => 'cafetera']) }}" class="rounded border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md">
            <p class="text-sm font-black uppercase text-emerald-700">Cocina</p>
            <h2 class="mt-2 text-xl font-black text-slate-950">Cafeteras, freidoras y utensilios</h2>
        </a>
        <a href="{{ route('compras.index', ['q' => 'tenis']) }}" class="rounded border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md">
            <p class="text-sm font-black uppercase text-red-700">Moda</p>
            <h2 class="mt-2 text-xl font-black text-slate-950">Tenis, relojes y accesorios</h2>
        </a>
        <a href="{{ route('compras.index', ['q' => 'gaming']) }}" class="rounded border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md">
            <p class="text-sm font-black uppercase text-indigo-700">Gaming</p>
            <h2 class="mt-2 text-xl font-black text-slate-950">Consolas, monitores y controles</h2>
        </a>
    </section>
</div>
@endsection

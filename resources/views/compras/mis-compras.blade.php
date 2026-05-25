@extends('layouts.app')

@section('title', 'Mis compras')

@section('content')
<div class="space-y-6">
    <section class="rounded bg-slate-950 px-6 py-8 text-white shadow-xl">
        <p class="text-sm font-black uppercase text-yellow-300">Historial</p>
        <h1 class="mt-2 text-4xl font-black">Mis compras</h1>
        <p class="mt-2 max-w-2xl text-slate-300">Consulta tus pedidos registrados, descarga tickets y revisa el estado de validación.</p>
    </section>

    @if($ventas->isEmpty())
        <div class="rounded border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm">
            <p class="text-xl font-black text-slate-950">Todavía no tienes compras registradas.</p>
            <a href="{{ route('compras.index') }}" class="mt-5 inline-block rounded bg-yellow-400 px-5 py-3 font-black text-slate-950 hover:bg-yellow-300">Ir a compras</a>
        </div>
    @else
        <section class="grid gap-4">
            @foreach($ventas as $venta)
                @php
                    $producto = $venta->producto;
                    $foto = $producto->fotos[0] ?? null;
                    $imagen = $foto && str_starts_with($foto, 'http')
                        ? $foto
                        : ($foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($foto) ? asset('storage/'.$foto) : 'https://loremflickr.com/900/700/product?lock=1998');
                @endphp

                <article class="grid gap-4 rounded border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-[128px_1fr_auto] md:items-center">
                    <img src="{{ $imagen }}" alt="{{ $producto->nombre }}" class="aspect-[4/3] w-full rounded object-cover md:h-28" loading="lazy" onerror="this.src='https://loremflickr.com/900/700/store?lock=1997'">
                    <div>
                        <p class="text-xs font-black uppercase text-cyan-700">{{ $producto->categoria->nombre ?? 'General' }}</p>
                        <h2 class="mt-1 text-xl font-black text-slate-950">{{ $producto->nombre }}</h2>
                        <p class="mt-1 text-sm font-semibold text-slate-500">Vendido por {{ $producto->vendedor->name }}</p>
                        <p class="mt-2 text-sm text-slate-500">Pedido #{{ $venta->id }} · {{ $venta->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="text-left md:text-right">
                        <p class="text-2xl font-black text-slate-950">${{ number_format($producto->precio, 2) }}</p>
                        <span class="mt-2 inline-block rounded px-3 py-1 text-xs font-black {{ $venta->validada ? 'bg-emerald-100 text-emerald-700' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $venta->validada ? 'Validada' : 'Pendiente' }}
                        </span>
                        <a href="{{ route('ventas.ticket', $venta) }}" class="mt-3 inline-block rounded border border-slate-300 px-3 py-2 text-sm font-black text-slate-700 hover:bg-slate-50">Descargar ticket</a>
                    </div>
                </article>
            @endforeach
        </section>
    @endif
</div>
@endsection

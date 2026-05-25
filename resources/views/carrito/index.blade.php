@extends('layouts.app')

@section('title', 'Carrito')

@section('content')
<div class="space-y-6">
    <section class="flex flex-col gap-4 rounded bg-slate-950 px-6 py-8 text-white shadow-xl md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm font-black uppercase text-yellow-300">Checkout</p>
            <h1 class="mt-2 text-4xl font-black">Carrito de compras</h1>
            <p class="mt-2 max-w-2xl text-slate-300">Revisa tus productos, ajusta cantidades y registra el pedido para validación.</p>
        </div>
        <a href="{{ route('compras.index') }}" class="inline-flex w-fit rounded bg-white px-4 py-2 text-sm font-black text-slate-950 hover:bg-slate-100">Seguir comprando</a>
    </section>

    @if($errors->any())
        <div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-red-700">{{ $errors->first() }}</div>
    @endif

    @if($items->isEmpty())
        <div class="rounded border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm">
            <p class="text-xl font-black text-slate-950">Tu carrito está vacío</p>
            <p class="mt-2 text-slate-500">Explora el catálogo y agrega productos para crear tu pedido.</p>
            <a href="{{ route('compras.index') }}" class="mt-5 inline-block rounded bg-yellow-400 px-5 py-3 font-black text-slate-950 hover:bg-yellow-300">Ir a compras</a>
        </div>
    @else
        <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
            <section class="space-y-4">
                @foreach($items as $item)
                    @php
                        $producto = $item['producto'];
                        $foto = $producto->fotos[0] ?? null;
                        $imagen = $foto && str_starts_with($foto, 'http')
                            ? $foto
                            : ($foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($foto) ? asset('storage/'.$foto) : 'https://loremflickr.com/900/700/product?lock=1998');
                    @endphp

                    <article class="grid gap-4 rounded border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-[160px_1fr_auto]">
                        <img src="{{ $imagen }}" alt="{{ $producto->nombre }}" class="aspect-[4/3] w-full rounded object-cover md:h-36" loading="lazy" onerror="this.src='https://loremflickr.com/900/700/store?lock=1997'">
                        <div>
                            <p class="text-xs font-black uppercase text-cyan-700">{{ $producto->categoria->nombre ?? 'General' }}</p>
                            <h2 class="mt-1 text-xl font-black text-slate-950">{{ $producto->nombre }}</h2>
                            <p class="mt-1 text-sm font-semibold text-slate-500">Vendido por {{ $producto->vendedor->name ?? 'MarketPro' }}</p>
                            <p class="mt-2 text-sm font-semibold text-emerald-700">Disponible para validación inmediata</p>

                            <div class="mt-4 flex flex-wrap items-center gap-3">
                                <form action="{{ route('carrito.actualizar', $producto) }}" method="POST" class="flex flex-wrap items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <label class="text-sm font-semibold text-slate-600">Cantidad</label>
                                    <input type="number" name="cantidad" value="{{ $item['cantidad'] }}" min="1" max="10" class="w-20 rounded border border-slate-300 px-3 py-2">
                                    <button class="rounded border border-slate-300 px-3 py-2 text-sm font-black text-slate-700 hover:bg-slate-50" type="submit">Actualizar</button>
                                </form>

                                <form action="{{ route('carrito.eliminar', $producto) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded px-3 py-2 text-sm font-black text-red-700 hover:bg-red-50" type="submit">Eliminar</button>
                                </form>
                            </div>
                        </div>
                        <div class="text-left md:text-right">
                            <p class="text-sm font-semibold text-slate-500">Precio unitario</p>
                            <p class="font-black text-slate-950">${{ number_format($producto->precio, 2) }}</p>
                            <p class="mt-4 text-sm font-semibold text-slate-500">Subtotal</p>
                            <p class="text-2xl font-black text-slate-950">${{ number_format($item['subtotal'], 2) }}</p>
                        </div>
                    </article>
                @endforeach
            </section>

            <aside class="h-fit rounded border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-lg font-black text-slate-950">Resumen del pedido</p>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>Productos</span>
                        <span class="font-black text-slate-950">{{ $items->sum('cantidad') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span class="font-black text-slate-950">${{ number_format($total, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Envío</span>
                        <span class="font-black text-emerald-700">Por validar</span>
                    </div>
                </div>

                <div class="mt-5 border-t border-slate-200 pt-5">
                    <p class="text-sm font-semibold text-slate-500">Total estimado</p>
                    <p class="text-4xl font-black text-slate-950">${{ number_format($total, 2) }}</p>
                </div>

                <form action="{{ route('carrito.finalizar') }}" method="POST" class="mt-5">
                    @csrf
                    <button class="w-full rounded bg-yellow-400 px-4 py-3 font-black text-slate-950 shadow-sm hover:bg-yellow-300" type="submit">
                        Finalizar compra
                    </button>
                </form>

                <form action="{{ route('carrito.vaciar') }}" method="POST" class="mt-3">
                    @csrf
                    @method('DELETE')
                    <button class="w-full rounded border border-slate-300 px-4 py-3 font-black text-slate-700 hover:bg-slate-50" type="submit">
                        Vaciar carrito
                    </button>
                </form>
            </aside>
        </div>
    @endif
</div>
@endsection

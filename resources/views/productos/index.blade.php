@extends('layouts.app')

@section('title', 'Compras')

@section('content')
@php
    $tab = request('tab', 'todos');
    $query = trim((string) request('q'));
    $categoriaId = request('categoria');
    $categoriaActiva = filled($categoriaId) && !in_array(strtolower((string) $categoriaId), ['todos', 'all', '0'], true);
    $orden = request('orden', 'relevancia');

    $productosBase = $productos
        ->when($query, fn($items) => $items->filter(fn($producto) => str_contains(strtolower($producto->nombre.' '.$producto->descripcion.' '.($producto->vendedor->name ?? '')), strtolower($query))))
        ->when($categoriaActiva, fn($items) => $items->where('categoria_id', (int) $categoriaId));

    $productosFiltrados = match ($tab) {
        'ofertas' => $productosBase->filter(fn($producto) => $producto->precio <= 1000 || $producto->id % 5 === 0),
        'mas-vendidos' => $productosBase->sortByDesc('ventas_count'),
        'novedades' => $productosBase->sortByDesc('created_at'),
        'premium' => $productosBase->filter(fn($producto) => $producto->precio >= 5000),
        default => $productosBase,
    };

    $productosFiltrados = (match ($orden) {
        'precio-menor' => $productosFiltrados->sortBy('precio'),
        'precio-mayor' => $productosFiltrados->sortByDesc('precio'),
        'tienda' => $productosFiltrados->sortBy(fn($producto) => $producto->vendedor->name ?? ''),
        default => $productosFiltrados,
    })->values();

    $tabs = [
        'todos' => 'Todos',
        'ofertas' => 'Ofertas',
        'mas-vendidos' => 'Más vendidos',
        'novedades' => 'Novedades',
        'premium' => 'Premium',
    ];

    $paramsBase = array_filter([
        'q' => request('q'),
        'categoria' => $categoriaActiva ? request('categoria') : null,
        'orden' => request('orden'),
    ], fn($value) => filled($value));

    $totalCarrito = array_sum(session('carrito', []));
    $destacados = $productos->sortByDesc('ventas_count')->take(3)->values();
@endphp

<div class="space-y-8">
    <section class="overflow-hidden rounded bg-slate-950 text-white shadow-xl">
        <div class="grid min-h-[360px] lg:grid-cols-[1.1fr_0.9fr]">
            <div class="flex flex-col justify-center px-6 py-10 md:px-10">
                <p class="text-sm font-black uppercase text-yellow-300">Marketplace en línea</p>
                <h1 class="mt-3 max-w-3xl text-4xl font-black leading-tight md:text-5xl">Compra tecnología, hogar, moda y más con tiendas verificadas</h1>
                <p class="mt-4 max-w-2xl text-base text-slate-300 md:text-lg">Catálogo realista con carrito, ventas, tickets, validación gerencial y dashboard administrativo.</p>

                <form action="{{ route('compras.index') }}" method="GET" class="mt-7 max-w-3xl">
                    <div class="flex flex-col overflow-hidden rounded border border-white/10 bg-white shadow-lg sm:flex-row">
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar laptop, tenis, cafetera, celular..." class="min-h-12 min-w-0 flex-1 px-4 text-slate-900 outline-none">
                        @if(request('categoria'))
                            <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                        @endif
                        @if(request('tab'))
                            <input type="hidden" name="tab" value="{{ request('tab') }}">
                        @endif
                        <button type="submit" class="min-h-12 bg-yellow-400 px-6 font-black text-slate-950 hover:bg-yellow-300">Buscar</button>
                    </div>
                </form>

                <div class="mt-6 grid gap-3 text-sm text-slate-200 sm:grid-cols-3">
                    <div class="border-l-4 border-yellow-400 pl-3">
                        <span class="block text-xl font-black text-white">{{ $productos->count() }}+</span>
                        productos activos
                    </div>
                    <div class="border-l-4 border-cyan-400 pl-3">
                        <span class="block text-xl font-black text-white">{{ $categorias->count() }}</span>
                        categorías
                    </div>
                    <div class="border-l-4 border-emerald-400 pl-3">
                        <span class="block text-xl font-black text-white">{{ $totalCarrito }}</span>
                        piezas en carrito
                    </div>
                </div>
            </div>
            <div class="relative min-h-[320px] bg-slate-900">
                <img src="https://images.unsplash.com/photo-1556742502-ec7c0e9f34b1?auto=format&fit=crop&w=1200&q=80" alt="Personas comprando en línea" class="absolute inset-0 h-full w-full object-cover opacity-85">
                <div class="absolute inset-0 bg-gradient-to-l from-transparent via-slate-950/20 to-slate-950/70"></div>
            </div>
        </div>
    </section>

    <section class="grid gap-3 md:grid-cols-3">
        @foreach($destacados as $destacado)
            @php
                $fotoDestacada = $destacado->fotos[0] ?? null;
                $imagenDestacada = $fotoDestacada && str_starts_with($fotoDestacada, 'http')
                    ? $fotoDestacada
                    : ($fotoDestacada && \Illuminate\Support\Facades\Storage::disk('public')->exists($fotoDestacada) ? asset('storage/'.$fotoDestacada) : 'https://loremflickr.com/900/700/storefront?lock=1999');
            @endphp
            <a href="{{ route('compras.index', ['q' => $destacado->nombre]) }}" class="group grid grid-cols-[108px_1fr] overflow-hidden rounded border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <img src="{{ $imagenDestacada }}" alt="{{ $destacado->nombre }}" class="h-full min-h-28 w-full object-cover" loading="lazy">
                <div class="p-4">
                    <p class="text-xs font-black uppercase text-cyan-700">{{ $destacado->categoria->nombre ?? 'Tienda' }}</p>
                    <h2 class="mt-1 line-clamp-2 font-black text-slate-950 group-hover:text-cyan-700">{{ $destacado->nombre }}</h2>
                    <p class="mt-2 text-sm font-semibold text-slate-600">{{ $destacado->vendedor->name ?? 'MarketPro' }}</p>
                    <p class="mt-2 text-lg font-black text-slate-950">${{ number_format($destacado->precio, 2) }}</p>
                </div>
            </a>
        @endforeach
    </section>

    <nav class="flex gap-2 overflow-x-auto border-b border-slate-200 pb-2">
        @foreach($tabs as $key => $label)
            <a href="{{ $key === 'todos' ? route('compras.index') : route('compras.index', array_merge($paramsBase, ['tab' => $key])) }}"
               class="whitespace-nowrap rounded-t px-4 py-2 text-sm font-black {{ $tab === $key ? 'border-b-4 border-yellow-400 bg-white text-slate-950 shadow-sm' : 'text-slate-600 hover:bg-white' }}">
                {{ $label }}
            </a>
        @endforeach
    </nav>

    <div class="grid gap-6 lg:grid-cols-[250px_1fr]">
        <aside class="space-y-5">
            <section class="rounded border border-slate-200 bg-white p-4 shadow-sm">
                <h2 class="text-lg font-black text-slate-950">Categorías</h2>
                <div class="mt-3 space-y-2">
                    <a href="{{ route('compras.index') }}" class="flex items-center justify-between rounded px-3 py-2 text-sm {{ ! $categoriaActiva ? 'bg-yellow-100 font-black text-slate-950' : 'text-slate-700 hover:bg-slate-50' }}">
                        Todas
                        <span class="text-slate-400">{{ $productos->count() }}</span>
                    </a>
                    @foreach($categorias as $categoria)
                        <a href="{{ route('compras.index', array_filter(['tab' => $tab, 'q' => request('q'), 'categoria' => $categoria->id, 'orden' => request('orden')], fn($value) => filled($value))) }}" class="flex items-center justify-between rounded px-3 py-2 text-sm {{ (int) request('categoria') === $categoria->id ? 'bg-yellow-100 font-black text-slate-950' : 'text-slate-700 hover:bg-slate-50' }}">
                            <span>{{ $categoria->nombre }}</span>
                            <span class="text-slate-400">{{ $categoria->productos_count }}</span>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="rounded border border-slate-200 bg-white p-4 text-sm shadow-sm">
                <h2 class="font-black text-slate-950">Compra protegida</h2>
                <div class="mt-3 space-y-3 text-slate-600">
                    <p><span class="font-black text-emerald-700">Validación</span> de compra por gerencia.</p>
                    <p><span class="font-black text-cyan-700">Tickets</span> descargables por pedido.</p>
                    <p><span class="font-black text-slate-950">Tiendas</span> con historial de ventas.</p>
                </div>
            </section>
        </aside>

        <section class="space-y-4">
            <div class="flex flex-col gap-3 rounded border border-slate-200 bg-white p-4 shadow-sm md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-500">{{ $productosFiltrados->count() }} resultados</p>
                    <h2 class="text-2xl font-black text-slate-950">{{ $tabs[$tab] ?? 'Productos' }}</h2>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <form action="{{ route('compras.index') }}" method="GET" class="flex items-center gap-2">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        @if(request('q'))
                            <input type="hidden" name="q" value="{{ request('q') }}">
                        @endif
                        @if($categoriaActiva)
                            <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                        @endif
                        <label class="text-sm font-semibold text-slate-600">Ordenar</label>
                        <select name="orden" onchange="this.form.submit()" class="rounded border border-slate-300 px-3 py-2 text-sm">
                            <option value="relevancia" @selected($orden === 'relevancia')>Relevancia</option>
                            <option value="precio-menor" @selected($orden === 'precio-menor')>Menor precio</option>
                            <option value="precio-mayor" @selected($orden === 'precio-mayor')>Mayor precio</option>
                            <option value="tienda" @selected($orden === 'tienda')>Tienda</option>
                        </select>
                    </form>
                    @auth
                        @if(auth()->user()->role === 'vendedor')
                            <a href="{{ route('productos.create') }}" class="rounded bg-slate-950 px-4 py-2 text-sm font-black text-white hover:bg-slate-800">Publicar producto</a>
                        @endif
                    @endauth
                </div>
            </div>

            @if($productosFiltrados->isEmpty())
                <div class="rounded border border-dashed border-slate-300 bg-white p-10 text-center">
                    <p class="text-lg font-black text-slate-950">No encontramos productos con esos filtros.</p>
                    <a href="{{ route('compras.index') }}" class="mt-4 inline-block rounded bg-yellow-400 px-5 py-2 font-black text-slate-950 hover:bg-yellow-300">Ver todo</a>
                </div>
            @else
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($productosFiltrados as $producto)
                        @php
                            $foto = $producto->fotos[0] ?? null;
                            $imagen = $foto && str_starts_with($foto, 'http')
                                ? $foto
                                : ($foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($foto) ? asset('storage/'.$foto) : 'https://loremflickr.com/900/700/product?lock=1998');
                            $rating = min(4.9, 4.1 + (($producto->id % 8) / 10));
                            $reviews = 24 + ($producto->ventas_count * 3) + ($producto->id % 17);
                            $tieneOferta = $producto->precio <= 1000 || $producto->id % 5 === 0;
                            $precioAnterior = $tieneOferta ? $producto->precio * 1.18 : null;
                        @endphp

                        <article class="group flex h-full flex-col overflow-hidden rounded border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                            <div class="relative bg-slate-100">
                                <img src="{{ $imagen }}" alt="{{ $producto->nombre }}" class="aspect-[4/3] w-full object-cover transition duration-300 group-hover:scale-[1.02]" loading="lazy" onerror="this.src='https://loremflickr.com/900/700/store?lock=1997'">
                                <div class="absolute left-3 top-3 flex flex-wrap gap-2">
                                    @if($tieneOferta)
                                        <span class="rounded bg-red-600 px-2 py-1 text-xs font-black text-white">Oferta</span>
                                    @endif
                                    @if(($producto->ventas_count ?? 0) > 0)
                                        <span class="rounded bg-slate-950 px-2 py-1 text-xs font-black text-white">Top ventas</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-1 flex-col p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <p class="text-xs font-black uppercase text-cyan-700">{{ $producto->categoria->nombre ?? 'General' }}</p>
                                    <p class="shrink-0 rounded bg-emerald-50 px-2 py-1 text-xs font-black text-emerald-700">Disponible</p>
                                </div>

                                <h2 class="mt-2 line-clamp-2 text-lg font-black leading-snug text-slate-950">{{ $producto->nombre }}</h2>
                                <p class="mt-2 line-clamp-2 text-sm text-slate-600">{{ $producto->descripcion }}</p>

                                <p class="mt-3 text-sm font-semibold text-slate-500">Vendido por <span class="text-slate-900">{{ $producto->vendedor->name ?? 'MarketPro' }}</span></p>

                                <div class="mt-3 flex items-center gap-2 text-sm">
                                    <span class="font-black text-yellow-500">★★★★★</span>
                                    <span class="font-black text-slate-900">{{ number_format($rating, 1) }}</span>
                                    <span class="text-slate-400">({{ $reviews }})</span>
                                </div>

                                <div class="mt-4">
                                    @if($precioAnterior)
                                        <p class="text-sm text-slate-400 line-through">${{ number_format($precioAnterior, 2) }}</p>
                                    @endif
                                    <p class="text-3xl font-black text-slate-950">${{ number_format($producto->precio, 2) }}</p>
                                    <p class="text-sm font-semibold text-emerald-700">Envío rápido en pedidos validados</p>
                                </div>

                                <form action="{{ route('carrito.agregar', $producto) }}" method="POST" class="mt-auto pt-5">
                                    @csrf
                                    <button type="submit" class="w-full rounded bg-yellow-400 px-4 py-3 font-black text-slate-950 shadow-sm hover:bg-yellow-300">
                                        Agregar al carrito
                                    </button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</div>
@endsection

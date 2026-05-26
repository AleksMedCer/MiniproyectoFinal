@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $productImage = function ($producto) {
        $foto = $producto->fotos[0] ?? null;

        if ($foto && str_starts_with($foto, 'http')) {
            return $foto;
        }

        if ($foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($foto)) {
            return asset('storage/'.$foto);
        }

        return 'https://images.unsplash.com/photo-1472851294608-062f824d29cc?auto=format&fit=crop&w=900&q=80';
    };

    $ventasPendientesCount = $ventasPendientes->count();
    $productosConVenta = $productosTop->where('ventas_count', '>', 0)->count();
    $rotacionCatalogo = $totalProductos > 0 ? round(($productosConVenta / min($totalProductos, max($productosTop->count(), 1))) * 100) : 0;
@endphp

<div class="mb-8 rounded-xl bg-gray-950 px-6 py-8 text-white shadow-lg">
    <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-800">
                    <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <p class="text-sm font-semibold uppercase tracking-wider text-yellow-400">Panel Ejecutivo</p>
            </div>
            <h1 class="mt-3 text-4xl font-extrabold tracking-tight">Dashboard Operativo</h1>
            <p class="mt-3 max-w-3xl text-lg text-gray-400">Vista consolidada de indicadores comerciales, operacionales y CRM. Seleccione una sección para analizar datos específicos.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('compras.index') }}" class="inline-flex items-center justify-center rounded-lg bg-yellow-400 px-5 py-2.5 text-sm font-semibold text-gray-950 shadow-sm transition-colors hover:bg-yellow-300">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Ver tienda
            </a>
            <a href="{{ route('dashboard.reportes.ventas') }}" class="inline-flex items-center justify-center rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-gray-950 shadow-sm transition-colors hover:bg-gray-100">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Descargar CSV
            </a>
            <a href="{{ route('productos.create') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-700 bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-gray-800">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nuevo producto
            </a>
        </div>
    </div>
</div>

<div class="flex flex-col items-start gap-8 lg:flex-row">
    <!-- Sidebar -->
    <aside class="w-full shrink-0 space-y-3 lg:sticky lg:top-8 lg:w-72">
        <button onclick="showTab('resumen')" id="btn-resumen" class="tab-btn flex w-full items-center justify-between rounded-xl bg-gray-900 px-5 py-4 text-left font-semibold text-white shadow-md transition-all hover:bg-gray-800">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span>Resumen General</span>
            </div>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
        
        <button onclick="showTab('ventas')" id="btn-ventas" class="tab-btn flex w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-5 py-4 text-left font-semibold text-gray-700 shadow-sm transition-all hover:border-gray-300 hover:bg-gray-50">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 opacity-70 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                <span>Ventas Pendientes</span>
            </div>
            @if($ventasPendientesCount > 0)
                <span class="inline-flex items-center justify-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-bold text-red-700">{{ $ventasPendientesCount }}</span>
            @else
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            @endif
        </button>

        <button onclick="showTab('catalogo')" id="btn-catalogo" class="tab-btn flex w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-5 py-4 text-left font-semibold text-gray-700 shadow-sm transition-all hover:border-gray-300 hover:bg-gray-50">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 opacity-70 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <span>Catálogo e Inventario</span>
            </div>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>

        <button onclick="showTab('crm')" id="btn-crm" class="tab-btn flex w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-5 py-4 text-left font-semibold text-gray-700 shadow-sm transition-all hover:border-gray-300 hover:bg-gray-50">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 opacity-70 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span>CRM & Rendimiento</span>
            </div>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>

        <button onclick="showTab('actividad')" id="btn-actividad" class="tab-btn flex w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-5 py-4 text-left font-semibold text-gray-700 shadow-sm transition-all hover:border-gray-300 hover:bg-gray-50">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 opacity-70 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Actividad e Info</span>
            </div>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>

        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'gerente')
        <button onclick="showTab('usuarios')" id="btn-usuarios" class="tab-btn flex w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-5 py-4 text-left font-semibold text-gray-700 shadow-sm transition-all hover:border-gray-300 hover:bg-gray-50">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 opacity-70 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span>Gestión de Usuarios</span>
            </div>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
        @endif
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 w-full min-w-0">
        
        <!-- ==================== TAB: RESUMEN GENERAL ==================== -->
        <div id="tab-resumen" class="tab-content space-y-6">
            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm transition-shadow hover:shadow-md">
                    <p class="text-sm font-semibold uppercase tracking-wider text-gray-500">Ingresos totales</p>
                    <p class="mt-2 text-3xl font-extrabold text-gray-900">${{ number_format($ingresosTotales, 2) }}</p>
                    <p class="mt-2 flex items-center text-sm font-medium text-green-600">
                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        ${{ number_format($ingresosValidados, 2) }} validados
                    </p>
                </article>
                <article class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm transition-shadow hover:shadow-md">
                    <p class="text-sm font-semibold uppercase tracking-wider text-gray-500">Ventas</p>
                    <p class="mt-2 text-3xl font-extrabold text-gray-900">{{ $totalVentas }}</p>
                    <p class="mt-2 flex items-center text-sm font-medium text-yellow-600">
                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ $ventasPendientesCount }} pendientes por validar
                    </p>
                </article>
                <article class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm transition-shadow hover:shadow-md">
                    <p class="text-sm font-semibold uppercase tracking-wider text-gray-500">Ticket promedio</p>
                    <p class="mt-2 text-3xl font-extrabold text-gray-900">${{ number_format($ticketPromedio, 2) }}</p>
                    <p class="mt-2 text-sm font-medium text-gray-500">{{ $tasaValidacion }}% de ventas validadas</p>
                </article>
                <article class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm transition-shadow hover:shadow-md">
                    <p class="text-sm font-semibold uppercase tracking-wider text-gray-500">Catálogo activo</p>
                    <p class="mt-2 text-3xl font-extrabold text-gray-900">{{ $totalProductos }}</p>
                    <p class="mt-2 flex items-center text-sm font-medium text-blue-600">
                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        {{ $totalUsuarios }} usuarios registrados
                    </p>
                </article>
            </section>

            <section class="grid gap-6 xl:grid-cols-[1.4fr_0.9fr]">
                <article class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="mb-6 flex items-center justify-between border-b border-gray-100 pb-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Ventas e ingresos recientes</h2>
                            <p class="text-sm text-gray-500">Comportamiento en los últimos 14 días</p>
                        </div>
                        <span class="rounded bg-green-50 px-3 py-1 text-sm font-semibold text-green-700">Live data</span>
                    </div>
                    <div class="h-80 w-full">
                        <canvas id="salesChart"></canvas>
                    </div>
                </article>

                <article class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="mb-6 border-b border-gray-100 pb-4">
                        <h2 class="text-lg font-bold text-gray-900">Posicionador de Salud Operativa</h2>
                        <p class="text-sm text-gray-500">Indicadores clave de eficiencia</p>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <div class="mb-2 flex justify-between text-sm">
                                <span class="font-semibold text-gray-700">Validación comercial</span>
                                <span class="font-bold text-gray-900">{{ $tasaValidacion }}%</span>
                            </div>
                            <div class="h-2.5 overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full rounded-full bg-green-500" style="width: {{ min($tasaValidacion, 100) }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="mb-2 flex justify-between text-sm">
                                <span class="font-semibold text-gray-700">Rotación de catálogo</span>
                                <span class="font-bold text-gray-900">{{ min($rotacionCatalogo, 100) }}%</span>
                            </div>
                            <div class="h-2.5 overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full rounded-full bg-blue-500" style="width: {{ min($rotacionCatalogo, 100) }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="mb-2 flex justify-between text-sm">
                                <span class="font-semibold text-gray-700">Carga operativa pendiente</span>
                                <span class="font-bold text-gray-900">{{ $ventasPendientesCount }} tickets</span>
                            </div>
                            <div class="h-2.5 overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full rounded-full bg-yellow-400" style="width: {{ min($ventasPendientesCount * 8, 100) }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 grid gap-4 text-sm">
                        <div class="flex items-start gap-3 rounded-lg border border-gray-100 bg-gray-50 p-4">
                            <div class="mt-0.5 rounded bg-blue-100 p-1.5 text-blue-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">Módulo CRM</p>
                                <p class="text-gray-600">{{ $clientesTop->count() }} clientes con historial de compra y seguimiento disponible para estrategias de fidelización.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 rounded-lg border border-gray-100 bg-gray-50 p-4">
                            <div class="mt-0.5 rounded bg-purple-100 p-1.5 text-purple-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">Módulo ERP</p>
                                <p class="text-gray-600">{{ $productosSinVentas->count() }} items requieren impulso comercial o revisión estratégica de inventario.</p>
                            </div>
                        </div>
                    </div>
                </article>
            </section>
        </div>

        <!-- ==================== TAB: VENTAS PENDIENTES ==================== -->
        <div id="tab-ventas" class="tab-content hidden space-y-6">
            <div class="flex justify-end">
                <a href="{{ route('ventas.tickets.todos') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-700 bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-gray-800">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Descargar todos los tickets pendientes
                </a>
            </div>
            <article class="rounded-xl border border-gray-100 bg-white shadow-sm">
                <div class="flex flex-col items-start justify-between gap-4 border-b border-gray-100 p-6 md:flex-row md:items-center">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Ventas Pendientes de Validación</h2>
                        <p class="text-sm text-gray-500">Apruebe o revise los tickets de compra para completar el proceso</p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-bold text-yellow-800">
                        <span class="mr-1.5 h-2 w-2 rounded-full bg-yellow-500"></span>
                        {{ $ventasPendientesCount }} por validar
                    </span>
                </div>

                <div class="p-6">
                    @if($ventasPendientes->isEmpty())
                        <div class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-200 py-12 text-center">
                            <svg class="h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Todo al día</h3>
                            <p class="mt-1 text-gray-500">No hay ventas pendientes por validar en este momento.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full text-left text-sm">
                                <thead class="bg-gray-50 text-gray-600">
                                    <tr>
                                        <th class="px-6 py-4 font-semibold">Producto</th>
                                        <th class="px-6 py-4 font-semibold">Comprador</th>
                                        <th class="px-6 py-4 font-semibold">Vendedor</th>
                                        <th class="px-6 py-4 font-semibold">Ticket</th>
                                        <th class="px-6 py-4 font-semibold text-right">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach($ventasPendientes as $venta)
                                        <tr class="transition-colors hover:bg-gray-50">
                                            <td class="px-6 py-4 font-bold text-gray-900">{{ $venta->producto->nombre }}</td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-2">
                                                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700">
                                                        {{ substr($venta->comprador->name, 0, 1) }}
                                                    </div>
                                                    {{ $venta->comprador->name }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-gray-600">{{ $venta->producto->vendedor->name }}</td>
                                            <td class="px-6 py-4">
                                                <a href="{{ route('ventas.ticket', $venta) }}" class="inline-flex items-center font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                                    Ver ticket
                                                    <svg class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <form action="{{ route('ventas.validar', $venta) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2 font-semibold text-white shadow-sm transition-colors hover:bg-green-700">
                                                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        Validar Venta
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </article>
        </div>

        <!-- ==================== TAB: CATÁLOGO ==================== -->
        <div id="tab-catalogo" class="tab-content hidden space-y-6">
            <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
                <article class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="mb-6 border-b border-gray-100 pb-4">
                        <h2 class="text-lg font-bold text-gray-900">Ingresos por categoría</h2>
                        <p class="text-sm text-gray-500">Rendimiento financiero según sector</p>
                    </div>
                    <div class="h-72 w-full">
                        <canvas id="categoryRevenueChart"></canvas>
                    </div>
                </article>

                <article class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="mb-6 border-b border-gray-100 pb-4">
                        <h2 class="text-lg font-bold text-gray-900">Composición del Catálogo</h2>
                        <p class="text-sm text-gray-500">Distribución de {{ $totalProductos }} items</p>
                    </div>
                    <div class="space-y-4">
                        @foreach($productosPorCategoria as $categoria)
                            @php
                                $pct = $totalProductos > 0 ? round(($categoria->productos_count / $totalProductos) * 100) : 0;
                            @endphp
                            <div>
                                <div class="mb-1.5 flex justify-between text-sm">
                                    <span class="font-semibold text-gray-700">{{ $categoria->nombre }}</span>
                                    <span class="text-gray-500">{{ $categoria->productos_count }} items ({{ $pct }}%)</span>
                                </div>
                                <div class="h-2.5 overflow-hidden rounded-full bg-gray-100">
                                    <div class="h-full rounded-full bg-gray-800" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>
            </section>

            <section class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="mb-6 flex items-center justify-between border-b border-gray-100 pb-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Productos Destacados Top</h2>
                        <p class="text-sm text-gray-500">Items con mayor movimiento comercial</p>
                    </div>
                    @if($productoMasVendido)
                        <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-sm font-semibold text-blue-700">
                            <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                            Best Seller: {{ $productoMasVendido->nombre }}
                        </span>
                    @endif
                </div>

                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                    @foreach($productosTop as $producto)
                        <article class="group relative flex flex-col rounded-xl border border-gray-100 bg-white p-4 shadow-sm transition-all hover:shadow-md">
                            <div class="relative h-40 w-full overflow-hidden rounded-lg">
                                <img src="{{ $productImage($producto) }}" alt="{{ $producto->nombre }}" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                                <div class="absolute right-2 top-2 rounded bg-white/90 px-2 py-1 text-xs font-bold text-gray-900 shadow-sm backdrop-blur-sm">
                                    {{ $producto->ventas_count }} ventas
                                </div>
                            </div>
                            <div class="mt-4 flex flex-1 flex-col">
                                <p class="text-xs font-bold uppercase tracking-wider text-blue-600">{{ $producto->categoria->nombre ?? 'General' }}</p>
                                <h3 class="mt-1 line-clamp-2 font-bold text-gray-900 group-hover:text-blue-700">{{ $producto->nombre }}</h3>
                                <div class="mt-auto pt-3">
                                    <span class="text-lg font-extrabold text-gray-900">${{ number_format($producto->precio, 2) }}</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        </div>

        <!-- ==================== TAB: CRM / ERP ==================== -->
        <div id="tab-crm" class="tab-content hidden space-y-6">
            <section class="grid gap-6 lg:grid-cols-3">
                <article class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="mb-6 flex items-center gap-3 border-b border-gray-100 pb-4">
                        <div class="rounded-lg bg-blue-100 p-2 text-blue-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">CRM: Clientes Top</h2>
                            <p class="text-xs text-gray-500">Mayor volumen de compra</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @forelse($clientesTop as $cliente)
                            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4 transition-colors hover:bg-gray-100">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate font-bold text-gray-900">{{ $cliente['comprador']->name }}</p>
                                        <p class="truncate text-sm text-gray-500">{{ $cliente['comprador']->email }}</p>
                                    </div>
                                    <p class="text-right text-lg font-extrabold text-gray-900">${{ number_format($cliente['ingresos'], 2) }}</p>
                                </div>
                                <div class="mt-3 flex items-center justify-between">
                                    <span class="inline-flex items-center rounded bg-white px-2 py-1 text-xs font-semibold text-gray-600 shadow-sm">
                                        {{ $cliente['ventas'] }} compras
                                    </span>
                                    <a href="mailto:{{ $cliente['comprador']->email }}" class="text-xs font-semibold text-blue-600 hover:underline">Contactar</a>
                                </div>
                            </div>
                        @empty
                            <p class="py-4 text-center text-sm text-gray-500">Aún no hay datos para segmentar clientes.</p>
                        @endforelse
                    </div>
                </article>

                <article class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="mb-6 flex items-center gap-3 border-b border-gray-100 pb-4">
                        <div class="rounded-lg bg-green-100 p-2 text-green-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">ERP: Vendedores</h2>
                            <p class="text-xs text-gray-500">Rendimiento por vendedor</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @foreach($vendedoresTop as $fila)
                            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4 transition-colors hover:bg-gray-100">
                                <div class="flex items-start justify-between">
                                    <p class="font-bold text-gray-900">{{ $fila['vendedor']->name }}</p>
                                    <p class="text-lg font-extrabold text-green-700">${{ number_format($fila['ingresos'], 2) }}</p>
                                </div>
                                <div class="mt-2 flex gap-2">
                                    <span class="inline-flex rounded bg-white px-2 py-1 text-xs font-semibold text-gray-600 shadow-sm">{{ $fila['productos'] }} prods</span>
                                    <span class="inline-flex rounded bg-white px-2 py-1 text-xs font-semibold text-gray-600 shadow-sm">{{ $fila['ventas'] }} ventas</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>

                <article class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="mb-6 flex items-center gap-3 border-b border-gray-100 pb-4">
                        <div class="rounded-lg bg-orange-100 p-2 text-orange-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Items por Impulsar</h2>
                            <p class="text-xs text-gray-500">Productos sin rotación</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @forelse($productosSinVentas as $producto)
                            <div class="flex items-center gap-4 rounded-lg border border-gray-100 bg-white p-3 shadow-sm transition-colors hover:bg-gray-50">
                                <img src="{{ $productImage($producto) }}" alt="{{ $producto->nombre }}" class="h-12 w-12 rounded-lg object-cover shadow-sm">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-bold text-gray-900">{{ $producto->nombre }}</p>
                                    <p class="truncate text-xs text-gray-500">{{ $producto->categoria->nombre ?? 'General' }}</p>
                                </div>
                                <p class="font-bold text-gray-900">${{ number_format($producto->precio, 2) }}</p>
                            </div>
                        @empty
                            <div class="rounded-lg border-2 border-dashed border-gray-200 py-8 text-center">
                                <p class="text-sm font-medium text-gray-500">Excelente, todos los productos tienen movimiento.</p>
                            </div>
                        @endforelse
                    </div>
                </article>
            </section>
        </div>

        <!-- ==================== TAB: ACTIVIDAD ==================== -->
        <div id="tab-actividad" class="tab-content hidden space-y-6">
            <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
                <article class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="mb-6 border-b border-gray-100 pb-4">
                        <h2 class="text-lg font-bold text-gray-900">Últimas transacciones</h2>
                        <p class="text-sm text-gray-500">Registro en tiempo real de ventas</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-600">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">Producto</th>
                                    <th class="px-4 py-3 font-semibold">Cliente</th>
                                    <th class="px-4 py-3 font-semibold text-right">Monto</th>
                                    <th class="px-4 py-3 font-semibold text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($ventasRecientes as $venta)
                                    <tr class="transition-colors hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $venta->producto->nombre }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $venta->comprador->name }}</td>
                                        <td class="px-4 py-3 text-right font-bold text-gray-900">${{ number_format($venta->producto->precio, 2) }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $venta->validada ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $venta->validada ? 'Validada' : 'Pendiente' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </article>

                <div class="space-y-6">
                    <article class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                        <div class="mb-6 border-b border-gray-100 pb-4">
                            <h2 class="text-lg font-bold text-gray-900">Demografía de usuarios</h2>
                            <p class="text-sm text-gray-500">Distribución por rol de sistema</p>
                        </div>
                        <div class="h-48 w-full">
                            <canvas id="rolesChart"></canvas>
                        </div>
                        <div class="mt-6 grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-lg bg-gray-50 p-3 text-center">
                                <p class="text-2xl font-extrabold text-blue-600">{{ $totalCompradores }}</p>
                                <p class="text-xs font-semibold uppercase text-gray-500">Compradores</p>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-3 text-center">
                                <p class="text-2xl font-extrabold text-green-600">{{ $totalVendedores }}</p>
                                <p class="text-xs font-semibold uppercase text-gray-500">Vendedores</p>
                            </div>
                            <div class="rounded-lg bg-gray-50 p-3 text-center">
                                <p class="text-2xl font-extrabold text-orange-500">{{ $totalGerentes }}</p>
                                <p class="text-xs font-semibold uppercase text-gray-500">Gerentes</p>
                            </div>
                            <div class="rounded-lg bg-gray-900 p-3 text-center">
                                <p class="text-2xl font-extrabold text-white">{{ $totalUsuarios }}</p>
                                <p class="text-xs font-semibold uppercase text-gray-400">Total</p>
                            </div>
                        </div>
                    </article>

                    <article class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                        <div class="mb-4 border-b border-gray-100 pb-4">
                            <h2 class="text-lg font-bold text-gray-900">Productos recientes</h2>
                        </div>
                        <div class="space-y-4">
                            @foreach($productosRecientes as $producto)
                                <div class="flex items-center gap-3">
                                    <img src="{{ $productImage($producto) }}" alt="{{ $producto->nombre }}" class="h-12 w-12 rounded-lg object-cover shadow-sm">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate font-bold text-gray-900">{{ $producto->nombre }}</p>
                                        <p class="truncate text-xs text-gray-500">{{ $producto->vendedor->name }}</p>
                                    </div>
                                    <p class="font-bold text-gray-900">${{ number_format($producto->precio, 2) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </article>
                </div>
            </section>
        </div>

        <!-- ==================== TAB: GESTION DE USUARIOS ==================== -->
        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'gerente')
        <div id="tab-usuarios" class="tab-content hidden space-y-6">
            <section class="grid gap-6">
                <article class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="mb-6 flex items-center justify-between border-b border-gray-100 pb-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Crear Nuevo Usuario</h2>
                            <p class="text-sm text-gray-500">Añade usuarios al sistema asignándoles un rol</p>
                        </div>
                    </div>
                    
                    @if(session('user_success'))
                        <div class="mb-6 rounded-lg bg-green-50 p-4 text-sm text-green-700 border border-green-200">
                            {{ session('user_success') }}
                        </div>
                    @endif

                    <form action="{{ route('usuarios.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                                <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                                <input type="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Teléfono (opcional)</label>
                                <input type="text" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Rol</label>
                                <select name="role" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="comprador">Comprador</option>
                                    <option value="vendedor">Vendedor</option>
                                    <option value="gerente">Gerente</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Contraseña</label>
                                <input type="password" name="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                                <input type="password" name="password_confirmation" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                        <div class="flex justify-end pt-4">
                            <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300">
                                Crear Usuario
                            </button>
                        </div>
                    </form>
                </article>

                <article class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
                    <div class="flex flex-col gap-3 border-b border-gray-100 p-6 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Usuarios registrados</h2>
                            <p class="text-sm text-gray-500">Cuentas cargadas por registro y seeders de despliegue</p>
                        </div>
                        <span class="inline-flex w-fit items-center rounded-full bg-gray-900 px-3 py-1 text-sm font-bold text-white">
                            {{ $usuarios->count() }} usuarios
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead class="bg-gray-50 text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                                <tr>
                                    <th class="px-6 py-3">Nombre</th>
                                    <th class="px-6 py-3">Correo</th>
                                    <th class="px-6 py-3">Rol</th>
                                    <th class="px-6 py-3">Telefono</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach($usuarios as $usuario)
                                    @php
                                        $roleClasses = [
                                            'admin' => 'bg-red-50 text-red-700',
                                            'gerente' => 'bg-orange-50 text-orange-700',
                                            'vendedor' => 'bg-blue-50 text-blue-700',
                                            'comprador' => 'bg-emerald-50 text-emerald-700',
                                        ][$usuario->role] ?? 'bg-gray-100 text-gray-700';
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="whitespace-nowrap px-6 py-4 font-semibold text-gray-900">{{ $usuario->name }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-gray-600">{{ $usuario->email }}</td>
                                        <td class="whitespace-nowrap px-6 py-4">
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase {{ $roleClasses }}">
                                                {{ $usuario->role }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-gray-600">{{ $usuario->phone ?? 'Sin telefono' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </article>
            </section>
        </div>
        @endif

    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// --- TABS LOGIC ---
function showTab(tabId) {
    // Save to localStorage
    localStorage.setItem('activeDashboardTab', tabId);

    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.add('hidden');
    });
    
    // Reset all buttons styling
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('bg-gray-900', 'text-white', 'shadow-md', 'hover:bg-gray-800');
        el.classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-200', 'shadow-sm', 'hover:bg-gray-50', 'hover:border-gray-300');
        
        // Reset icon opacity
        const icon = el.querySelector('svg.opacity-100');
        if (icon) icon.classList.replace('opacity-100', 'opacity-70');
    });
    
    // Show selected tab
    document.getElementById('tab-' + tabId).classList.remove('hidden');
    
    // Highlight selected button
    const activeBtn = document.getElementById('btn-' + tabId);
    activeBtn.classList.remove('bg-white', 'text-gray-700', 'border', 'border-gray-200', 'shadow-sm', 'hover:bg-gray-50', 'hover:border-gray-300');
    activeBtn.classList.add('bg-gray-900', 'text-white', 'shadow-md', 'hover:bg-gray-800');
    
    // Set active icon opacity
    const activeIcon = activeBtn.querySelector('svg.opacity-70');
    if (activeIcon) activeIcon.classList.replace('opacity-70', 'opacity-100');

    // Dispatch resize event so Chart.js correctly calculates dimensions when unhidden
    setTimeout(() => {
        window.dispatchEvent(new Event('resize'));
    }, 50);
}

document.addEventListener('DOMContentLoaded', () => {
    const activeTab = localStorage.getItem('activeDashboardTab') || 'resumen';
    showTab(activeTab);
});

// --- CHARTS INITIALIZATION ---
const salesLabels = @json($ventasPorDia->pluck('label')->values());
const salesCounts = @json($ventasPorDia->pluck('ventas')->values());
const salesRevenue = @json($ventasPorDia->pluck('ingresos')->values());
const categoryLabels = @json($ingresosPorCategoria->pluck('categoria')->values());
const categoryRevenue = @json($ingresosPorCategoria->pluck('ingresos')->values());
const roleLabels = @json($usuariosPorRol->keys()->values());
const roleCounts = @json($usuariosPorRol->values());

// Shared defaults for modern look
Chart.defaults.font.family = "'Inter', 'sans-serif'";
Chart.defaults.color = '#6b7280';
Chart.defaults.scale.grid.color = '#f3f4f6';

new Chart(document.getElementById('salesChart'), {
    data: {
        labels: salesLabels,
        datasets: [
            {
                type: 'bar',
                label: 'Ventas (Cant.)',
                data: salesCounts,
                backgroundColor: '#3b82f6',
                borderRadius: 4,
                order: 2,
                yAxisID: 'y',
            },
            {
                type: 'line',
                label: 'Ingresos ($)',
                data: salesRevenue,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#10b981',
                order: 1,
                yAxisID: 'y1',
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        scales: {
            x: { grid: { display: false } },
            y: { beginAtZero: true, ticks: { precision: 0 } },
            y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } },
        },
        plugins: { 
            legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8 } },
            tooltip: { backgroundColor: 'rgba(17, 24, 39, 0.9)', padding: 12, cornerRadius: 8 }
        },
    },
});

new Chart(document.getElementById('categoryRevenueChart'), {
    type: 'bar',
    data: {
        labels: categoryLabels,
        datasets: [{
            label: 'Ingresos ($)',
            data: categoryRevenue,
            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#6b7280', '#f97316'],
            borderRadius: 4,
        }],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { display: false },
            tooltip: { backgroundColor: 'rgba(17, 24, 39, 0.9)', padding: 12, cornerRadius: 8 }
        },
        scales: { 
            x: { grid: { display: false } },
            y: { beginAtZero: true } 
        },
    },
});

new Chart(document.getElementById('rolesChart'), {
    type: 'doughnut',
    data: {
        labels: roleLabels,
        datasets: [{
            data: roleCounts,
            backgroundColor: ['#3b82f6', '#10b981', '#f97316', '#ef4444'],
            borderWidth: 2,
            borderColor: '#ffffff',
            hoverOffset: 4
        }],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '75%',
        plugins: { 
            legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } },
            tooltip: { backgroundColor: 'rgba(17, 24, 39, 0.9)', padding: 12, cornerRadius: 8 }
        },
    },
});
</script>
@endsection

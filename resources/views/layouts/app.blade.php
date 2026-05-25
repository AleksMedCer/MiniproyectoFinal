<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MarketPro - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    <nav class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-3">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded bg-slate-950 text-lg font-black text-yellow-300">M</span>
                <span>
                    <span class="block text-lg font-black leading-5 text-slate-950">MarketPro</span>
                    <span class="block text-xs font-semibold text-slate-500">Marketplace demo funcional</span>
                </span>
            </a>

            <div class="flex flex-wrap items-center justify-end gap-2 text-sm font-semibold">
                <a href="{{ route('compras.index') }}" class="rounded px-3 py-2 text-slate-700 hover:bg-slate-100">Compras</a>
                <a href="{{ route('carrito.index') }}" class="rounded bg-yellow-400 px-3 py-2 text-slate-950 shadow-sm hover:bg-yellow-300">
                    Carrito ({{ array_sum(session('carrito', [])) }})
                </a>
                @auth
                    @if(auth()->user()->role === 'comprador')
                        <a href="{{ route('compras.mias') }}" class="rounded px-3 py-2 text-slate-700 hover:bg-slate-100">Mis compras</a>
                    @endif
                    @if(in_array(auth()->user()->role, ['admin', 'gerente'], true))
                        <a href="{{ route('dashboard') }}" class="rounded px-3 py-2 text-slate-700 hover:bg-slate-100">Dashboard</a>
                    @endif
                    <span class="hidden max-w-48 truncate rounded bg-slate-100 px-3 py-2 text-slate-600 md:inline">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded border border-slate-300 px-3 py-2 text-slate-700 hover:bg-slate-100">Salir</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded border border-slate-300 px-3 py-2 text-slate-700 hover:bg-slate-100">Entrar</a>
                    <a href="{{ route('register') }}" class="rounded bg-slate-950 px-3 py-2 text-white shadow-sm hover:bg-slate-800">Crear cuenta</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-7xl px-4 py-8">
        @if(session('success') || session('status'))
            <div class="mb-6 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                {{ session('success') ?? session('status') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>

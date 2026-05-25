@extends('layouts.app')

@section('title', 'Verificación de Seguridad')

@section('content')
<div class="flex min-h-[620px] items-center justify-center">
    <div class="w-full max-w-md rounded border border-slate-200 bg-white p-8 shadow-xl">
        <div class="mb-8 text-center">
            <div class="mx-auto grid h-16 w-16 place-items-center rounded bg-slate-950 text-2xl font-black text-yellow-300">2F</div>
            <h2 class="mt-5 text-3xl font-black text-slate-950">Verificación 2FA</h2>
            <p class="mt-2 text-slate-500">Enviamos un código a {{ $correoDestino }}</p>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded border border-orange-200 bg-orange-50 p-4">
                <p class="text-sm text-orange-700">{{ $errors->first() }}</p>
            </div>
        @endif

        <form action="{{ route('2fa.verify') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="mb-4 block text-center text-sm font-black text-slate-700">Código de 6 dígitos</label>
                <input type="text" name="codigo" maxlength="6" class="block w-full rounded border border-slate-300 py-3 text-center font-mono text-3xl tracking-[0.35em] outline-none focus:border-cyan-600" placeholder="000000" required autofocus>
            </div>

            <button type="submit" class="w-full rounded bg-yellow-400 px-4 py-3 font-black text-slate-950 shadow-sm hover:bg-yellow-300">
                Verificar acceso
            </button>
        </form>

        @if(app()->environment('local'))
            <p class="mt-6 text-center text-xs text-slate-400">
                El código expira en 5 minutos. Si usas MAIL_MAILER=log, revisa storage/logs/laravel.log.
            </p>
        @endif
    </div>
</div>
@endsection

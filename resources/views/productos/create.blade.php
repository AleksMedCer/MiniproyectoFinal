@extends('layouts.app')

@section('title', 'Publicar producto')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <section class="rounded bg-slate-950 px-6 py-8 text-white shadow-xl">
        <p class="text-sm font-black uppercase text-yellow-300">Panel de vendedor</p>
        <h1 class="mt-2 text-4xl font-black">Publicar producto</h1>
        <p class="mt-2 text-slate-300">Agrega nombre, descripción, precio, categoría y fotografías para mostrarlo en la tienda.</p>
    </section>

    @if($errors->any())
        <div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5 rounded border border-slate-200 bg-white p-6 shadow-sm">
        @csrf

        <div>
            <label class="block text-sm font-black text-slate-700">Nombre</label>
            <input type="text" name="nombre" value="{{ old('nombre') }}" required placeholder="Ej. Monitor Gamer AOC 27 165Hz" class="mt-1 w-full rounded border border-slate-300 px-4 py-3 outline-none focus:border-cyan-600">
        </div>

        <div>
            <label class="block text-sm font-black text-slate-700">Descripción</label>
            <textarea name="descripcion" rows="4" required placeholder="Describe características, estado, garantía o beneficios principales." class="mt-1 w-full rounded border border-slate-300 px-4 py-3 outline-none focus:border-cyan-600">{{ old('descripcion') }}</textarea>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="block text-sm font-black text-slate-700">Precio</label>
                <input type="number" name="precio" value="{{ old('precio') }}" min="0.01" step="0.01" required class="mt-1 w-full rounded border border-slate-300 px-4 py-3 outline-none focus:border-cyan-600">
            </div>

            <div>
                <label class="block text-sm font-black text-slate-700">Categoría</label>
                <select name="categoria_id" required class="mt-1 w-full rounded border border-slate-300 px-4 py-3 outline-none focus:border-cyan-600">
                    <option value="">Selecciona una categoría</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}" @selected(old('categoria_id') == $categoria->id)>{{ $categoria->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-black text-slate-700">Fotos</label>
            <input type="file" name="fotos[]" accept=".jpg,.jpeg,.png" multiple required class="mt-2 block w-full rounded border border-dashed border-slate-300 px-4 py-5 text-sm text-slate-700">
            <p class="mt-1 text-xs font-semibold text-slate-500">Mínimo 1 y máximo 5 imágenes.</p>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="rounded bg-yellow-400 px-5 py-3 font-black text-slate-950 hover:bg-yellow-300">
                Guardar producto
            </button>
            <a href="{{ route('compras.index') }}" class="rounded border border-slate-300 px-5 py-3 font-black text-slate-700 hover:bg-slate-50">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection

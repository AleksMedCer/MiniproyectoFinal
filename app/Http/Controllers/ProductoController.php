<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Http\Requests\StoreProductoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    // Mostrar formulario de creación
    public function create()
    {
        $categorias = Categoria::all();
        return view('productos.create', compact('categorias'));
    }

    // Procesar la subida del producto
    public function store(StoreProductoRequest $request)
    {
        // Si llega aquí, es porque pasó TODAS las validaciones del FormRequest
        $data = $request->validated();
        $rutasFotos = [];

        // REQUISITO: Manejo de disco público para múltiples imágenes
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                // store('carpeta', 'disco_a_usar')
                $path = $foto->store('productos', 'public');
                $rutasFotos[] = $path; // Guardamos la ruta en el arreglo
            }
        }

        // Crear el producto
        Producto::create([
            'categoria_id' => $data['categoria_id'],
            'vendedor_id' => auth()->id(), // Asignamos automáticamente al vendedor logueado
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'precio' => $data['precio'],
            'fotos' => $rutasFotos, // El modelo Producto automáticamente convierte este arreglo a JSON
        ]);

        return redirect()->route('productos.index')->with('success', 'Producto publicado con éxito.');
    }

    // Mostrar todos los productos
    public function index(Request $request)
    {
        // Cargamos relaciones para mostrar tienda, categoría y popularidad sin consultas extra.
        $query = Producto::with(['categoria', 'vendedor'])
            ->withCount('ventas')
            ->latest();

        // Filtro de categorías al dar clic en la interfaz
        // Ignoramos la palabra 'todos' o 'all' para que muestre el catálogo completo
        if ($request->filled('categoria') && !in_array(strtolower($request->categoria), ['todos', 'all', '0'])) {
            $query->where('categoria_id', $request->categoria);
        }

        $productos = $query->get();
        $categorias = Categoria::withCount('productos')->orderBy('nombre')->get();

        return view('productos.index', compact('productos', 'categorias'));
    }
}

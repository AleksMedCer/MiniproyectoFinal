<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarritoController extends Controller
{
    public function index()
    {
        return view('carrito.index', [
            'items' => $this->items(),
            'total' => $this->total(),
        ]);
    }

    public function agregar(Producto $producto)
    {
        $carrito = session('carrito', []);
        $carrito[$producto->id] = min(($carrito[$producto->id] ?? 0) + 1, 10);

        session(['carrito' => $carrito]);

        return redirect()->route('carrito.index')->with('success', 'Producto agregado al carrito.');
    }

    public function actualizar(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'cantidad' => 'required|integer|min:1|max:10',
        ]);

        $carrito = session('carrito', []);
        $carrito[$producto->id] = $data['cantidad'];

        session(['carrito' => $carrito]);

        return back()->with('success', 'Carrito actualizado.');
    }

    public function eliminar(Producto $producto)
    {
        $carrito = session('carrito', []);
        unset($carrito[$producto->id]);

        session(['carrito' => $carrito]);

        return back()->with('success', 'Producto eliminado del carrito.');
    }

    public function vaciar()
    {
        session()->forget('carrito');

        return redirect()->route('carrito.index')->with('success', 'Carrito vacío.');
    }

    public function finalizar()
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('success', 'Inicia sesión para finalizar tu compra.');
        }

        if (! auth()->user()->hasRole(User::ROLE_COMPRADOR)) {
            return back()->withErrors(['carrito' => 'Solo los compradores pueden finalizar compras.']);
        }

        $items = $this->items();

        if ($items->isEmpty()) {
            return redirect()->route('compras.index')->with('success', 'Tu carrito está vacío.');
        }

        $ticketPath = 'tickets/carrito-'.auth()->id().'-'.now()->format('YmdHis').'.txt';
        Storage::disk('local')->put($ticketPath, $this->ticketContenido($items));

        foreach ($items as $item) {
            for ($i = 0; $i < $item['cantidad']; $i++) {
                Venta::create([
                    'producto_id' => $item['producto']->id,
                    'comprador_id' => auth()->id(),
                    'ticket_path' => $ticketPath,
                    'validada' => false,
                ]);
            }
        }

        session()->forget('carrito');

        return redirect()->route('compras.mias')->with('success', 'Compra registrada. Queda pendiente de validación por gerencia.');
    }

    public function misCompras()
    {
        $ventas = Venta::with(['producto.categoria', 'producto.vendedor'])
            ->where('comprador_id', auth()->id())
            ->latest()
            ->get();

        return view('compras.mis-compras', compact('ventas'));
    }

    private function items()
    {
        $carrito = session('carrito', []);
        $productos = Producto::with(['categoria', 'vendedor'])
            ->whereIn('id', array_keys($carrito))
            ->get()
            ->keyBy('id');

        return collect($carrito)
            ->map(function ($cantidad, $productoId) use ($productos) {
                $producto = $productos->get((int) $productoId);

                if (! $producto) {
                    return null;
                }

                return [
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'subtotal' => $producto->precio * $cantidad,
                ];
            })
            ->filter()
            ->values();
    }

    private function total(): float
    {
        return (float) $this->items()->sum('subtotal');
    }

    private function ticketContenido($items): string
    {
        $lineas = [
            'Ticket de carrito',
            'Comprador: '.auth()->user()->name.' <'.auth()->user()->email.'>',
            'Fecha: '.now()->format('Y-m-d H:i:s'),
            '',
            'Productos:',
        ];

        foreach ($items as $item) {
            $lineas[] = '- '.$item['producto']->nombre.' x'.$item['cantidad'].' = $'.number_format($item['subtotal'], 2);
        }

        $lineas[] = '';
        $lineas[] = 'Total: $'.number_format($items->sum('subtotal'), 2);

        return implode(PHP_EOL, $lineas);
    }
}

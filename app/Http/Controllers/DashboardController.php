<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function storeUsuario(Request $request)
    {
        $this->authorize('viewDashboard', User::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => ['required', Rule::in(['comprador', 'vendedor', 'gerente', 'admin'])],
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return back()->with('user_success', 'Usuario creado correctamente.');
    }

    public function index()
    {
        if (auth()->user()->hasRole([User::ROLE_COMPRADOR, User::ROLE_VENDEDOR])) {
            return redirect()->route('productos.index');
        }

        // REQUISITO: Políticas de acceso para administración y gerencia
        $this->authorize('viewDashboard', User::class);

        $totalUsuarios = User::count();
        $totalGerentes = User::where('role', User::ROLE_GERENTE)->count();
        $totalVendedores = User::where('role', 'vendedor')->count();
        $totalCompradores = User::where('role', 'comprador')->count();

        $totalProductos = Producto::count();
        $totalVentas = Venta::count();
        $ventasValidadas = Venta::where('validada', true)->count();
        $ventasPendientes = Venta::with(['producto.vendedor', 'comprador'])
            ->where('validada', false)
            ->latest()
            ->get();

        $ventas = Venta::with(['producto.categoria', 'producto.vendedor', 'comprador'])->latest()->get();
        $ingresosTotales = $ventas->sum(fn($venta) => $venta->producto?->precio ?? 0);
        $ingresosValidados = $ventas->where('validada', true)->sum(fn($venta) => $venta->producto?->precio ?? 0);
        $ticketPromedio = $totalVentas > 0 ? $ingresosTotales / $totalVentas : 0;
        $tasaValidacion = $totalVentas > 0 ? round(($ventasValidadas / $totalVentas) * 100) : 0;

        $productosPorCategoria = Categoria::withCount('productos')->orderBy('nombre')->get();
        $usuariosPorRol = User::select('role')->get()->groupBy('role')->map->count();

        $ventasPorDia = collect(range(13, 0))
            ->map(function ($daysAgo) use ($ventas) {
                $date = now()->subDays($daysAgo)->toDateString();
                $ventasDelDia = $ventas->filter(fn($venta) => $venta->created_at->toDateString() === $date);

                return [
                    'label' => now()->subDays($daysAgo)->format('d/m'),
                    'ventas' => $ventasDelDia->count(),
                    'ingresos' => round($ventasDelDia->sum(fn($venta) => $venta->producto?->precio ?? 0), 2),
                ];
            });

        $productoMasVendido = Producto::withCount('ventas')
            ->with(['categoria', 'vendedor'])
            ->orderBy('ventas_count', 'desc')
            ->first();

        $productosTop = Producto::withCount('ventas')
            ->with(['categoria', 'vendedor'])
            ->orderBy('ventas_count', 'desc')
            ->limit(8)
            ->get();

        $productosRecientes = Producto::with(['categoria', 'vendedor'])
            ->latest()
            ->limit(8)
            ->get();

        $ingresosPorCategoria = $ventas
            ->groupBy(fn($venta) => $venta->producto?->categoria?->nombre ?? 'Sin categoria')
            ->map(fn($items, $categoria) => [
                'categoria' => $categoria,
                'ventas' => $items->count(),
                'ingresos' => round($items->sum(fn($venta) => $venta->producto?->precio ?? 0), 2),
            ])
            ->sortByDesc('ingresos')
            ->values();

        $clientesTop = $ventas
            ->groupBy('comprador_id')
            ->map(function ($items) {
                $comprador = $items->first()->comprador;

                return [
                    'comprador' => $comprador,
                    'ventas' => $items->count(),
                    'ingresos' => round($items->sum(fn($venta) => $venta->producto?->precio ?? 0), 2),
                    'ultima_compra' => $items->max('created_at'),
                ];
            })
            ->sortByDesc('ingresos')
            ->take(6)
            ->values();

        $vendedoresTop = User::where('role', User::ROLE_VENDEDOR)
            ->withCount('productos')
            ->with(['productos.ventas'])
            ->get()
            ->map(function ($vendedor) {
                $ventas = $vendedor->productos->flatMap->ventas;

                return [
                    'vendedor' => $vendedor,
                    'productos' => $vendedor->productos_count,
                    'ventas' => $ventas->count(),
                    'ingresos' => round($ventas->sum(fn($venta) => $venta->producto?->precio ?? 0), 2),
                ];
            })
            ->sortByDesc('ingresos')
            ->take(5)
            ->values();

        $productosSinVentas = Producto::with(['categoria', 'vendedor'])
            ->withCount('ventas')
            ->get()
            ->where('ventas_count', 0)
            ->take(6);

        $ventasRecientes = $ventas->take(8);

        return view('dashboard.index', compact(
            'totalUsuarios',
            'totalGerentes',
            'totalVendedores',
            'totalCompradores',
            'totalProductos',
            'totalVentas',
            'ventasValidadas',
            'ventasPendientes',
            'ingresosTotales',
            'ingresosValidados',
            'ticketPromedio',
            'tasaValidacion',
            'productosPorCategoria',
            'usuariosPorRol',
            'ventasPorDia',
            'productoMasVendido',
            'productosTop',
            'productosRecientes',
            'ingresosPorCategoria',
            'clientesTop',
            'vendedoresTop',
            'productosSinVentas',
            'ventasRecientes'
        ));
    }

    public function exportVentasCsv()
    {
        $this->authorize('viewDashboard', User::class);

        $ventas = Venta::with(['producto.categoria', 'producto.vendedor', 'comprador'])
            ->latest()
            ->get();

        return response()->streamDownload(function () use ($ventas) {
            $output = fopen('php://output', 'w');

            fputcsv($output, [
                'id',
                'fecha',
                'producto',
                'categoria',
                'comprador',
                'vendedor',
                'monto',
                'estado',
            ]);

            foreach ($ventas as $venta) {
                fputcsv($output, [
                    $venta->id,
                    $venta->created_at->format('Y-m-d H:i:s'),
                    $venta->producto->nombre,
                    $venta->producto->categoria->nombre ?? 'Sin categoria',
                    $venta->comprador->email,
                    $venta->producto->vendedor->email,
                    $venta->producto->precio,
                    $venta->validada ? 'validada' : 'pendiente',
                ]);
            }

            fclose($output);
        }, 'reporte-ventas.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}

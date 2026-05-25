<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CarritoController;

Route::view('/', 'welcome')->name('home');

// Rutas de Login Normal
Route::get('/entrar', [AuthController::class, 'showLogin'])->name('login');
Route::post('/entrar', [AuthController::class, 'login']);
Route::get('/login', fn () => redirect()->route('login'));
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Rutas del 2FA (Fase 2)
Route::get('/2fa', [TwoFactorController::class, 'index'])->name('2fa.index');
Route::post('/2fa', [TwoFactorController::class, 'verify'])->name('2fa.verify');

Route::get('/compras', [ProductoController::class, 'index'])->name('compras.index');
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
Route::post('/carrito/finalizar', [CarritoController::class, 'finalizar'])->middleware('auth')->name('carrito.finalizar');
Route::post('/carrito/{producto}', [CarritoController::class, 'agregar'])->name('carrito.agregar');
Route::patch('/carrito/{producto}', [CarritoController::class, 'actualizar'])->name('carrito.actualizar');
Route::delete('/carrito/{producto}', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');
Route::delete('/carrito', [CarritoController::class, 'vaciar'])->name('carrito.vaciar');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/usuarios', [DashboardController::class, 'storeUsuario'])->name('usuarios.store');
    Route::get('/dashboard/reportes/ventas.csv', [DashboardController::class, 'exportVentasCsv'])->name('dashboard.reportes.ventas');
    Route::get('/mis-compras', [CarritoController::class, 'misCompras'])->name('compras.mias');

    Route::get('/productos/crear', [ProductoController::class, 'create'])->name('productos.create');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');

    // Ruta para registrar la venta
    Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
    Route::patch('/ventas/{venta}/validar', [VentaController::class, 'validar'])->name('ventas.validar');

    // Ruta protegida para ver el ticket (invoca al método que tiene el $this->authorize)
    Route::get('/ventas/tickets/todos', [VentaController::class, 'descargarTodosTickets'])->name('ventas.tickets.todos');
    Route::get('/ventas/{venta}/ticket', [VentaController::class, 'descargarTicket'])->name('ventas.ticket');
});

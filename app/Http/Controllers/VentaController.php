<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Http\Requests\StoreVentaRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\VentaValidadaVendedorMail;
use App\Mail\VentaValidadaCompradorMail;

class VentaController extends Controller
{
    // Procesar la compra
    public function store(StoreVentaRequest $request)
    {
        $data = $request->validated();

        // REQUISITO: Almacenamiento en Disco Privado ('local')
        // Esto guarda el archivo en storage/app/tickets/ (NO accesible públicamente)
        $pathTicket = $request->file('ticket')->store('tickets', 'local');

        Venta::create([
            'producto_id' => $data['producto_id'],
            'comprador_id' => auth()->id(),
            'ticket_path' => $pathTicket,
            'validada' => false,
        ]);

        return redirect()->back()->with('success', 'Compra registrada. Esperando validación del gerente.');
    }

    public function descargarTicket(Venta $venta)
    {
        $this->authorize('viewTicket', $venta);

        $venta->loadMissing(['producto.vendedor', 'comprador']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('ventas.ticket-pdf', [
            'ventas' => collect([$venta]),
            'fecha' => clone $venta->created_at,
            'comprador' => $venta->comprador,
            'ticket_id' => 'IND-' . $venta->id
        ]);

        return $pdf->download('ticket-' . $venta->id . '.pdf');
    }

    public function descargarTodosTickets()
    {
        $this->authorize('validarVenta', Venta::class); // Gerente / Admin only

        $ventas = Venta::with(['producto.vendedor', 'comprador'])
            ->where('validada', false)
            ->latest()
            ->get();

        if ($ventas->isEmpty()) {
            return back()->with('success', 'No hay tickets pendientes.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('ventas.ticket-pdf', [
            'ventas' => $ventas,
            'fecha' => now(),
            'comprador' => auth()->user(), // Usamos el admin/gerente actual como referente del reporte
            'ticket_id' => 'BULK-PENDIENTES-' . now()->format('YmdHis')
        ]);

        return $pdf->download('tickets-pendientes.pdf');
    }

    public function validar(Venta $venta)
    {
        // REQUISITO: Policy - Solo el gerente puede validar la venta
        $this->authorize('validarVenta', Venta::class);

        $venta->update(['validada' => true]);

        $venta->loadMissing(['producto.vendedor', 'comprador']);

        Mail::to($venta->producto->vendedor->email)->send(new VentaValidadaVendedorMail($venta));
        Mail::to($venta->comprador->email)->send(new VentaValidadaCompradorMail($venta));

        return redirect()->back()->with('success', 'Venta validada y correos enviados correctamente.');
    }
}

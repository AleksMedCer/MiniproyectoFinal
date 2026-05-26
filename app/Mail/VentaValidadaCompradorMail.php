<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Venta;

class VentaValidadaCompradorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $venta;

    public function __construct(Venta $venta)
    {
        $this->venta = $venta;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Validación de tu compra exitosa');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.venta_comprador');
    }
}

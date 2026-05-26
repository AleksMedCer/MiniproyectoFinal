<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Venta;

class VentaValidadaVendedorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $venta;

    public function __construct(Venta $venta)
    {
        $this->venta = $venta; // Pasamos la venta completa
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: '¡Tu venta ha sido validada!');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.venta_vendedor');
    }
}

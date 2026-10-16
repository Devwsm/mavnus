<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Email konfirmasi "pesanan diterima" untuk pembeli. Dikirim langsung (tidak lewat queue,
 * karena hosting tanpa terminal tidak punya queue worker) dan hanya kalau
 * config('mavnus.order_email_enabled') aktif.
 */
class OrderPlaced extends Mailable
{
    public function __construct(public Order $order)
    {
        $order->loadMissing('items');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pesanan ' . $this->order->order_number . ' diterima - Mavnus',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.order-placed');
    }
}
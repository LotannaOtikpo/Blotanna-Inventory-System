<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function build()
    {
        // We use Reply-To for the business email so that the 'From' header 
        // can match the authenticated SMTP user (Gmail requirement) to prevent blocking.
        return $this->replyTo('admin@blotanna.com', 'Blotanna Nig Ltd')
                    ->subject('Invoice #' . $this->invoice->invoice_number . ' - Blotanna Nig Ltd')
                    ->view('invoices.email');
    }
}
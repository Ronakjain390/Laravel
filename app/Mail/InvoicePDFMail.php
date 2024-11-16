<?php

// app/Mail/InvoicePDFMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoicePDFMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;
    public $pdfUrl;
    public $invoice;

    /**
     * Create a new message instance.
     *
     * @param  string  $pdfUrl
     * @return void
     */
    public function __construct($subject,$message,$pdfUrl, $invoice)
    {
        // dd($invoice);
        $this->subject = $subject;
        $this->message = $message;
        $this->pdfUrl = $pdfUrl;
        $this->invoice = $invoice;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    // public function build()
    // {
    //     return $this->subject($this->subject)->view('emails.sender.invoice_pdf')->with([
    //         'message' => $this->message,
    //         'pdfUrl' => $this->pdfUrl,
    //     ])->attach($this->pdfUrl);
    // }

    public function build()
    {
        return $this->subject($this->subject)->view('emails.seller.invoice_pdf')->with([
            'message' => $this->message,
            'pdfUrl' => $this->pdfUrl,
            'invoice' => $this->invoice->invoice_series,
        ]);
    }
}
 
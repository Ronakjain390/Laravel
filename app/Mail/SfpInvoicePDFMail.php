<?php

// app/Mail/SfpChallanPDFMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SfpInvoicePDFMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;
    public $pdfUrl;
    public $invoice;
    public $userName;

    /**
     * Create a new message instance.
     *
     * @param  string  $pdfUrl
     * @return void
     */
    public function __construct($subject,$message,$pdfUrl, $invoice,$userName)
    {
        // dd($invoice->id, $subject);
        $this->subject = $subject;
        $this->message = $message;
        $this->pdfUrl = $pdfUrl;
        $this->invoice = $invoice;
        $this->userName = $userName;
        // dd($this->invoice);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
  
    public function build()
    {
        // dd($this->message);
        return $this->subject($this->subject)->view('emails.seller.sfp_invoice_pdf')->with([
            'displayMessage' => $this->message,
            'pdfUrl' => $this->pdfUrl,
            'invoice' => $this->invoice->invoice_series,
            'userName' => $this->userName,

        ]);
    }
}

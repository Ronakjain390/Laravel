<?php

// app/Mail/PurchaseOrderPDFMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderPDFMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;
    public $pdfUrl;
    public $purchaseOrder;

    /**
     * Create a new message instance.
     *
     * @param  string  $subject
     * @param  string  $message
     * @param  string  $pdfUrl
     * @return void
     */
    public function __construct($subject, $message, $pdfUrl, $purchaseOrder)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->pdfUrl = $pdfUrl;
        $this->purchaseOrder = $purchaseOrder;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)->view('emails.buyer.purchase_order_pdf')->with([
            'message' => $this->message,
            'pdfUrl' => $this->pdfUrl,
            'purchaseOrder' => $this->purchaseOrder,
        ])->attach($this->pdfUrl);
    }
}

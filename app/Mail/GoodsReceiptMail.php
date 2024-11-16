<?php

// app/Mail/GoodsReceiptMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GoodsReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;
    public $pdfUrl;
    public $goodsReceipt;

    /**
     * Create a new message instance.
     *
     * @param  string  $pdfUrl
     * @return void
     */
    public function __construct($subject,$message,$pdfUrl, $goodsReceipt)
    {
        // dd($goodsReceipt->id, $subject);
        $this->subject = $subject;
        $this->message = $message;
        $this->pdfUrl = $pdfUrl;
        $this->goodsReceipt = $goodsReceipt;
        // dd($this->goodsReceipt);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
  
    public function build()
    {
        // dd($this->message);
        return $this->subject($this->subject)->view('emails.grn.goods_receipt_pdf')->with([
            'displayMessage' => $this->message,
            'pdfUrl' => $this->pdfUrl,
            'goodsReceipt' => $this->goodsReceipt->goods_series,

        ]);
    }
}

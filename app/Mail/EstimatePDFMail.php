<?php

// app/Mail/EstimatePDFMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EstimatePDFMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;
    public $pdfUrl;
    public $estimate;

    /**
     * Create a new message instance.
     *
     * @param  string  $pdfUrl
     * @return void
     */
    public function __construct($subject,$message,$pdfUrl, $estimate)
    {
        // dd($estimate);
        $this->subject = $subject;
        $this->message = $message;
        $this->pdfUrl = $pdfUrl;
        $this->estimate = $estimate;
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
        return $this->subject($this->subject)->view('emails.estimate.estimate_pdf')->with([
            'message' => $this->message,
            'pdfUrl' => $this->pdfUrl,
            'estimate' => $this->estimate->estimate_series,
        ]);
    }
}

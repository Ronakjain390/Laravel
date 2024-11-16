<?php

// app/Mail/SfpChallanPDFMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SfpReturnChallanPDFMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;
    public $pdfUrl;
    public $challan;
    public $userName;

    /**
     * Create a new message instance.
     *
     * @param  string  $pdfUrl
     * @return void
     */
    public function __construct($subject,$message,$pdfUrl, $challan, $userName)
    {
        // dd($challan->id, $subject);
        $this->subject = $subject;
        $this->message = $message;
        $this->pdfUrl = $pdfUrl;
        $this->challan = $challan;
        $this->userName = $userName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
  
    public function build()
    {
        // dd($this->message);
        return $this->subject($this->subject)->view('emails.receiver.sfp_return_challan_pdf')->with([
            'displayMessage' => $this->message,
            'pdfUrl' => $this->pdfUrl,
            'challan' => $this->challan->challan_series,
            'userName' => $this->userName,

        ]);
    }
}

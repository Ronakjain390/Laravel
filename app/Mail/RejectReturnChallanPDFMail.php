<?php

// app/Mail/ReturnChallanPDFMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AcceptReturnChallanPDFMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;
    public $pdfUrl;
    public $returnChallan;
    

    /**
     * Create a new message instance.
     *
     * @param  string  $subject
     * @param  string  $message
     * @param  string  $pdfUrl
     * @return void
     */
    public function __construct($subject, $message, $pdfUrl, $returnChallan)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->pdfUrl = $pdfUrl;
        $this->returnChallan = $returnChallan;
        // dd($this->returnChallan);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    // public function build()
    // {
    //     return $this->subject($this->subject)->view('emails.receiver.return_challan_pdf')->with([
    //         'displayMessage' => $this->message,
    //         'pdfUrl' => $this->pdfUrl,
    //     ])->attach($this->pdfUrl);
    // }

    public function build()
    {
        return $this->subject($this->subject)->view('emails.sender.reject_return_challan_pdf')->with([
            'displayMessage' => $this->message,
            'pdfUrl' => $this->pdfUrl,
            'returnChallan' => $this->returnChallan,
            // 'challan' => $this->challan->challan_series,
        ]);
    }
}

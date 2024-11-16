<?php

// app/Mail/ChallanPDFMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AddCommentSentChallanMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject; 
    public $pdfUrl;
    public $challan;
    public $status_comment;
    public $heading;

    /**
     * Create a new message instance.
     *
     * @param  string  $pdfUrl
     * @return void
     */
    public function __construct($subject, $challan, $status_comment, $heading)
    { 
        $this->subject = $subject;  
        $this->challan = $challan;
        $this->status_comment = $status_comment;
        $this->heading = $heading;
        // dd($this->heading);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
  
    public function build()
    {
        // dd($this->message);
        return $this->subject($this->subject)->view('emails.sender.add_comment_sent_challlan_pdf')->with([  
            'challan' => $this->challan->challan_series,
            'status_comment' => $this->status_comment,
            'heading' => $this->heading,

        ]);
    }
}

<?php
// app/Mail/ChallanPDFMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExportReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $downloadLink;
    public $dynamicText;
    public $heading;

    /**
     * Create a new message instance.
     *
     * @param  string  $downloadLink
     * @param  string  $heading
     * @param  string  $dynamicText
     * @return void
     */
    public function __construct($downloadLink, $heading, $dynamicText)
    {
        $this->downloadLink = $downloadLink;
        $this->heading = $heading;
        $this->dynamicText = $dynamicText;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.export.export')
                    ->subject($this->heading)
                    ->with([
                        'downloadLink' => $this->downloadLink,
                        'dynamicText' => $this->dynamicText,
                    ]);
    }
}
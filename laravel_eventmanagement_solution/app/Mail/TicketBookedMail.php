<?php

namespace App\Mail;

use App\Models\Template;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketBookedMail extends Mailable
{
    use Queueable, SerializesModels;
    public $name;
    public $ticket;

    /**
     * Create a new message instance.
     *
     * 
     * @return void
     */
    public function __construct($name, $ticket)
    {
        $this->name = $name;
        $this->ticket = $ticket;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Ticket Booked Mail',
            cc: 'testreceiver-cc@gmail.com',
            bcc: 'testreceiver-bcc@gmail.com'
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        $template = Template::all();
        return new Content(
            view: 'Email.TicketBooking',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
?>
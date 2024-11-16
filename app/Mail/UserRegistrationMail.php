<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userData;

    /**
     * Create a new message instance.
     *
     * @param  int  $userData
     * @return void
     */
    public function __construct($userData)
    {
        $this->userData = $userData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New User Registration')
            ->markdown('emails.Auth.user_registration_mail');
    }
}

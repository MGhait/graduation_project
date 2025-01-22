<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $email;
    public $name;
    public $token;
    public $for;

    /**
     * Create a new message instance.
     */


    public function __construct($email,$name, $token, $for = 'api' )
    {
        $this->email = $email;
        $this->name = $name;
        $this->token = $token;
        $this->for = $for;
    }
    public function build()
    {
        return $this->subject('Verify Your Email Address')
            ->view('emails.verify')
            ->with([
                'name' => $this->name,
                'url' => url('/' . $this->for. '/verify?email='. $this->email.'&token='. $this->token),
            ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verification Email',
        );
    }

    /**
     * Get the message content definition.
     */
//    public function content(): Content
//    {
//        return new Content(
//            view: 'view.name',
//        );
//    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

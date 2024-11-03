<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderSynFailEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Ticket $ticket;
    public $subject;
    public $isSuccess;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Ticket $ticket, $subject, $isSuccess = 0)
    {
        $this->ticket = $ticket;
        $this->subject = $subject;
        $this->isSuccess = $isSuccess;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            from: new Address('crmalerts.inittech@gmail.com', 'Auso System Alert'),
            cc: ['crmalerts.inittech@gmail.com'],
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'mails.order-sync-fail-email',
            with: ['subject' => $this->subject, 'ticket' => $this->ticket, 'isSuccess' => $this->isSuccess],
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

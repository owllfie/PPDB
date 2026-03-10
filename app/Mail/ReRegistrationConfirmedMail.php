<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReRegistrationConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $nis;

    public function __construct(User $user, string $nis)
    {
        $this->user = $user;
        $this->nis = $nis;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daftar Ulang Berhasil',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reregistration-confirmed',
        );
    }
}

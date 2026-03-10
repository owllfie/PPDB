<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestPassedMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $actionUrl;

    public function __construct(User $user, string $actionUrl)
    {
        $this->user = $user;
        $this->actionUrl = $actionUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Lulus Seleksi - Daftar Ulang',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.test-passed',
        );
    }
}

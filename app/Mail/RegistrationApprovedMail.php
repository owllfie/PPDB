<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $studentName;
    public string $nisn;
    public string $adminName;

    public function __construct(string $studentName, string $nisn, string $adminName)
    {
        $this->studentName = $studentName;
        $this->nisn = $nisn;
        $this->adminName = $adminName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registration Approved',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-approved',
        );
    }
}

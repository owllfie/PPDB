<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $otp;
    public string $name;

    public function __construct(User $user, string $otp)
    {
        $this->otp = $otp;
        $this->name = $user->nama_lengkap ?? $user->email ?? 'User';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Email Verification - OTP Code',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a WhatsApp message.
     * 
     * In a real-world scenario, this would call a WhatsApp API (like Twilio, Wablas, etc.)
     * For this project, we'll simulate the sending by logging it and providing a deep link.
     */
    public function sendMessage(string $phoneNumber, string $message)
    {
        // Format phone number to international format if needed (simple check)
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        if (strpos($phoneNumber, '0') === 0) {
            $phoneNumber = '62' . substr($phoneNumber, 1);
        }

        // Log the message for debugging
        Log::info("WhatsApp Message sent to {$phoneNumber}: {$message}");

        // Return a wa.me link that can be used in the UI if needed
        return "https://wa.me/{$phoneNumber}?text=" . urlencode($message);
    }

    /**
     * Send a password reset OTP.
     */
    public function sendOTP(string $phoneNumber, string $otp)
    {
        $message = "Kode OTP reset password Anda adalah: *{$otp}*. Kode ini berlaku selama 10 menit. Jangan berikan kode ini kepada siapa pun.";
        return $this->sendMessage($phoneNumber, $message);
    }
}

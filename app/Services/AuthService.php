<?php

namespace App\Services;

use App\Models\User;
use App\Mail\VerificationEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AuthService
{
    public function registerUser(array $data): User
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'], ['rounds' => 12]),
            'role' => 1,
            'is_verified' => false,
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        Mail::to($user->email)->queue(new VerificationEmail($user, $otp));

        return $user;
    }

    public function verifyOtp(User $user, string $otp): bool
    {
        if ($user->is_verified) {
            return true;
        }

        if ($user->otp_code !== $otp) {
            return false;
        }

        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return false;
        }

        $user->update([
            'is_verified' => true,
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        return true;
    }

    public function resendOtp(User $user): void
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        Mail::to($user->email)->queue(new VerificationEmail($user, $otp));
    }

    public function attemptLogin(string $email, string $password): User|false
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return false;
        }

        if (!$user->is_verified) {
            return false;
        }

        return $user;
    }

    public function generateCaptcha(): array
    {
        $num1 = random_int(1, 20);
        $num2 = random_int(1, 20);
        $operators = ['+', '-'];
        $operator = $operators[array_rand($operators)];

        if ($operator === '-' && $num1 < $num2) {
            [$num1, $num2] = [$num2, $num1];
        }

        $answer = $operator === '+' ? $num1 + $num2 : $num1 - $num2;
        $question = "{$num1} {$operator} {$num2} = ?";

        return [
            'question' => $question,
            'answer' => $answer,
        ];
    }

    public function isOnline(): bool
    {
        $connected = @fsockopen("www.google.com", 443);
        if ($connected) {
            fclose($connected);
            return true;
        }
        return false;
    }

    public function verifyRecaptcha(string $token): bool
    {
        $secret = env('RECAPTCHA_SECRET_KEY');
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$token}");
        $responseKeys = json_decode($response, true);
        return (bool)($responseKeys["success"] ?? false);
    }
}

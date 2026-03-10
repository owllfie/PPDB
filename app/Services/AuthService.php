<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function attemptLogin(string $email, string $password): User|false
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
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

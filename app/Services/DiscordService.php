<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordService
{
    protected string $webhookUrl;

    public function __construct()
    {
        $this->webhookUrl = env('DISCORD_WEBHOOK_URL', '');
    }

    public function sendNotification(string $title, string $description, int $color = 3447003, array $fields = []): void
    {
        if (empty($this->webhookUrl) || $this->webhookUrl === 'https://discord.com/api/webhooks/your_webhook_url_here') {
            Log::warning('Discord Webhook URL is not configured.');
            return;
        }

        $payload = [
            'embeds' => [
                [
                    'title' => $title,
                    'description' => $description,
                    'color' => $color,
                    'fields' => $fields,
                    'timestamp' => now()->toIso8601String(),
                    'footer' => [
                        'text' => 'Pendaftaran Siswa Notification System',
                    ],
                ]
            ]
        ];

        try {
            Http::post($this->webhookUrl, $payload);
        } catch (\Exception $e) {
            Log::error('Failed to send Discord notification: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Services;

use App\Models\Setting;
use Carbon\Carbon;

class SettingService
{
    public function getSettings(): ?Setting
    {
        return Setting::first();
    }

    public function updateSettings(array $data, int $userId): Setting
    {
        $setting = Setting::first();

        $payload = [
            'nama_sekolah' => $data['nama_sekolah'] ?? null,
            'alamat' => $data['alamat'] ?? null,
            'kepala_sekolah' => $data['kepala_sekolah'] ?? null,
            'nomor_kontak' => $data['nomor_kontak'] ?? null,
            'updated_at' => Carbon::now(),
            'updated_by' => $userId,
        ];

        if (isset($data['logo_sekolah'])) {
            $path = $data['logo_sekolah']->store('logos', 'public');
            $payload['logo_sekolah'] = $path;
        }

        if ($setting) {
            $setting->update($payload);
        } else {
            $payload['created_at'] = Carbon::now();
            $payload['created_by'] = $userId;
            $setting = Setting::create($payload);
        }

        return $setting;
    }
}

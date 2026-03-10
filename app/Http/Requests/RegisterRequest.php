<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'no_hp' => ['required', 'string', 'max:50'],
            'nisn' => ['required', 'string', 'max:50'],
            'nama_lengkap' => ['required', 'string', 'max:50'],
            'nik' => ['required', 'string', 'max:50'],
            'tempat_lahir' => ['required', 'string', 'max:50'],
            'tanggal_lahir' => ['required', 'date'],
            'jenis_kelamin' => ['required', 'string', 'max:50'],
            'agama' => ['required', 'string', 'max:50'],
            'anak_ke-' => ['required', 'integer'],
            'alamat_lengkap' => ['required', 'string', 'max:255'],
            'nama_ayah' => ['required', 'string', 'max:50'],
            'nama_ibu' => ['required', 'string', 'max:50'],
            'pekerjaan_ayah' => ['required', 'string', 'max:50'],
            'pekerjaan_ibu' => ['required', 'string', 'max:50'],
            'sekolah_asal' => ['required', 'string', 'max:50'],
            'kk' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'ijazah' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'akta_lahir' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'rapor' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'pas_foto' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already registered.',
            'no_hp.required' => 'Phone number is required.',
            'nisn.required' => 'NISN is required.',
            'nama_lengkap.required' => 'Full name is required.',
            'nik.required' => 'NIK is required.',
            'tempat_lahir.required' => 'Place of birth is required.',
            'tanggal_lahir.required' => 'Date of birth is required.',
            'jenis_kelamin.required' => 'Gender is required.',
            'agama.required' => 'Religion is required.',
            'anak_ke-.required' => 'Child order is required.',
            'alamat_lengkap.required' => 'Full address is required.',
            'nama_ayah.required' => 'Father name is required.',
            'nama_ibu.required' => 'Mother name is required.',
            'pekerjaan_ayah.required' => 'Father occupation is required.',
            'pekerjaan_ibu.required' => 'Mother occupation is required.',
            'sekolah_asal.required' => 'School origin is required.',
        ];
    }
}

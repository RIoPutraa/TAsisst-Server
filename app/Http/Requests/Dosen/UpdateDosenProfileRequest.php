<?php
// app/Http/Requests/Dosen/UpdateDosenProfileRequest.php

namespace App\Http\Requests\Dosen;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateDosenProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();
        return [
            'nama'            => 'sometimes|string|max:255',
            'email'           => [
                'sometimes', 'email',
                Rule::unique('users', 'email')->ignore($user->user_id, 'user_id'),
            ],
            'bidang_keahlian' => 'sometimes|string|max:255',
            'kuota_bimbingan' => 'sometimes|integer|min:0',
            'profil_singkat'  => 'sometimes|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'           => 'Email sudah digunakan.',
            'kuota_bimbingan.min'    => 'Kuota bimbingan tidak boleh negatif.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
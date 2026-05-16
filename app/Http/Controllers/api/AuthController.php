<?php
// app/Http/Controllers/Api/AuthController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterMahasiswaRequest;
use App\Http\Requests\Auth\RegisterDosenRequest;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    // ==================== REGISTER MAHASISWA ====================

    public function registerMahasiswa(RegisterMahasiswaRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'nama'         => $request->nama,
                'email'        => $request->email,
                'password_hash'=> Hash::make($request->password),
                'role'         => 'mahasiswa',
                'status_akun'  => 'aktif',
            ]);

            $mahasiswa = Mahasiswa::create([
                'user_id'  => $user->user_id,
                'nim'      => $request->nim,
                'prodi'    => $request->prodi,
                'angkatan' => $request->angkatan,
                'topik_ta' => $request->topik_ta,
                'judul_ta' => $request->judul_ta,
            ]);

            DB::commit();

            return $this->successResponse('Registrasi mahasiswa berhasil', [
                'user' => [
                    'user_id'  => $user->user_id,
                    'nama'     => $user->nama,
                    'email'    => $user->email,
                    'role'     => $user->role,
                ],
                'mahasiswa' => [
                    'mahasiswa_id' => $mahasiswa->mahasiswa_id,
                    'nim'          => $mahasiswa->nim,
                    'prodi'        => $mahasiswa->prodi,
                    'angkatan'     => $mahasiswa->angkatan,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Registrasi gagal: ' . $e->getMessage(), null, 500);
        }
    }

    // ==================== REGISTER DOSEN ====================

    public function registerDosen(RegisterDosenRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'nama'          => $request->nama,
                'email'         => $request->email,
                'password_hash' => Hash::make($request->password),
                'role'          => 'dosen',
                'status_akun'   => 'aktif',
            ]);

            $dosen = Dosen::create([
                'user_id'         => $user->user_id,
                'nid'             => $request->nid,
                'bidang_keahlian' => $request->bidang_keahlian,
                'kuota_bimbingan' => $request->kuota_bimbingan,
                'profil_singkat'  => $request->profil_singkat,
            ]);

            DB::commit();

            return $this->successResponse('Registrasi dosen berhasil', [
                'user' => [
                    'user_id' => $user->user_id,
                    'nama'    => $user->nama,
                    'email'   => $user->email,
                    'role'    => $user->role,
                ],
                'dosen' => [
                    'dosen_id'        => $dosen->dosen_id,
                    'nid'             => $dosen->nid,
                    'bidang_keahlian' => $dosen->bidang_keahlian,
                    'kuota_bimbingan' => $dosen->kuota_bimbingan,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Registrasi gagal: ' . $e->getMessage(), null, 500);
        }
    }

    // ==================== LOGIN ====================

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return $this->errorResponse('Email atau password salah.', null, 401);
        }

        if ($user->status_akun !== 'aktif') {
            return $this->errorResponse('Akun Anda tidak aktif. Hubungi administrator.', null, 403);
        }

        // Hapus semua token lama sebelum buat yang baru
        $user->tokens()->delete();

        // Buat token baru dengan expired 1 jam
        $expiresAt = now()->addHour();
        $token     = $user->createToken('auth_token', ['*'], $expiresAt)->plainTextToken;

        // Siapkan data profil berdasarkan role
        $profileData = $this->getProfileData($user);

        return $this->successResponse('Login berhasil', [
            'user' => [
                'user_id' => $user->user_id,
                'nama'    => $user->nama,
                'email'   => $user->email,
                'role'    => $user->role,
            ],
            'profile'    => $profileData,
            'token'      => $token,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ]);
    }

    // ==================== LOGOUT ====================

    public function logout(Request $request): JsonResponse
    {
        // Hapus token aktif saat ini
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse('Logout berhasil.');
    }

    // ==================== PROFILE ====================

    public function profile(Request $request): JsonResponse
    {
        $user        = $request->user();
        $profileData = $this->getProfileData($user);

        return $this->successResponse('Data profil berhasil diambil', [
            'user' => [
                'user_id'     => $user->user_id,
                'nama'        => $user->nama,
                'email'       => $user->email,
                'role'        => $user->role,
                'status_akun' => $user->status_akun,
                'created_at'  => $user->created_at?->format('Y-m-d H:i:s'),
            ],
            'profile' => $profileData,
        ]);
    }

    // ==================== PRIVATE HELPER ====================

    private function getProfileData(User $user): ?array
    {
        return match ($user->role) {
            'mahasiswa' => $user->mahasiswa ? [
                'mahasiswa_id' => $user->mahasiswa->mahasiswa_id,
                'nim'          => $user->mahasiswa->nim,
                'prodi'        => $user->mahasiswa->prodi,
                'angkatan'     => $user->mahasiswa->angkatan,
                'topik_ta'     => $user->mahasiswa->topik_ta,
                'judul_ta'     => $user->mahasiswa->judul_ta,
            ] : null,

            'dosen' => $user->dosen ? [
                'dosen_id'        => $user->dosen->dosen_id,
                'nid'             => $user->dosen->nid,
                'bidang_keahlian' => $user->dosen->bidang_keahlian,
                'kuota_bimbingan' => $user->dosen->kuota_bimbingan,
                'sisa_kuota'      => $user->dosen->sisaKuota(),
                'profil_singkat'  => $user->dosen->profil_singkat,
            ] : null,

            'admin' => $user->admin ? [
                'admin_id' => $user->admin->admin_id,
                'jabatan'  => $user->admin->jabatan,
            ] : null,

            default => null,
        };
    }
}
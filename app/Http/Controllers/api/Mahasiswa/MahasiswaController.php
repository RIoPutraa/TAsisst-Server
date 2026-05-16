<?php
// app/Http/Controllers/Api/Mahasiswa/MahasiswaController.php

namespace App\Http\Controllers\Api\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    use ApiResponse;

    // GET /api/mahasiswa/dosen
    // Lihat daftar semua dosen beserta profil, keahlian, kuota
    public function daftarDosen(Request $request): JsonResponse
    {
        $query = Dosen::with('user')
            ->whereHas('user', fn($q) => $q->where('status_akun', 'aktif'));

        // Filter by bidang keahlian jika ada
        if ($request->filled('bidang_keahlian')) {
            $query->where('bidang_keahlian', 'like', '%' . $request->bidang_keahlian . '%');
        }

        // Filter hanya yang masih ada kuota
        if ($request->boolean('ada_kuota')) {
            $query->whereRaw('kuota_bimbingan > (
                SELECT COUNT(*) FROM bimbingan
                WHERE bimbingan.dosen_id = dosen.dosen_id
                AND bimbingan.status_bimbingan = "aktif"
            )');
        }

        $dosen = $query->paginate($request->get('per_page', 10));

        $data = $dosen->through(function ($d) {
            $bimbinganAktif = $d->bimbinganAktif()->count();
            return [
                'dosen_id'        => $d->dosen_id,
                'nama'            => $d->user->nama,
                'email'           => $d->user->email,
                'nid'             => $d->nid,
                'bidang_keahlian' => $d->bidang_keahlian,
                'kuota_bimbingan' => $d->kuota_bimbingan,
                'sisa_kuota'      => max(0, $d->kuota_bimbingan - $bimbinganAktif),
                'profil_singkat'  => $d->profil_singkat,
            ];
        });

        return $this->successResponse('Daftar dosen berhasil diambil', $data);
    }
}
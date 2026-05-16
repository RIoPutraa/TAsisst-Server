<?php
// app/Http/Controllers/Api/Dosen/DosenController.php

namespace App\Http\Controllers\Api\Dosen;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dosen\UpdateDosenProfileRequest;
use App\Models\Bimbingan;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DosenController extends Controller
{
    use ApiResponse;

    // GET /api/dosen/profile
    public function profile(Request $request): JsonResponse
    {
        $user  = $request->user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return $this->errorResponse('Data dosen tidak ditemukan.', null, 404);
        }

        $bimbinganAktif = $dosen->bimbinganAktif()->count();

        return $this->successResponse('Profil dosen berhasil diambil', [
            'user' => [
                'user_id'     => $user->user_id,
                'nama'        => $user->nama,
                'email'       => $user->email,
                'status_akun' => $user->status_akun,
            ],
            'dosen' => [
                'dosen_id'        => $dosen->dosen_id,
                'nid'             => $dosen->nid,
                'bidang_keahlian' => $dosen->bidang_keahlian,
                'kuota_bimbingan' => $dosen->kuota_bimbingan,
                'sisa_kuota'      => max(0, $dosen->kuota_bimbingan - $bimbinganAktif),
                'bimbingan_aktif' => $bimbinganAktif,
                'profil_singkat'  => $dosen->profil_singkat,
            ],
        ]);
    }

    // PUT /api/dosen/profile
    public function updateProfile(UpdateDosenProfileRequest $request): JsonResponse
    {
        $user  = $request->user();
        $dosen = $user->dosen;

        if (!$dosen) {
            return $this->errorResponse('Data dosen tidak ditemukan.', null, 404);
        }

        // Update data user jika ada
        $userFields = array_filter([
            'nama'  => $request->nama,
            'email' => $request->email,
        ]);

        if (!empty($userFields)) {
            $user->update($userFields);
        }

        // Update data dosen jika ada
        $dosenFields = array_filter([
            'bidang_keahlian' => $request->bidang_keahlian,
            'kuota_bimbingan' => $request->kuota_bimbingan,
            'profil_singkat'  => $request->profil_singkat,
        ], fn($v) => $v !== null);

        if (!empty($dosenFields)) {
            $dosen->update($dosenFields);
        }

        $dosen->refresh();
        $user->refresh();

        return $this->successResponse('Profil berhasil diperbarui', [
            'user' => [
                'user_id' => $user->user_id,
                'nama'    => $user->nama,
                'email'   => $user->email,
            ],
            'dosen' => [
                'dosen_id'        => $dosen->dosen_id,
                'nid'             => $dosen->nid,
                'bidang_keahlian' => $dosen->bidang_keahlian,
                'kuota_bimbingan' => $dosen->kuota_bimbingan,
                'profil_singkat'  => $dosen->profil_singkat,
            ],
        ]);
    }

    // GET /api/dosen/mahasiswa-bimbingan
    public function mahasiswaBimbingan(Request $request): JsonResponse
    {
        $dosen = $request->user()->dosen;

        if (!$dosen) {
            return $this->errorResponse('Data dosen tidak ditemukan.', null, 404);
        }

        $bimbingan = Bimbingan::with([
                'mahasiswa.user',
                'progresAktif',
            ])
            ->where('dosen_id', $dosen->dosen_id)
            ->when(
                $request->filled('status'),
                fn($q) => $q->where('status_bimbingan', $request->status)
            )
            ->orderByDesc('tanggal_mulai')
            ->paginate($request->get('per_page', 10));

        $data = $bimbingan->through(function ($b) {
            return [
                'bimbingan_id'     => $b->bimbingan_id,
                'tanggal_mulai'    => $b->tanggal_mulai->format('Y-m-d'),
                'status_bimbingan' => $b->status_bimbingan,
                'mahasiswa'        => [
                    'mahasiswa_id' => $b->mahasiswa->mahasiswa_id,
                    'nama'         => $b->mahasiswa->user->nama,
                    'nim'          => $b->mahasiswa->nim,
                    'prodi'        => $b->mahasiswa->prodi,
                    'angkatan'     => $b->mahasiswa->angkatan,
                    'topik_ta'     => $b->mahasiswa->topik_ta,
                    'judul_ta'     => $b->mahasiswa->judul_ta,
                ],
                'progres_terkini'  => $b->progresAktif ? [
                    'persentase'      => $b->progresAktif->persentase,
                    'status_progress' => $b->progresAktif->status_progress,
                    'updated_at'      => $b->progresAktif->updated_at->format('Y-m-d H:i:s'),
                ] : null,
            ];
        });

        return $this->successResponse('Daftar mahasiswa bimbingan berhasil diambil', $data);
    }

    // GET /api/dosen/mahasiswa-bimbingan/{id}/progres
    public function progreesMahasiswa(Request $request, int $id): JsonResponse
    {
        $dosen = $request->user()->dosen;

        if (!$dosen) {
            return $this->errorResponse('Data dosen tidak ditemukan.', null, 404);
        }

        $bimbingan = Bimbingan::with([
                'mahasiswa.user',
                'progresTA.checklistProgress',
            ])
            ->where('bimbingan_id', $id)
            ->where('dosen_id', $dosen->dosen_id)
            ->first();

        if (!$bimbingan) {
            return $this->errorResponse(
                'Bimbingan tidak ditemukan atau bukan milik Anda.',
                null, 404
            );
        }

        $progres = $bimbingan->progresTA->map(function ($p) {
            $totalChecklist   = $p->checklistProgress->count();
            $selesaiChecklist = $p->checklistProgress->where('tgl_selesai', true)->count();

            return [
                'progress_id'     => $p->progress_id,
                'persentase'      => $p->persentase,
                'status_progress' => $p->status_progress,
                'catatan'         => $p->catatan,
                'updated_at'      => $p->updated_at->format('Y-m-d H:i:s'),
                'checklist_summary' => [
                    'total'   => $totalChecklist,
                    'selesai' => $selesaiChecklist,
                ],
                'checklist'       => $p->checklistProgress->map(fn($c) => [
                    'checklist_id'    => $c->checklist_id,
                    'nama_item'       => $c->nama_item,
                    'tgl_selesai'     => $c->tgl_selesai,
                    'tanggal_selesai' => $c->tanggal_selesai?->format('Y-m-d'),
                    'catatan'         => $c->catatan,
                ]),
            ];
        });

        return $this->successResponse('Data progres mahasiswa berhasil diambil', [
            'bimbingan_id'     => $bimbingan->bimbingan_id,
            'status_bimbingan' => $bimbingan->status_bimbingan,
            'mahasiswa'        => [
                'mahasiswa_id' => $bimbingan->mahasiswa->mahasiswa_id,
                'nama'         => $bimbingan->mahasiswa->user->nama,
                'nim'          => $bimbingan->mahasiswa->nim,
                'judul_ta'     => $bimbingan->mahasiswa->judul_ta,
            ],
            'progres'          => $progres,
        ]);
    }
}
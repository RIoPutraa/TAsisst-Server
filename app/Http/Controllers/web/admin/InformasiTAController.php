<?php
// app/Http/Controllers/Web/Admin/InformasiTAController.php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\InformasiTA;
use App\Models\User;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;

class InformasiTAController extends Controller
{
    // ==================== Helper ambil admin dari session ====================
    private function getAdminFromSession(): ?Admin
    {
        $adminData = session('admin_user');
        if (!$adminData) return null;

        return Admin::find($adminData['admin_id']);
    }

    // ==================== INDEX ====================
    public function index(Request $request)
    {
        $query = InformasiTA::with('admin.user')
            ->when($request->filled('search'), fn($q) =>
                $q->where('judul', 'like', '%'.$request->search.'%')
                  ->orWhere('konten_or_file', 'like', '%'.$request->search.'%')
            )
            ->when($request->filled('status'), function ($q) use ($request) {
                if ($request->status === 'published') {
                    $q->whereNotNull('published_at')
                      ->where('published_at', '<=', now());
                } elseif ($request->status === 'draft') {
                    $q->where(function ($q2) {
                        $q2->whereNull('published_at')
                           ->orWhere('published_at', '>', now());
                    });
                }
            })
            ->when($request->filled('kategori'), fn($q) =>
                $q->where('kategori', $request->kategori)
            )
            ->orderByDesc('created_at');

        $totalInfo = InformasiTA::count();
        $published = InformasiTA::whereNotNull('published_at')
                        ->where('published_at', '<=', now())->count();
        $drafts    = $totalInfo - $published;

        $infos = $query->paginate(15)->withQueryString();

        $kategoriList = InformasiTA::whereNotNull('kategori')
            ->distinct()->pluck('kategori')->sort()->values();

        return view('admin.informasi-ta.index', compact(
            'infos', 'totalInfo', 'published', 'drafts', 'kategoriList'
        ));
    }

    // ==================== STORE ====================
    public function store(Request $request)
    {
        $request->validate([
            'judul'          => 'required|string|max:255',
            'kategori'       => 'required|string|max:100',
            'konten_or_file' => 'required|string',
            'published_at'   => 'nullable|date',
        ], [
            'judul.required'          => 'Judul wajib diisi.',
            'kategori.required'       => 'Kategori wajib diisi.',
            'konten_or_file.required' => 'Konten wajib diisi.',
        ]);

        // Ambil admin dari session — bukan dari $request->user()
        $admin = $this->getAdminFromSession();

        if (!$admin) {
            return back()
                ->with('error', 'Sesi admin tidak ditemukan. Silakan login ulang.')
                ->withInput();
        }

        // Tentukan published_at
        $publishedAt = null;
        if ($request->boolean('is_published')) {
            // Publish sekarang atau sesuai tanggal yang dipilih
            $publishedAt = $request->filled('published_at')
                ? $request->published_at
                : now()->toDateTimeString();
        } elseif ($request->filled('published_at')) {
            $publishedAt = $request->published_at;
        }

        $info = InformasiTA::create([
            'admin_id'       => $admin->admin_id,
            'kategori'       => $request->kategori,
            'judul'          => $request->judul,
            'konten_or_file' => $request->konten_or_file,
            'published_at'   => $publishedAt,
        ]);

        // Kirim notifikasi jika langsung publish
        if ($publishedAt) {
            $userIds = User::whereIn('role', ['mahasiswa', 'dosen'])
                ->where('status_akun', 'aktif')
                ->pluck('user_id')->toArray();

            if (!empty($userIds)) {
                NotifikasiService::kirimKeBanyak(
                    userIds  : $userIds,
                    tipe     : 'informasi_ta',
                    judul    : 'Informasi TA Baru: ' . $request->judul,
                    pesan    : "Informasi TA [{$request->kategori}]: {$request->judul}",
                    refTabel : 'informasi_ta',
                    refId    : $info->info_id
                );
            }
        }

        return redirect()->route('admin.informasi-ta.index')
            ->with('success', 'Informasi TA berhasil ditambahkan.');
    }

    // ==================== UPDATE ====================
    public function update(Request $request, int $id)
    {
        $request->validate([
            'judul'          => 'required|string|max:255',
            'kategori'       => 'required|string|max:100',
            'konten_or_file' => 'required|string',
            'published_at'   => 'nullable|date',
        ]);

        $info = InformasiTA::findOrFail($id);

        $publishedAt = null;
        if ($request->boolean('is_published')) {
            $publishedAt = $request->filled('published_at')
                ? $request->published_at
                : now()->toDateTimeString();
        } elseif ($request->filled('published_at')) {
            $publishedAt = $request->published_at;
        }

        $info->update([
            'kategori'       => $request->kategori,
            'judul'          => $request->judul,
            'konten_or_file' => $request->konten_or_file,
            'published_at'   => $publishedAt,
        ]);

        return redirect()->route('admin.informasi-ta.index')
            ->with('success', 'Informasi TA berhasil diperbarui.');
    }

    // ==================== DESTROY ====================
    public function destroy(int $id)
    {
        $info  = InformasiTA::findOrFail($id);
        $judul = $info->judul;
        $info->delete();

        return redirect()->route('admin.informasi-ta.index')
            ->with('success', "Informasi \"{$judul}\" berhasil dihapus.");
    }

    // ==================== TOGGLE PUBLISH ====================
    public function togglePublish(int $id)
    {
        $info        = InformasiTA::findOrFail($id);
        $isPublished = $info->published_at && $info->published_at <= now();

        if ($isPublished) {
            $info->update(['published_at' => null]);
            return back()->with('success', "Informasi \"{$info->judul}\" berhasil di-unpublish.");
        } else {
            $info->update(['published_at' => now()]);

            $userIds = User::whereIn('role', ['mahasiswa', 'dosen'])
                ->where('status_akun', 'aktif')
                ->pluck('user_id')->toArray();

            if (!empty($userIds)) {
                NotifikasiService::kirimKeBanyak(
                    userIds  : $userIds,
                    tipe     : 'informasi_ta',
                    judul    : 'Informasi TA: ' . $info->judul,
                    pesan    : "Informasi TA [{$info->kategori}] dipublikasikan: {$info->judul}",
                    refTabel : 'informasi_ta',
                    refId    : $info->info_id
                );
            }

            return back()->with('success', "Informasi \"{$info->judul}\" berhasil dipublish.");
        }
    }
}
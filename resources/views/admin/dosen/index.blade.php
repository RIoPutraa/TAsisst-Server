@extends('layout.admin')

@section('title', 'Dosen - TAssist Admin')
@section('page-title', 'Manajemen Dosen')
@section('page-subtitle', 'Kelola data dosen pembimbing')

@section('content')
<div class="space-y-5">

    {{-- Top Filter --}}
    <form method="GET" action="{{ route('admin.dosen.index') }}" id="filterForm">
        <div class="flex flex-wrap items-center gap-3">

            {{-- Search --}}
            <div class="relative flex-1 min-w-[280px]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-[#A0A8C0]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama atau NID..."
                    onchange="this.form.submit()"
                    class="w-full pl-11 pr-4 py-3 rounded-2xl bg-[#242D45] border border-[#3A4566] text-sm text-white outline-none placeholder:text-[#7F89A8] focus:border-[#4DA3FF]"
                >
            </div>

            {{-- Filter Bidang Keahlian --}}
            <select
                name="bidang_keahlian"
                onchange="this.form.submit()"
                class="min-w-[220px] px-4 py-3 rounded-2xl bg-[#242D45] border border-[#3A4566] text-sm text-[#A0A8C0] outline-none"
            >
                <option value="">Semua Bidang Keahlian</option>
                @foreach($bidangKeahlianList as $bidang)
                    <option value="{{ $bidang }}" {{ request('bidang_keahlian') === $bidang ? 'selected' : '' }}>
                        {{ $bidang }}
                    </option>
                @endforeach
            </select>

            {{-- Filter Kuota --}}
            <select
                name="kuota"
                onchange="this.form.submit()"
                class="min-w-[160px] px-4 py-3 rounded-2xl bg-[#242D45] border border-[#3A4566] text-sm text-[#A0A8C0] outline-none"
            >
                <option value="">Semua Status Kuota</option>
                <option value="available" {{ request('kuota') === 'available' ? 'selected' : '' }}>Masih Tersedia</option>
                <option value="full"      {{ request('kuota') === 'full'      ? 'selected' : '' }}>Penuh</option>
            </select>

            {{-- Tombol Add --}}
            <button
                type="button"
                onclick="document.getElementById('addLecturerModal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-[#0057B8] text-white font-semibold hover:bg-[#0046A0] transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Dosen
            </button>
        </div>
    </form>

    {{-- Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @forelse($dosen as $d)
            @php
                $bimbinganAktif = $d->bimbinganAktif()->count();
                $kuota          = $d->kuota_bimbingan;
                $sisa           = max(0, $kuota - $bimbinganAktif);
                $pct            = $kuota > 0 ? round(($bimbinganAktif / $kuota) * 100) : 0;
                $isFull         = $sisa <= 0;

                $badgeBg    = $isFull ? 'rgba(255,77,77,0.15)'   : 'rgba(61,220,151,0.15)';
                $badgeColor = $isFull ? '#FF4D4D'                 : '#3DDC97';
                $badgeText  = $isFull ? 'Penuh'                   : 'Tersedia';
                $barColor   = $isFull ? '#FF4D4D' : ($pct > 70 ? '#FFB900' : '#3DDC97');
            @endphp

            {{-- Card dengan data-* untuk JS --}}
            <div class="rounded-[24px] p-5 bg-[#242D45] border border-[#3A4566]"
                data-id="{{ $d->dosen_id }}"
                data-nama="{{ $d->user->nama }}"
                data-email="{{ $d->user->email }}"
                data-nid="{{ $d->nid }}"
                data-bidang="{{ $d->bidang_keahlian }}"
                data-kuota="{{ $d->kuota_bimbingan }}"
                data-profil="{{ addslashes($d->profil_singkat ?? '') }}"
                data-status="{{ $d->user->status_akun }}"
                data-bimbingan-aktif="{{ $d->bimbinganAktif()->count() }}"
                data-sisa="{{ max(0, $d->kuota_bimbingan - $d->bimbinganAktif()->count()) }}"
                data-url-edit="{{ route('admin.dosen.update', $d->dosen_id) }}"
                data-url-kuota="{{ route('admin.dosen.kuota', $d->dosen_id) }}"
                data-url-delete="{{ route('admin.dosen.destroy', $d->dosen_id) }}">

                {{-- Header Card --}}
                <div class="flex items-start justify-between mb-5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-[#0057B8] flex items-center justify-center text-white text-xl font-bold flex-shrink-0">
                            {{ strtoupper(substr($d->user->nama, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-white text-[17px] font-bold leading-tight">{{ $d->user->nama }}</h3>
                            <p class="text-[#A0A8C0] text-sm mt-1">{{ $d->nid }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium"
                          style="background-color: {{ $badgeBg }}; color: {{ $badgeColor }};">
                        {{ $badgeText }}
                    </span>
                </div>

                {{-- Info --}}
                <div class="space-y-3 mb-5">
                    <div class="flex justify-between gap-4 text-sm">
                        <span class="text-[#A0A8C0]">Bidang Keahlian</span>
                        <span class="text-white font-medium text-right max-w-[60%]">
                            {{ $d->bidang_keahlian ?? '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between gap-4 text-sm">
                        <span class="text-[#A0A8C0]">Email</span>
                        <span class="text-[#4DA3FF] text-right max-w-[60%] break-all">
                            {{ $d->user->email }}
                        </span>
                    </div>
                    <div class="flex justify-between gap-4 text-sm">
                        <span class="text-[#A0A8C0]">Status Akun</span>
                        <span class="font-medium {{ $d->user->status_akun === 'aktif' ? 'text-[#3DDC97]' : 'text-[#FF4D4D]' }}">
                            {{ ucfirst($d->user->status_akun) }}
                        </span>
                    </div>
                </div>

                {{-- Progress Kuota --}}
                <div class="mb-5">
                    <div class="flex justify-between items-center text-sm mb-2">
                        <span class="text-[#A0A8C0]">Kuota Bimbingan</span>
                        <span class="font-semibold" style="color: {{ $isFull ? '#FF4D4D' : '#3DDC97' }}">
                            {{ $bimbinganAktif }}/{{ $kuota }}
                            <span class="text-[#A0A8C0] font-normal">(sisa {{ $sisa }})</span>
                        </span>
                    </div>
                    <div class="h-2.5 rounded-full overflow-hidden bg-[#3A4566]">
                        <div class="h-full rounded-full transition-all duration-300"
                             style="width: {{ $pct }}%; background-color: {{ $barColor }}"></div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-2">

                    {{-- Edit --}}
                    <button
                        type="button"
                        onclick="openEditDosenModal(this.closest('[data-id]'))"
                        class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl border border-[#3A4566] text-[#A0A8C0] hover:bg-[#2A3352] hover:text-white transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[14px] h-[14px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 4h4a2 2 0 012 2v4m-9.586 9.586L3 21l1.414-4.414L14 7l3 3-9.586 9.586z"/>
                        </svg>
                        <span class="text-sm">Edit</span>
                    </button>

                    {{-- Set Quota --}}
                    <button
                        type="button"
                        onclick="openQuotaModal(this.closest('[data-id]'))"
                        class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl border border-[rgba(77,163,255,0.3)] text-[#4DA3FF] bg-[rgba(77,163,255,0.05)] hover:bg-[rgba(77,163,255,0.12)] transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[14px] h-[14px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                        <span class="text-sm">Set Kuota</span>
                    </button>

                    {{-- Delete --}}
                    <button
                        type="button"
                        onclick="openDeleteDosenModal(this.closest('[data-id]'))"
                        class="w-11 h-11 rounded-xl border border-[rgba(255,77,77,0.3)] text-[#FF4D4D] bg-[rgba(255,77,77,0.05)] hover:bg-[rgba(255,77,77,0.12)] transition flex items-center justify-center"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[16px] h-[16px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m-7 0l1 12h6l1-12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-2 text-center py-16 text-[#A0A8C0]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m0 0a4 4 0 118 0"/>
                </svg>
                <p>Tidak ada data dosen ditemukan.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($dosen->hasPages())
        <div class="flex items-center justify-between px-2">
            <p class="text-sm text-[#A0A8C0]">
                Menampilkan {{ $dosen->firstItem() }}–{{ $dosen->lastItem() }}
                dari {{ $dosen->total() }} dosen
            </p>
            <div class="flex items-center gap-2">
                @if($dosen->onFirstPage())
                    <span class="text-[#3A4566]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </span>
                @else
                    <a href="{{ $dosen->previousPageUrl() }}" class="text-[#A0A8C0] hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                @endif

                @foreach($dosen->getUrlRange(1, $dosen->lastPage()) as $page => $url)
                    <a href="{{ $url }}"
                       class="w-9 h-9 rounded-xl text-sm flex items-center justify-center
                       {{ $page == $dosen->currentPage()
                           ? 'bg-[#0057B8] text-white font-semibold'
                           : 'text-[#A0A8C0] hover:bg-[#2A3352] hover:text-white' }}">
                        {{ $page }}
                    </a>
                @endforeach

                @if($dosen->hasMorePages())
                    <a href="{{ $dosen->nextPageUrl() }}" class="text-[#A0A8C0] hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @else
                    <span class="text-[#3A4566]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                @endif
            </div>
        </div>
    @endif
</div>

{{-- ============================================================ --}}
{{-- MODAL: ADD DOSEN --}}
{{-- ============================================================ --}}
<div id="addLecturerModal" class="hidden fixed inset-0 z-50 bg-[rgba(0,0,0,0.78)] flex items-center justify-center p-4">
    <div class="w-full max-w-3xl rounded-[28px] p-8 bg-[#242D45] border border-[#4B5780]">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-white text-2xl font-bold">Tambah Dosen</h2>
            <button type="button" onclick="document.getElementById('addLecturerModal').classList.add('hidden')" class="text-[#A0A8C0]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.dosen.store') }}">
            @csrf
            <div class="grid grid-cols-2 gap-5">

                <div class="col-span-2">
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Nama Lengkap <span class="text-red-400">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>

                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">NID <span class="text-red-400">*</span></label>
                    <input type="text" name="nid" value="{{ old('nid') }}" required
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>

                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Email <span class="text-red-400">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>

                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Password <span class="text-red-400">*</span></label>
                    <input type="password" name="password" required minlength="8"
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>

                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Kuota Bimbingan <span class="text-red-400">*</span></label>
                    <input type="number" name="kuota_bimbingan" value="{{ old('kuota_bimbingan', 0) }}" min="0" required
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>

                <div class="col-span-2">
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Bidang Keahlian</label>
                    <input type="text" name="bidang_keahlian" value="{{ old('bidang_keahlian') }}"
                        placeholder="contoh: Kecerdasan Buatan, Machine Learning"
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>

                <div class="col-span-2">
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Profil Singkat</label>
                    <textarea name="profil_singkat" rows="3"
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF] resize-none">{{ old('profil_singkat') }}</textarea>
                </div>
            </div>

            <div class="flex gap-4 mt-8">
                <button type="button"
                    onclick="document.getElementById('addLecturerModal').classList.add('hidden')"
                    class="flex-1 py-3 rounded-2xl border border-[#3A4566] text-[#A0A8C0] text-base">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-[#0057B8] text-white text-base font-semibold hover:bg-[#0046A0]">
                    Tambah Dosen
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: EDIT DOSEN --}}
{{-- ============================================================ --}}
<div id="editLecturerModal" class="hidden fixed inset-0 z-50 bg-[rgba(0,0,0,0.78)] flex items-center justify-center p-4">
    <div class="w-full max-w-3xl rounded-[28px] p-8 bg-[#242D45] border border-[#4B5780]">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-white text-2xl font-bold">Edit Dosen</h2>
            <button type="button" onclick="document.getElementById('editLecturerModal').classList.add('hidden')" class="text-[#A0A8C0]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="editDosenForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-5">

                <div class="col-span-2">
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Nama Lengkap <span class="text-red-400">*</span></label>
                    <input type="text" name="nama" id="edit_dosen_nama" required
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>

                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">NID <span class="text-red-400">*</span></label>
                    <input type="text" name="nid" id="edit_dosen_nid" required
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>

                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Email <span class="text-red-400">*</span></label>
                    <input type="email" name="email" id="edit_dosen_email" required
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>

                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Kuota Bimbingan <span class="text-red-400">*</span></label>
                    <input type="number" name="kuota_bimbingan" id="edit_dosen_kuota" min="0" required
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>

                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Status Akun <span class="text-red-400">*</span></label>
                    <select name="status_akun" id="edit_dosen_status" required
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Bidang Keahlian</label>
                    <input type="text" name="bidang_keahlian" id="edit_dosen_bidang"
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>

                <div class="col-span-2">
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Profil Singkat</label>
                    <textarea name="profil_singkat" id="edit_dosen_profil" rows="3"
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF] resize-none"></textarea>
                </div>
            </div>

            <div class="flex gap-4 mt-8">
                <button type="button"
                    onclick="document.getElementById('editLecturerModal').classList.add('hidden')"
                    class="flex-1 py-3 rounded-2xl border border-[#3A4566] text-[#A0A8C0] text-base">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-[#0057B8] text-white text-base font-semibold hover:bg-[#0046A0]">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: SET KUOTA --}}
{{-- ============================================================ --}}
<div id="quotaLecturerModal" class="hidden fixed inset-0 z-50 bg-[rgba(0,0,0,0.78)] flex items-center justify-center p-4">
    <div class="w-full max-w-2xl rounded-[28px] p-8 bg-[#242D45] border border-[#4B5780]">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-white text-2xl font-bold">Set Kuota Bimbingan</h2>
            <button type="button" onclick="document.getElementById('quotaLecturerModal').classList.add('hidden')" class="text-[#A0A8C0]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <p class="text-[#A0A8C0] text-sm mb-5">
            Setting kuota untuk:
            <span id="quota_nama" class="text-white font-semibold"></span>
        </p>

        {{-- Info Saat Ini --}}
        <div class="rounded-2xl bg-[#2A3352] p-5 mb-5 grid grid-cols-2 gap-4">
            <div>
                <p class="text-[#A0A8C0] text-xs mb-1">Bimbingan Aktif Saat Ini</p>
                <p id="quota_current" class="text-white text-xl font-bold">0</p>
            </div>
            <div>
                <p class="text-[#A0A8C0] text-xs mb-1">Sisa Slot</p>
                <p id="quota_sisa" class="text-[#3DDC97] text-xl font-bold">0</p>
            </div>
        </div>

        <form id="quotaForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Kuota Maksimal Baru <span class="text-red-400">*</span></label>
                <input type="number" name="kuota_bimbingan" id="quota_input"
                    min="0" required
                    class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                <p class="text-[#A0A8C0] text-xs mt-2">
                    * Kuota tidak bisa dikurangi di bawah jumlah bimbingan aktif saat ini.
                </p>
            </div>

            <div class="flex gap-4 mt-8">
                <button type="button"
                    onclick="document.getElementById('quotaLecturerModal').classList.add('hidden')"
                    class="flex-1 py-3 rounded-2xl border border-[#3A4566] text-[#A0A8C0] text-base">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-[#0057B8] text-white text-base font-semibold hover:bg-[#0046A0]">
                    Update Kuota
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: DELETE DOSEN --}}
{{-- ============================================================ --}}
<div id="deleteLecturerModal" class="hidden fixed inset-0 z-50 bg-[rgba(0,0,0,0.78)] flex items-center justify-center p-4">
    <div class="w-full max-w-xl rounded-[28px] p-8 bg-[#242D45] border border-[#4B5780]">
        <h2 class="text-white text-2xl font-bold mb-4">Konfirmasi Hapus</h2>
        <p class="text-[#A0A8C0] text-base leading-7 mb-2">
            Apakah Anda yakin ingin menghapus dosen
            <span id="delete_dosen_nama" class="text-white font-semibold"></span>?
        </p>
        <p class="text-[#FF4D4D] text-sm mb-8">Tindakan ini tidak dapat dibatalkan.</p>

        <form id="deleteDosenForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex gap-4">
                <button type="button"
                    onclick="document.getElementById('deleteLecturerModal').classList.add('hidden')"
                    class="flex-1 py-3 rounded-2xl border border-[#3A4566] text-[#A0A8C0] text-base">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-[#FF4D4D] text-white text-base font-semibold hover:bg-red-600">
                    Hapus
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ==================== EDIT MODAL ====================
    function openEditDosenModal(card) {
        const d = card.dataset;

        document.getElementById('edit_dosen_nama').value   = d.nama;
        document.getElementById('edit_dosen_nid').value    = d.nid;
        document.getElementById('edit_dosen_email').value  = d.email;
        document.getElementById('edit_dosen_kuota').value  = d.kuota;
        document.getElementById('edit_dosen_bidang').value = d.bidang;
        document.getElementById('edit_dosen_profil').value = d.profil;

        // Set status select
        const statusSelect = document.getElementById('edit_dosen_status');
        for (let opt of statusSelect.options) {
            opt.selected = opt.value === d.status;
        }

        // Pakai URL dari data attribute — bukan hardcode
        document.getElementById('editDosenForm').action = d.urlEdit;
        document.getElementById('editLecturerModal').classList.remove('hidden');
    }

    // ==================== QUOTA MODAL ====================
    function openQuotaModal(card) {
        const d = card.dataset;

        document.getElementById('quota_nama').textContent    = d.nama;
        document.getElementById('quota_current').textContent = d.bimbinganAktif;
        document.getElementById('quota_sisa').textContent    = d.sisa;
        document.getElementById('quota_input').value         = d.kuota;
        document.getElementById('quota_input').min           = d.bimbinganAktif;

        // Pakai URL dari data attribute
        document.getElementById('quotaForm').action = d.urlKuota;
        document.getElementById('quotaLecturerModal').classList.remove('hidden');
    }

    // ==================== DELETE MODAL ====================
    function openDeleteDosenModal(card) {
        const d = card.dataset;

        document.getElementById('delete_dosen_nama').textContent = d.nama;

        // Pakai URL dari data attribute
        document.getElementById('deleteDosenForm').action = d.urlDelete;
        document.getElementById('deleteLecturerModal').classList.remove('hidden');
    }
</script>
@endpush
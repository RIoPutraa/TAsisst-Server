@extends('layout.admin')

@section('title', 'Informasi TA - TAssist Admin')
@section('page-title', 'Informasi TA')
@section('page-subtitle', 'Kelola informasi dan pengumuman Tugas Akhir')

@section('content')
<div class="space-y-5">

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <div class="rounded-[24px] p-5 flex items-center gap-4 bg-[#242D45] border border-[#3A4566]">
            <div class="w-16 h-16 rounded-2xl bg-[rgba(0,87,184,0.15)] flex items-center justify-center text-[#0057B8] flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h7l5 5v11a1 1 0 01-1 1H7a1 1 0 01-1-1V5a1 1 0 011-1z"/>
                </svg>
            </div>
            <div>
                <p class="text-white text-4xl font-extrabold leading-none">{{ $totalInfo }}</p>
                <p class="text-[#A0A8C0] text-sm mt-2">Total Informasi</p>
            </div>
        </div>

        <div class="rounded-[24px] p-5 flex items-center gap-4 bg-[#242D45] border border-[#3A4566]">
            <div class="w-16 h-16 rounded-2xl bg-[rgba(61,220,151,0.15)] flex items-center justify-center text-[#3DDC97] flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="text-white text-4xl font-extrabold leading-none">{{ $published }}</p>
                <p class="text-[#A0A8C0] text-sm mt-2">Published</p>
            </div>
        </div>

        <div class="rounded-[24px] p-5 flex items-center gap-4 bg-[#242D45] border border-[#3A4566]">
            <div class="w-16 h-16 rounded-2xl bg-[rgba(255,185,0,0.15)] flex items-center justify-center text-[#FFB900] flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <p class="text-white text-4xl font-extrabold leading-none">{{ $drafts }}</p>
                <p class="text-[#A0A8C0] text-sm mt-2">Draft</p>
            </div>
        </div>
    </div>

    {{-- Top Bar Filter --}}
    <form method="GET" action="{{ route('admin.informasi-ta.index') }}" id="filterForm">
        <div class="flex flex-wrap items-center gap-4">

            {{-- Search --}}
            <div class="relative flex-1 min-w-[320px]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-[#A0A8C0]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari judul atau konten..."
                    onchange="this.form.submit()"
                    class="w-full pl-12 pr-4 py-4 rounded-3xl bg-[#242D45] border border-[#3A4566] text-sm text-white outline-none placeholder:text-[#7F89A8] focus:border-[#4DA3FF]"
                >
            </div>

            {{-- Filter Kategori --}}
            <select name="kategori" onchange="this.form.submit()"
                class="min-w-[170px] px-4 py-4 rounded-3xl bg-[#242D45] border border-[#3A4566] text-sm text-[#A0A8C0] outline-none">
                <option value="">Semua Kategori</option>
                @foreach($kategoriList as $kat)
                    <option value="{{ $kat }}" {{ request('kategori') === $kat ? 'selected' : '' }}>
                        {{ $kat }}
                    </option>
                @endforeach
            </select>

            {{-- Filter Status --}}
            <select name="status" onchange="this.form.submit()"
                class="min-w-[170px] px-4 py-4 rounded-3xl bg-[#242D45] border border-[#3A4566] text-sm text-[#A0A8C0] outline-none">
                <option value="">Semua Status</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft"     {{ request('status') === 'draft'     ? 'selected' : '' }}>Draft</option>
            </select>

            {{-- Tombol Add --}}
            <button
                type="button"
                onclick="document.getElementById('addInfoModal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-6 py-4 rounded-3xl bg-[#0057B8] text-white font-semibold hover:bg-[#0046A0] transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Info
            </button>
        </div>
    </form>

    {{-- Table --}}
    <div class="rounded-[26px] overflow-hidden border border-[#3A4566]">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-[#242D45]">
                        <th class="px-5 py-5 text-left text-xs font-semibold text-[#A0A8C0] border-b border-[#3A4566]">No</th>
                        <th class="px-5 py-5 text-left text-xs font-semibold text-[#A0A8C0] border-b border-[#3A4566]">Judul</th>
                        <th class="px-5 py-5 text-left text-xs font-semibold text-[#A0A8C0] border-b border-[#3A4566]">Kategori</th>
                        <th class="px-5 py-5 text-left text-xs font-semibold text-[#A0A8C0] border-b border-[#3A4566]">Konten</th>
                        <th class="px-5 py-5 text-left text-xs font-semibold text-[#A0A8C0] border-b border-[#3A4566]">Tanggal Publish</th>
                        <th class="px-5 py-5 text-left text-xs font-semibold text-[#A0A8C0] border-b border-[#3A4566]">Status</th>
                        <th class="px-5 py-5 text-left text-xs font-semibold text-[#A0A8C0] border-b border-[#3A4566]">Publish</th>
                        <th class="px-5 py-5 text-left text-xs font-semibold text-[#A0A8C0] border-b border-[#3A4566]">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($infos as $index => $info)
                        @php
                            $isPublished = $info->published_at && $info->published_at <= now();
                        @endphp

                        <tr class="{{ $index % 2 === 0 ? 'bg-[#1A2035]' : 'bg-[#242D45]' }} border-b border-[#3A4566]"
                            data-id="{{ $info->info_id }}"
                            data-judul="{{ $info->judul }}"
                            data-kategori="{{ $info->kategori }}"
                            data-konten="{{ addslashes($info->konten_or_file) }}"
                            data-published-at="{{ $info->published_at?->format('Y-m-d') ?? '' }}"
                            data-is-published="{{ $isPublished ? '1' : '0' }}"
                            data-admin="{{ $info->admin->user->nama ?? '-' }}"
                            data-url-update="{{ route('admin.informasi-ta.update', $info->info_id) }}"
                            data-url-delete="{{ route('admin.informasi-ta.destroy', $info->info_id) }}"
                            data-url-toggle="{{ route('admin.informasi-ta.toggle', $info->info_id) }}">

                            {{-- No --}}
                            <td class="px-5 py-5 text-sm text-[#A0A8C0]">
                                {{ $infos->firstItem() + $index }}
                            </td>

                            {{-- Judul + Author --}}
                            <td class="px-5 py-5 max-w-[240px]">
                                <p class="text-white text-sm font-medium truncate">{{ $info->judul }}</p>
                                <p class="text-xs text-[#A0A8C0]">by {{ $info->admin->user->nama ?? '-' }}</p>
                            </td>

                            {{-- Kategori --}}
                            <td class="px-5 py-5">
                                <span class="px-2 py-1 rounded-lg text-xs font-medium bg-[rgba(77,163,255,0.15)] text-[#4DA3FF]">
                                    {{ $info->kategori }}
                                </span>
                            </td>

                            {{-- Konten --}}
                            <td class="px-5 py-5 max-w-[300px]">
                                <p class="text-sm text-[#A0A8C0] line-clamp-2">
                                    {{ Str::limit($info->konten_or_file, 100) }}
                                </p>
                            </td>

                            {{-- Tanggal Publish --}}
                            <td class="px-5 py-5">
                                <div class="flex items-center gap-2 text-[#A0A8C0]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-[14px] h-[14px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm">
                                        {{ $info->published_at ? $info->published_at->format('d M Y') : '-' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-5 py-5">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    {{ $isPublished
                                        ? 'bg-[rgba(61,220,151,0.15)] text-[#3DDC97]'
                                        : 'bg-[rgba(255,185,0,0.15)] text-[#FFB900]' }}">
                                    {{ $isPublished ? 'Published' : 'Draft' }}
                                </span>
                            </td>

                            {{-- Toggle Publish --}}
                            <td class="px-5 py-5">
                                <form method="POST"
                                      action="{{ route('admin.informasi-ta.toggle', $info->info_id) }}"
                                      class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="{{ $isPublished ? 'text-[#3DDC97]' : 'text-[#A0A8C0]' }} hover:opacity-80 transition-opacity"
                                        title="{{ $isPublished ? 'Klik untuk unpublish' : 'Klik untuk publish' }}">
                                        @if($isPublished)
                                            {{-- Toggle ON --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-7" viewBox="0 0 40 24" fill="none">
                                                <rect x="1.5" y="1.5" width="37" height="21" rx="10.5" stroke="currentColor" stroke-width="3"/>
                                                <circle cx="28" cy="12" r="5" fill="currentColor"/>
                                            </svg>
                                        @else
                                            {{-- Toggle OFF --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-7" viewBox="0 0 40 24" fill="none">
                                                <rect x="1.5" y="1.5" width="37" height="21" rx="10.5" stroke="currentColor" stroke-width="3"/>
                                                <circle cx="12" cy="12" r="5" fill="currentColor"/>
                                            </svg>
                                        @endif
                                    </button>
                                </form>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-5 py-5">
                                <div class="flex items-center gap-3">

                                    {{-- View --}}
                                    <button type="button"
                                        onclick="openViewInfoModal(this.closest('tr'))"
                                        class="text-[#4DA3FF] hover:opacity-80" title="Lihat">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>

                                    {{-- Edit --}}
                                    <button type="button"
                                        onclick="openEditInfoModal(this.closest('tr'))"
                                        class="text-[#FFB900] hover:opacity-80" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 4h4a2 2 0 012 2v4m-9.586 9.586L3 21l1.414-4.414L14 7l3 3-9.586 9.586z"/>
                                        </svg>
                                    </button>

                                    {{-- Delete --}}
                                    <button type="button"
                                        onclick="openDeleteInfoModal(this.closest('tr'))"
                                        class="text-[#FF4D4D] hover:opacity-80" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m-7 0l1 12h6l1-12"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-[#A0A8C0] text-sm bg-[#1A2035]">
                                Tidak ada informasi TA ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($infos->hasPages())
            <div class="flex items-center justify-between px-5 py-4 bg-[#242D45] border-t border-[#3A4566]">
                <p class="text-sm text-[#A0A8C0]">
                    Menampilkan {{ $infos->firstItem() }}–{{ $infos->lastItem() }}
                    dari {{ $infos->total() }} informasi
                </p>
                <div class="flex items-center gap-2">
                    @if($infos->onFirstPage())
                        <span class="text-[#3A4566]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $infos->previousPageUrl() }}" class="text-[#A0A8C0] hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    @foreach($infos->getUrlRange(1, $infos->lastPage()) as $page => $url)
                        <a href="{{ $url }}"
                           class="w-9 h-9 rounded-xl text-sm flex items-center justify-center
                           {{ $page == $infos->currentPage()
                               ? 'bg-[#0057B8] text-white font-semibold'
                               : 'text-[#A0A8C0] hover:bg-[#2A3352] hover:text-white' }}">
                            {{ $page }}
                        </a>
                    @endforeach

                    @if($infos->hasMorePages())
                        <a href="{{ $infos->nextPageUrl() }}" class="text-[#A0A8C0] hover:text-white">
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
</div>

{{-- ============================================================ --}}
{{-- MODAL: VIEW INFO --}}
{{-- ============================================================ --}}
<div id="viewInfoModal" class="hidden fixed inset-0 z-50 bg-[rgba(0,0,0,0.78)] flex items-center justify-center p-4">
    <div class="w-full max-w-4xl rounded-[28px] p-8 bg-[#242D45] border border-[#4B5780]">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-white text-2xl font-bold">Detail Informasi TA</h2>
            <button type="button" onclick="document.getElementById('viewInfoModal').classList.add('hidden')" class="text-[#A0A8C0]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="space-y-5">
            <div class="grid grid-cols-2 gap-5">
                <div class="col-span-2">
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Judul</label>
                    <input type="text" id="view_info_judul" disabled
                        class="w-full px-4 py-4 rounded-2xl bg-[#1A2035] border border-[#2A3352] text-white/80 outline-none">
                </div>
                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Kategori</label>
                    <input type="text" id="view_info_kategori" disabled
                        class="w-full px-4 py-4 rounded-2xl bg-[#1A2035] border border-[#2A3352] text-white/80 outline-none">
                </div>
                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Tanggal Publish</label>
                    <input type="text" id="view_info_date" disabled
                        class="w-full px-4 py-4 rounded-2xl bg-[#1A2035] border border-[#2A3352] text-white/80 outline-none">
                </div>
                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Status</label>
                    <input type="text" id="view_info_status" disabled
                        class="w-full px-4 py-4 rounded-2xl bg-[#1A2035] border border-[#2A3352] text-white/80 outline-none">
                </div>
                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Dibuat Oleh</label>
                    <input type="text" id="view_info_admin" disabled
                        class="w-full px-4 py-4 rounded-2xl bg-[#1A2035] border border-[#2A3352] text-white/80 outline-none">
                </div>
                <div class="col-span-2">
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Konten</label>
                    <textarea id="view_info_konten" rows="6" disabled
                        class="w-full px-4 py-4 rounded-2xl bg-[#1A2035] border border-[#2A3352] text-white/80 outline-none resize-none"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: ADD INFO --}}
{{-- ============================================================ --}}
<div id="addInfoModal" class="hidden fixed inset-0 z-50 bg-[rgba(0,0,0,0.78)] flex items-center justify-center p-4">
    <div class="w-full max-w-4xl rounded-[28px] p-8 bg-[#242D45] border border-[#4B5780]">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-white text-2xl font-bold">Tambah Informasi TA</h2>
            <button type="button" onclick="document.getElementById('addInfoModal').classList.add('hidden')" class="text-[#A0A8C0]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.informasi-ta.store') }}">
            @csrf
            <div class="space-y-5">

                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Judul <span class="text-red-400">*</span></label>
                    <input type="text" name="judul" value="{{ old('judul') }}" required
                        placeholder="Masukkan judul informasi..."
                        class="w-full px-4 py-4 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Kategori <span class="text-red-400">*</span></label>
                        <input type="text" name="kategori" value="{{ old('kategori') }}" required
                            placeholder="contoh: Pengumuman, Panduan, Jadwal"
                            class="w-full px-4 py-4 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                    </div>
                    <div>
                        <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Tanggal Publish</label>
                        <input type="date" name="published_at" value="{{ old('published_at') }}"
                            class="w-full px-4 py-4 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF] [color-scheme:dark]">
                    </div>
                </div>

                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Konten <span class="text-red-400">*</span></label>
                    <textarea name="konten_or_file" rows="6" required
                        placeholder="Tulis konten informasi TA..."
                        class="w-full px-4 py-4 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none resize-none focus:border-[#4DA3FF]">{{ old('konten_or_file') }}</textarea>
                </div>

                {{-- Checkbox publish sekarang --}}
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_published" id="add_is_published" value="1"
                        class="w-4 h-4 rounded accent-[#0057B8]">
                    <label for="add_is_published" class="text-[#A0A8C0] text-sm">
                        Publish sekarang
                        <span class="text-[#7F89A8] text-xs">(jika tidak dicentang, tersimpan sebagai draft)</span>
                    </label>
                </div>

                <div class="flex gap-4 pt-2">
                    <button type="button"
                        onclick="document.getElementById('addInfoModal').classList.add('hidden')"
                        class="flex-1 py-4 rounded-2xl border border-[#3A4566] text-[#A0A8C0] text-base">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-4 rounded-2xl bg-[#0057B8] text-white text-base font-semibold hover:bg-[#0046A0]">
                        Tambah Informasi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: EDIT INFO --}}
{{-- ============================================================ --}}
<div id="editInfoModal" class="hidden fixed inset-0 z-50 bg-[rgba(0,0,0,0.78)] flex items-center justify-center p-4">
    <div class="w-full max-w-4xl rounded-[28px] p-8 bg-[#242D45] border border-[#4B5780]">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-white text-2xl font-bold">Edit Informasi TA</h2>
            <button type="button" onclick="document.getElementById('editInfoModal').classList.add('hidden')" class="text-[#A0A8C0]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="editInfoForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="space-y-5">

                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Judul <span class="text-red-400">*</span></label>
                    <input type="text" name="judul" id="edit_info_judul" required
                        class="w-full px-4 py-4 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Kategori <span class="text-red-400">*</span></label>
                        <input type="text" name="kategori" id="edit_info_kategori" required
                            class="w-full px-4 py-4 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                    </div>
                    <div>
                        <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Tanggal Publish</label>
                        <input type="date" name="published_at" id="edit_info_date"
                            class="w-full px-4 py-4 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF] [color-scheme:dark]">
                    </div>
                </div>

                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Konten <span class="text-red-400">*</span></label>
                    <textarea name="konten_or_file" id="edit_info_konten" rows="6" required
                        class="w-full px-4 py-4 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none resize-none focus:border-[#4DA3FF]"></textarea>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_published" id="edit_is_published" value="1"
                        class="w-4 h-4 rounded accent-[#0057B8]">
                    <label for="edit_is_published" class="text-[#A0A8C0] text-sm">
                        Publish sekarang
                    </label>
                </div>

                <div class="flex gap-4 pt-2">
                    <button type="button"
                        onclick="document.getElementById('editInfoModal').classList.add('hidden')"
                        class="flex-1 py-4 rounded-2xl border border-[#3A4566] text-[#A0A8C0] text-base">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-4 rounded-2xl bg-[#0057B8] text-white text-base font-semibold hover:bg-[#0046A0]">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: DELETE INFO --}}
{{-- ============================================================ --}}
<div id="deleteInfoModal" class="hidden fixed inset-0 z-50 bg-[rgba(0,0,0,0.78)] flex items-center justify-center p-4">
    <div class="w-full max-w-2xl rounded-[28px] p-8 bg-[#242D45] border border-[#4B5780]">
        <h2 class="text-white text-2xl font-bold mb-4">Konfirmasi Hapus</h2>
        <p class="text-[#A0A8C0] text-base leading-7 mb-2">
            Apakah Anda yakin ingin menghapus informasi
            <span id="delete_info_judul" class="text-white font-semibold"></span>?
        </p>
        <p class="text-[#FF4D4D] text-sm mb-8">Tindakan ini tidak dapat dibatalkan.</p>

        <form id="deleteInfoForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex gap-4">
                <button type="button"
                    onclick="document.getElementById('deleteInfoModal').classList.add('hidden')"
                    class="flex-1 py-4 rounded-2xl border border-[#3A4566] text-[#A0A8C0] text-base">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-4 rounded-2xl bg-[#FF4D4D] text-white text-base font-semibold hover:bg-red-600">
                    Hapus
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ==================== VIEW MODAL ====================
    function openViewInfoModal(row) {
        const d = row.dataset;
        document.getElementById('view_info_judul').value    = d.judul;
        document.getElementById('view_info_kategori').value = d.kategori;
        document.getElementById('view_info_konten').value   = d.konten;
        document.getElementById('view_info_date').value     = d.publishedAt || '-';
        document.getElementById('view_info_status').value   = d.isPublished === '1' ? 'Published' : 'Draft';
        document.getElementById('view_info_admin').value    = d.admin;
        document.getElementById('viewInfoModal').classList.remove('hidden');
    }

    // ==================== EDIT MODAL ====================
    function openEditInfoModal(row) {
        const d = row.dataset;
        document.getElementById('edit_info_judul').value    = d.judul;
        document.getElementById('edit_info_kategori').value = d.kategori;
        document.getElementById('edit_info_konten').value   = d.konten;
        document.getElementById('edit_info_date').value     = d.publishedAt || '';

        // Set checkbox is_published
        document.getElementById('edit_is_published').checked = d.isPublished === '1';

        // Set form action
        document.getElementById('editInfoForm').action = d.urlUpdate;
        document.getElementById('editInfoModal').classList.remove('hidden');
    }

    // ==================== DELETE MODAL ====================
    function openDeleteInfoModal(row) {
        const d = row.dataset;
        document.getElementById('delete_info_judul').textContent = d.judul;
        document.getElementById('deleteInfoForm').action         = d.urlDelete;
        document.getElementById('deleteInfoModal').classList.remove('hidden');
    }
</script>
@endpush
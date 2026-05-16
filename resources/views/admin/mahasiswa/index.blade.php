@extends('layout.admin')

@section('title', 'Mahasiswa - TAssist Admin')
@section('page-title', 'Manajemen Mahasiswa')
@section('page-subtitle', 'Kelola data mahasiswa')

@section('content')
<div class="space-y-5">

    {{-- Top Filter --}}
    <form method="GET" action="{{ route('admin.mahasiswa.index') }}" id="filterForm">
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
                    placeholder="Cari nama, NIM, atau email..."
                    onchange="this.form.submit()"
                    class="w-full pl-11 pr-4 py-3 rounded-2xl bg-[#242D45] border border-[#3A4566] text-sm text-white outline-none placeholder:text-[#7F89A8] focus:border-[#4DA3FF]"
                >
            </div>

            {{-- Filter Prodi --}}
            <select
                name="prodi"
                onchange="this.form.submit()"
                class="min-w-[200px] px-4 py-3 rounded-2xl bg-[#242D45] border border-[#3A4566] text-sm text-[#A0A8C0] outline-none"
            >
                <option value="">Semua Prodi</option>
                <option value="Teknik Informatika"   {{ request('prodi') === 'Teknik Informatika'   ? 'selected' : '' }}>Teknik Informatika</option>
                <option value="Sistem Informasi"     {{ request('prodi') === 'Sistem Informasi'     ? 'selected' : '' }}>Sistem Informasi</option>
                <option value="Teknik Elektro"       {{ request('prodi') === 'Teknik Elektro'       ? 'selected' : '' }}>Teknik Elektro</option>
            </select>

            {{-- Filter Angkatan --}}
            <select
                name="angkatan"
                onchange="this.form.submit()"
                class="min-w-[160px] px-4 py-3 rounded-2xl bg-[#242D45] border border-[#3A4566] text-sm text-[#A0A8C0] outline-none"
            >
                <option value="">Semua Angkatan</option>
                @for($y = date('Y'); $y >= 2018; $y--)
                    <option value="{{ $y }}" {{ request('angkatan') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>

            {{-- Tombol Add --}}
            <button
                type="button"
                onclick="document.getElementById('addStudentModal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-[#0057B8] text-white font-semibold hover:bg-[#0046A0] transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Mahasiswa
            </button>
        </div>
    </form>

    {{-- Table --}}
    <div class="rounded-[24px] overflow-hidden border border-[#3A4566]">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-[#242D45]">
                        <th class="px-5 py-5 text-left text-xs font-semibold text-[#A0A8C0] border-b border-[#3A4566]">No</th>
                        <th class="px-5 py-5 text-left text-xs font-semibold text-[#A0A8C0] border-b border-[#3A4566]">Nama</th>
                        <th class="px-5 py-5 text-left text-xs font-semibold text-[#A0A8C0] border-b border-[#3A4566]">NIM</th>
                        <th class="px-5 py-5 text-left text-xs font-semibold text-[#A0A8C0] border-b border-[#3A4566]">Prodi</th>
                        <th class="px-5 py-5 text-left text-xs font-semibold text-[#A0A8C0] border-b border-[#3A4566]">Angkatan</th>
                        <th class="px-5 py-5 text-left text-xs font-semibold text-[#A0A8C0] border-b border-[#3A4566]">Status Akun</th>
                        <th class="px-5 py-5 text-left text-xs font-semibold text-[#A0A8C0] border-b border-[#3A4566]">Aksi</th>
                    </tr>
                </thead>
                <tbody>
    @forelse($mahasiswa as $index => $m)
        {{-- Tambahkan data-* attribute di <tr> untuk openViewModal --}}
        <tr class="{{ $index % 2 === 0 ? 'bg-[#1A2035]' : 'bg-[#242D45]' }} border-b border-[#3A4566]"
            data-id="{{ $m->mahasiswa_id }}"
            data-nama="{{ $m->user->nama }}"
            data-email="{{ $m->user->email }}"
            data-nim="{{ $m->nim }}"
            data-prodi="{{ $m->prodi }}"
            data-angkatan="{{ $m->angkatan }}"
            data-topik="{{ $m->topik_ta ?? '' }}"
            data-judul="{{ $m->judul_ta ?? '' }}"
            data-status="{{ $m->user->status_akun }}">

            {{-- No --}}
            <td class="px-5 py-5 text-sm text-[#A0A8C0]">
                {{ $mahasiswa->firstItem() + $index }}
            </td>

            {{-- Nama + Email --}}
            <td class="px-5 py-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#0057B8] text-white text-sm font-bold flex items-center justify-center flex-shrink-0">
                        {{ strtoupper(substr($m->user->nama, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-white text-sm font-medium">{{ $m->user->nama }}</p>
                        <p class="text-xs text-[#A0A8C0]">{{ $m->user->email }}</p>
                    </div>
                </div>
            </td>

            {{-- NIM --}}
            <td class="px-5 py-5 text-sm text-[#A0A8C0]">{{ $m->nim }}</td>

            {{-- Prodi --}}
            <td class="px-5 py-5 text-sm text-[#A0A8C0]">{{ $m->prodi }}</td>

            {{-- Angkatan --}}
            <td class="px-5 py-5 text-sm text-[#A0A8C0]">{{ $m->angkatan }}</td>

            {{-- Status --}}
            <td class="px-5 py-5">
                <span class="px-3 py-1 rounded-full text-xs font-medium
                    {{ $m->user->status_akun === 'aktif'
                        ? 'bg-[rgba(0,200,100,0.15)] text-[#00C864]'
                        : 'bg-[rgba(255,77,77,0.15)] text-[#FF4D4D]' }}">
                    {{ ucfirst($m->user->status_akun) }}
                </span>
            </td>

            {{-- Aksi --}}
            <td class="px-5 py-5">
                <div class="flex items-center gap-3">

                    {{-- View — kirim element <tr> nya --}}
                    <button
                        type="button"
                        onclick="openViewModal(this.closest('tr'))"
                        class="text-[#4DA3FF] hover:opacity-80"
                        title="Lihat Detail"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>

                    {{-- Edit — kirim element <tr> nya --}}
                    <button
                        type="button"
                        onclick="openEditModal(this.closest('tr'), '{{ route('admin.mahasiswa.update', $m->mahasiswa_id) }}')"
                        class="text-[#FFB900] hover:opacity-80"
                        title="Edit"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 4h4a2 2 0 012 2v4m-9.586 9.586L3 21l1.414-4.414L14 7l3 3-9.586 9.586z"/>
                        </svg>
                    </button>

                    {{-- Delete — kirim element <tr> nya --}}
                    <button
                        type="button"
                        onclick="openDeleteModal(this.closest('tr'), '{{ route('admin.mahasiswa.destroy', $m->mahasiswa_id) }}')"
                        class="text-[#FF4D4D] hover:opacity-80"
                        title="Hapus"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m-7 0l1 12h6l1-12"/>
                        </svg>
                    </button>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="px-5 py-12 text-center text-[#A0A8C0] text-sm bg-[#1A2035]">
                Tidak ada data mahasiswa ditemukan.
            </td>
        </tr>
    @endforelse
</tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="flex items-center justify-between px-5 py-4 bg-[#242D45] border-t border-[#3A4566]">
            <p class="text-sm text-[#A0A8C0]">
                Menampilkan {{ $mahasiswa->firstItem() ?? 0 }}–{{ $mahasiswa->lastItem() ?? 0 }}
                dari {{ $mahasiswa->total() }} mahasiswa
            </p>
            <div class="flex items-center gap-2">
                {{-- Prev --}}
                @if($mahasiswa->onFirstPage())
                    <span class="text-[#3A4566]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </span>
                @else
                    <a href="{{ $mahasiswa->previousPageUrl() }}" class="text-[#A0A8C0] hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                @endif

                {{-- Page Numbers --}}
                @foreach($mahasiswa->getUrlRange(1, $mahasiswa->lastPage()) as $page => $url)
                    <a href="{{ $url }}"
                       class="w-9 h-9 rounded-xl text-sm flex items-center justify-center
                       {{ $page == $mahasiswa->currentPage()
                           ? 'bg-[#0057B8] text-white font-semibold'
                           : 'text-[#A0A8C0] hover:bg-[#2A3352] hover:text-white' }}">
                        {{ $page }}
                    </a>
                @endforeach

                {{-- Next --}}
                @if($mahasiswa->hasMorePages())
                    <a href="{{ $mahasiswa->nextPageUrl() }}" class="text-[#A0A8C0] hover:text-white">
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
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: ADD STUDENT --}}
{{-- ============================================================ --}}
<div id="addStudentModal" class="hidden fixed inset-0 z-50 bg-[rgba(0,0,0,0.78)] flex items-center justify-center p-4">
    <div class="w-full max-w-3xl rounded-[28px] p-8 bg-[#242D45] border border-[#4B5780]">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-white text-2xl font-bold">Tambah Mahasiswa</h2>
            <button type="button" onclick="document.getElementById('addStudentModal').classList.add('hidden')" class="text-[#A0A8C0]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.mahasiswa.store') }}">
            @csrf
            <div class="grid grid-cols-2 gap-5">

                <div class="col-span-2">
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Nama Lengkap <span class="text-red-400">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>

                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">NIM <span class="text-red-400">*</span></label>
                    <input type="text" name="nim" value="{{ old('nim') }}" required
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
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Angkatan <span class="text-red-400">*</span></label>
                    <input type="number" name="angkatan" value="{{ old('angkatan') }}" min="2000" max="{{ date('Y') }}" required
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>

                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Program Studi <span class="text-red-400">*</span></label>
                    <select name="prodi" required
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                        <option value="">Pilih Prodi</option>
                        <option value="Teknik Informatika"  {{ old('prodi') === 'Teknik Informatika'  ? 'selected' : '' }}>Teknik Informatika</option>
                        <option value="Sistem Informasi"    {{ old('prodi') === 'Sistem Informasi'    ? 'selected' : '' }}>Sistem Informasi</option>
                        <option value="Teknik Elektro"      {{ old('prodi') === 'Teknik Elektro'      ? 'selected' : '' }}>Teknik Elektro</option>
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Topik TA</label>
                    <input type="text" name="topik_ta" value="{{ old('topik_ta') }}"
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>

                <div class="col-span-2">
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Judul TA</label>
                    <input type="text" name="judul_ta" value="{{ old('judul_ta') }}"
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>
            </div>

            <div class="flex gap-4 mt-8">
                <button type="button"
                    onclick="document.getElementById('addStudentModal').classList.add('hidden')"
                    class="flex-1 py-3 rounded-2xl border border-[#3A4566] text-[#A0A8C0] text-base">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-[#0057B8] text-white text-base font-semibold hover:bg-[#0046A0]">
                    Tambah Mahasiswa
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: VIEW STUDENT --}}
{{-- ============================================================ --}}
<div id="viewStudentModal" class="hidden fixed inset-0 z-50 bg-[rgba(0,0,0,0.78)] flex items-center justify-center p-4">
    <div class="w-full max-w-3xl rounded-[28px] p-8 bg-[#242D45] border border-[#4B5780]">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-white text-2xl font-bold">Detail Mahasiswa</h2>
            <button type="button" onclick="document.getElementById('viewStudentModal').classList.add('hidden')" class="text-[#A0A8C0]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div class="col-span-2">
                <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Nama Lengkap</label>
                <input type="text" id="view_nama" disabled class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white/80 outline-none">
            </div>
            <div>
                <label class="block text-[#A0A8C0] text-sm font-medium mb-2">NIM</label>
                <input type="text" id="view_nim" disabled class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white/80 outline-none">
            </div>
            <div>
                <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Email</label>
                <input type="text" id="view_email" disabled class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white/80 outline-none">
            </div>
            <div>
                <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Angkatan</label>
                <input type="text" id="view_angkatan" disabled class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white/80 outline-none">
            </div>
            <div>
                <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Program Studi</label>
                <input type="text" id="view_prodi" disabled class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white/80 outline-none">
            </div>
            <div class="col-span-2">
                <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Topik TA</label>
                <input type="text" id="view_topik" disabled class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white/80 outline-none">
            </div>
            <div class="col-span-2">
                <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Judul TA</label>
                <input type="text" id="view_judul" disabled class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white/80 outline-none">
            </div>
            <div>
                <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Status Akun</label>
                <input type="text" id="view_status" disabled class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white/80 outline-none">
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: EDIT STUDENT --}}
{{-- ============================================================ --}}
<div id="editStudentModal" class="hidden fixed inset-0 z-50 bg-[rgba(0,0,0,0.78)] flex items-center justify-center p-4">
    <div class="w-full max-w-3xl rounded-[28px] p-8 bg-[#242D45] border border-[#4B5780]">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-white text-2xl font-bold">Edit Mahasiswa</h2>
            <button type="button" onclick="document.getElementById('editStudentModal').classList.add('hidden')" class="text-[#A0A8C0]">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="editForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-5">

                <div class="col-span-2">
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Nama Lengkap <span class="text-red-400">*</span></label>
                    <input type="text" name="nama" id="edit_nama" required
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>

                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">NIM <span class="text-red-400">*</span></label>
                    <input type="text" name="nim" id="edit_nim" required
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>

                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Email <span class="text-red-400">*</span></label>
                    <input type="email" name="email" id="edit_email" required
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>

                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Angkatan <span class="text-red-400">*</span></label>
                    <input type="number" name="angkatan" id="edit_angkatan" min="2000" max="{{ date('Y') }}" required
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>

                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Program Studi <span class="text-red-400">*</span></label>
                    <select name="prodi" id="edit_prodi" required
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                        <option value="Teknik Informatika">Teknik Informatika</option>
                        <option value="Sistem Informasi">Sistem Informasi</option>
                        <option value="Teknik Elektro">Teknik Elektro</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Status Akun <span class="text-red-400">*</span></label>
                    <select name="status_akun" id="edit_status_akun" required
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Topik TA</label>
                    <input type="text" name="topik_ta" id="edit_topik_ta"
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>

                <div class="col-span-2">
                    <label class="block text-[#A0A8C0] text-sm font-medium mb-2">Judul TA</label>
                    <input type="text" name="judul_ta" id="edit_judul_ta"
                        class="w-full px-4 py-3 rounded-2xl bg-[#2A3352] border border-[#3A4566] text-white outline-none focus:border-[#4DA3FF]">
                </div>
            </div>

            <div class="flex gap-4 mt-8">
                <button type="button"
                    onclick="document.getElementById('editStudentModal').classList.add('hidden')"
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
{{-- MODAL: DELETE STUDENT --}}
{{-- ============================================================ --}}
<div id="deleteStudentModal" class="hidden fixed inset-0 z-50 bg-[rgba(0,0,0,0.78)] flex items-center justify-center p-4">
    <div class="w-full max-w-xl rounded-[28px] p-8 bg-[#242D45] border border-[#4B5780]">
        <h2 class="text-white text-2xl font-bold mb-4">Konfirmasi Hapus</h2>
        <p class="text-[#A0A8C0] text-base leading-7 mb-2">
            Apakah Anda yakin ingin menghapus mahasiswa
            <span id="delete_nama" class="text-white font-semibold"></span>?
        </p>
        <p class="text-[#FF4D4D] text-sm mb-8">Tindakan ini tidak dapat dibatalkan.</p>

        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex gap-4">
                <button type="button"
                    onclick="document.getElementById('deleteStudentModal').classList.add('hidden')"
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
    // ==================== VIEW MODAL ====================
    function openViewModal(row) {
        const d = row.dataset;
        document.getElementById('view_nama').value     = d.nama;
        document.getElementById('view_nim').value      = d.nim;
        document.getElementById('view_email').value    = d.email;
        document.getElementById('view_angkatan').value = d.angkatan;
        document.getElementById('view_prodi').value    = d.prodi;
        document.getElementById('view_topik').value    = d.topik || '-';
        document.getElementById('view_judul').value    = d.judul || '-';
        document.getElementById('view_status').value   = d.status;
        document.getElementById('viewStudentModal').classList.remove('hidden');
    }

    // ==================== EDIT MODAL ====================
    function openEditModal(row, urlEdit) {
        const d = row.dataset;

        document.getElementById('edit_nama').value     = d.nama;
        document.getElementById('edit_email').value    = d.email;
        document.getElementById('edit_nim').value      = d.nim;
        document.getElementById('edit_angkatan').value = d.angkatan;
        document.getElementById('edit_topik_ta').value = d.topik;
        document.getElementById('edit_judul_ta').value = d.judul;

        // Set prodi select
        const prodiSelect = document.getElementById('edit_prodi');
        for (let opt of prodiSelect.options) {
            opt.selected = opt.value === d.prodi;
        }

        // Set status select
        const statusSelect = document.getElementById('edit_status_akun');
        for (let opt of statusSelect.options) {
            opt.selected = opt.value === d.status;
        }

        // Set action form pakai URL dari route Laravel
        document.getElementById('editForm').action = urlEdit;
        document.getElementById('editStudentModal').classList.remove('hidden');
    }

    // ==================== DELETE MODAL ====================
    function openDeleteModal(row, urlDelete) {
        const d = row.dataset;
        document.getElementById('delete_nama').textContent = d.nama;
        document.getElementById('deleteForm').action       = urlDelete;
        document.getElementById('deleteStudentModal').classList.remove('hidden');
    }
</script>
@endpush
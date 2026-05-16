@extends('layout.admin')

@section('title', 'Dashboard - TAssist Admin')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, {{ session("admin_user.nama", "Administrator") }}!')

@section('content')
<div class="space-y-6">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        <div class="rounded-2xl p-5 flex items-start gap-4 bg-[#242D45] border border-[#3A4566] border-l-4 border-l-[#0057B8]">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-[rgba(0,87,184,0.15)] flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#0057B8]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m0 0a4 4 0 118 0"/>
                </svg>
            </div>
            <div>
                <p class="text-sm mb-1 text-[#A0A8C0] font-medium">Total Mahasiswa</p>
                <p class="text-white text-2xl font-extrabold">{{ $totalMahasiswa }}</p>
                @if($mahasiswaBulanIni > 0)
                    <p class="text-xs mt-1 text-[#3DDC97]">+{{ $mahasiswaBulanIni }} bulan ini</p>
                @endif
            </div>
        </div>

        <div class="rounded-2xl p-5 flex items-start gap-4 bg-[#242D45] border border-[#3A4566] border-l-4 border-l-[#4DA3FF]">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-[rgba(77,163,255,0.15)] flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#4DA3FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A8 8 0 1118.88 6.196M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm mb-1 text-[#A0A8C0] font-medium">Total Dosen</p>
                <p class="text-white text-2xl font-extrabold">{{ $totalDosen }}</p>
                @if($dosenBulanIni > 0)
                    <p class="text-xs mt-1 text-[#3DDC97]">+{{ $dosenBulanIni }} bulan ini</p>
                @endif
            </div>
        </div>

        <div class="rounded-2xl p-5 flex items-start gap-4 bg-[#242D45] border border-[#3A4566] border-l-4 border-l-[#3DDC97]">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-[rgba(61,220,151,0.15)] flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#3DDC97]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm mb-1 text-[#A0A8C0] font-medium">Slot Tersedia</p>
                <p class="text-white text-2xl font-extrabold">{{ $availableSlots }}</p>
                <p class="text-xs mt-1 text-[#A0A8C0]">dari total kuota dosen</p>
            </div>
        </div>

        <div class="rounded-2xl p-5 flex items-start gap-4 bg-[#242D45] border border-[#3A4566] border-l-4 border-l-[#FFB900]">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-[rgba(255,185,0,0.15)] flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-[#FFB900]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm mb-1 text-[#A0A8C0] font-medium">Permohonan Menunggu</p>
                <p class="text-white text-2xl font-extrabold">{{ $permohonanMenunggu }}</p>
                @if($permohonanMingguIni > 0)
                    <p class="text-xs mt-1 text-[#3DDC97]">+{{ $permohonanMingguIni }} minggu ini</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Chart + Quick Actions --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

        {{-- Chart --}}
        <div class="xl:col-span-2 rounded-2xl p-5 bg-[#242D45] border border-[#3A4566]">
            <div class="mb-5">
                <h3 class="text-white text-sm font-bold">Ringkasan Permohonan Bimbingan</h3>
                <p class="text-xs mt-0.5 text-[#A0A8C0]">6 bulan terakhir</p>
            </div>
            <canvas id="submissionChart" height="100"></canvas>
        </div>

        {{-- Quick Actions --}}
        <div class="rounded-2xl p-5 bg-[#242D45] border border-[#3A4566]">
            <h3 class="text-white text-sm mb-4 font-bold">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('admin.mahasiswa.index') }}"
                   class="w-full flex items-center gap-3 p-3 rounded-xl bg-[#2A3352] border border-[#3A4566] hover:border-[#0057B8] transition">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center bg-[rgba(0,87,184,0.15)]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#0057B8]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <span class="text-white text-sm flex-1 font-medium">Tambah Mahasiswa</span>
                    <span class="text-[#A0A8C0] text-xs">→</span>
                </a>

                <a href="{{ route('admin.dosen.index') }}"
                   class="w-full flex items-center gap-3 p-3 rounded-xl bg-[#2A3352] border border-[#3A4566] hover:border-[#4DA3FF] transition">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center bg-[rgba(77,163,255,0.15)]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#4DA3FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A8 8 0 1118.88 6.196"/>
                        </svg>
                    </div>
                    <span class="text-white text-sm flex-1 font-medium">Tambah Dosen</span>
                    <span class="text-[#A0A8C0] text-xs">→</span>
                </a>

                <a href="{{ route('admin.supervisor-quota.index') }}"
                   class="w-full flex items-center gap-3 p-3 rounded-xl bg-[#2A3352] border border-[#3A4566] hover:border-[#3DDC97] transition">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center bg-[rgba(61,220,151,0.15)]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#3DDC97]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                    </div>
                    <span class="text-white text-sm flex-1 font-medium">Set Kuota</span>
                    <span class="text-[#A0A8C0] text-xs">→</span>
                </a>

                <a href="{{ route('admin.informasi-ta.index') }}"
                   class="w-full flex items-center gap-3 p-3 rounded-xl bg-[#2A3352] border border-[#3A4566] hover:border-[#FFB900] transition">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center bg-[rgba(255,185,0,0.15)]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#FFB900]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h7l5 5v11a1 1 0 01-1 1H7a1 1 0 01-1-1V5a1 1 0 011-1z"/>
                        </svg>
                    </div>
                    <span class="text-white text-sm flex-1 font-medium">Post Informasi TA</span>
                    <span class="text-[#A0A8C0] text-xs">→</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Recent Students + Recent Activity --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

        {{-- Recent Students --}}
        <div class="xl:col-span-2 rounded-2xl p-5 bg-[#242D45] border border-[#3A4566]">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-white text-sm font-bold">Mahasiswa Terbaru</h3>
                <a href="{{ route('admin.mahasiswa.index') }}" class="text-xs text-[#4DA3FF] hover:underline">
                    Lihat Semua →
                </a>
            </div>

            <div class="space-y-1">
                {{-- Header --}}
                <div class="grid grid-cols-12 gap-2 px-3 py-2 text-xs text-[#A0A8C0] font-medium">
                    <span class="col-span-4">Nama</span>
                    <span class="col-span-3">Prodi</span>
                    <span class="col-span-3">Dosen Pembimbing</span>
                    <span class="col-span-2">Status</span>
                </div>

                @forelse($recentMahasiswa as $m)
                    @php
                        $bimbingan = $m->bimbinganAktif;
                        $dosenNama = $bimbingan?->dosen?->user?->nama ?? null;
                        $statusLabel = $bimbingan ? 'Aktif' : 'Belum';
                        $statusClass = $bimbingan
                            ? 'bg-[rgba(77,163,255,0.15)] text-[#4DA3FF]'
                            : 'bg-[rgba(160,168,192,0.15)] text-[#A0A8C0]';
                    @endphp
                    <div class="grid grid-cols-12 gap-2 px-3 py-2.5 rounded-xl text-sm items-center
                        {{ $loop->odd ? 'bg-[#2A3352]' : '' }}">
                        <div class="col-span-4 flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs text-white bg-[#0057B8] font-bold flex-shrink-0">
                                {{ strtoupper(substr($m->user->nama, 0, 1)) }}
                            </div>
                            <span class="text-white text-xs truncate font-medium">{{ $m->user->nama }}</span>
                        </div>
                        <span class="col-span-3 text-xs truncate text-[#A0A8C0]">{{ $m->prodi }}</span>
                        <span class="col-span-3 text-xs truncate text-[#A0A8C0]">
                            {{ $dosenNama ?? '— Belum' }}
                        </span>
                        <div class="col-span-2">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="px-3 py-6 text-center text-[#A0A8C0] text-xs">
                        Belum ada mahasiswa terdaftar.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="rounded-2xl p-5 bg-[#242D45] border border-[#3A4566]">
            <div class="flex items-center gap-2 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#4DA3FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                <h3 class="text-white text-sm font-bold">Aktivitas Terbaru</h3>
            </div>

            <div class="space-y-4">
                @forelse($recentActivity as $activity)
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0"
                             style="background-color: {{ $activity['color'] }}"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-white text-xs font-medium">{{ $activity['title'] }}</p>
                            <p class="text-xs text-[#A0A8C0] truncate">{{ $activity['desc'] }}</p>
                            <p class="text-xs mt-0.5 text-[#4DA3FF]">
                                {{ $activity['time']?->diffForHumans() ?? '-' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-[#A0A8C0] text-xs text-center py-4">Belum ada aktivitas.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Lecturer Quota Overview --}}
    <div class="rounded-2xl p-5 bg-[#242D45] border border-[#3A4566]">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-white text-sm font-bold">Kuota Dosen Pembimbing</h3>
            <a href="{{ route('admin.supervisor-quota.index') }}" class="text-xs text-[#4DA3FF] hover:underline">
                Kelola →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            @forelse($dosenQuota as $d)
                <div class="p-4 rounded-xl bg-[#2A3352] border border-[#3A4566]">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs text-white bg-[#0057B8] font-bold flex-shrink-0">
                            {{ $d['initial'] }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-white text-xs font-semibold truncate">{{ $d['nama'] }}</p>
                            <p class="text-xs text-[#A0A8C0] truncate">{{ $d['bidang_keahlian'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs mb-2">
                        <span class="text-[#A0A8C0]">{{ $d['aktif'] }}/{{ $d['kuota'] }} mahasiswa</span>
                        <span class="font-semibold" style="color: {{ $d['barColor'] }}">
                            {{ $d['pct'] }}%
                        </span>
                    </div>
                    <div class="h-1.5 rounded-full overflow-hidden bg-[#3A4566]">
                        <div class="h-full rounded-full transition-all duration-300"
                             style="width: {{ $d['pct'] }}%; background-color: {{ $d['barColor'] }}"></div>
                    </div>
                    @if($d['isFull'])
                        <p class="text-xs text-[#FF4D4D] mt-2 font-medium">Kuota penuh</p>
                    @else
                        <p class="text-xs text-[#A0A8C0] mt-2">Sisa: {{ $d['sisa'] }} slot</p>
                    @endif
                </div>
            @empty
                <div class="col-span-4 text-center text-[#A0A8C0] text-xs py-6">
                    Belum ada data dosen.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ==================== CHART ====================
    const chartData = @json($chartData);

    const ctx = document.getElementById('submissionChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.map(d => d.label),
            datasets: [
                {
                    label: 'Total Permohonan',
                    data: chartData.map(d => d.permohonan),
                    backgroundColor: 'rgba(0, 87, 184, 0.6)',
                    borderColor: '#0057B8',
                    borderWidth: 1,
                    borderRadius: 6,
                },
                {
                    label: 'Diterima',
                    data: chartData.map(d => d.diterima),
                    backgroundColor: 'rgba(61, 220, 151, 0.6)',
                    borderColor: '#3DDC97',
                    borderWidth: 1,
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: {
                        color: '#A0A8C0',
                        font: { size: 11 }
                    }
                }
            },
            scales: {
                x: {
                    ticks: { color: '#A0A8C0', font: { size: 11 } },
                    grid:  { color: 'rgba(58, 69, 102, 0.5)' }
                },
                y: {
                    ticks: {
                        color: '#A0A8C0',
                        font: { size: 11 },
                        stepSize: 1
                    },
                    grid: { color: 'rgba(58, 69, 102, 0.5)' },
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
@extends('layouts.public')

@section('title', 'Pengumuman Riset | '.config('app.name', 'SIPRISDA'))

@section('content')
    <section class="relative overflow-hidden rounded-3xl border border-[#cde3ff] bg-white/95 backdrop-blur shadow-lg shadow-[#cde3ff]/30">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(205,227,255,0.35),transparent_40%),radial-gradient(circle_at_bottom_right,_rgba(255,214,165,0.25),transparent_35%)]"></div>
        <div class="relative flex flex-wrap items-center justify-between gap-4 px-6 py-6 border-b border-[#cde3ff]/60 bg-gradient-to-r from-[#e7f5ff] via-white to-[#eaf9f3]">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-2 rounded-full bg-white/70 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-[#0f3d73] ring-1 ring-[#cde3ff] shadow-sm">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#0f3d73]"></span> Katalog Publik
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Penelitian Disetujui Terbaru</h1>
                <p class="text-sm text-gray-600">Daftar riset yang telah diverifikasi dan ditayangkan.</p>
                <div class="flex flex-wrap gap-2 text-xs font-semibold text-[#0f3d73]">
                    <span class="inline-flex items-center gap-1 rounded-full bg-[#eaf9f3] px-3 py-1 text-emerald-700 ring-1 ring-emerald-200 shadow-sm"><i class="fas fa-check-circle text-[11px]"></i> Sudah verifikasi</span>
                </div>
            </div>
            <div class="text-right text-sm text-gray-500">
                @if($researches->count())
                    <p class="font-semibold text-gray-900 text-base">{{ $researches->total() }}</p>
                    <p>Entri riset</p>
                @else
                    <p class="text-gray-400">Tidak ada entri</p>
                @endif
            </div>
        </div>

        @if($researches->count())
            <div class="grid gap-4 p-6 relative z-10">
                @foreach ($researches as $r)
                    @php
                        $startDate = $r->tanggal_mulai;
                        $endDate = $r->tanggal_selesai;
                        $startLabel = $startDate ? $startDate->format('d M Y') : '-';
                        $endLabel = $endDate ? $endDate->format('d M Y') : '-';
                        $statusLabel = 'Belum Dijadwalkan';
                        $statusClasses = 'bg-gray-100 text-gray-600';
                        $statusHint = 'Lengkapi tanggal untuk memantau progres.';
                        $statusIcon = 'far fa-calendar-alt';

                        if ($endDate && $endDate->isPast()) {
                            $statusLabel = 'Selesai';
                            $statusClasses = 'bg-emerald-100 text-emerald-700';
                            $statusHint = 'Penelitian telah selesai';
                            $statusIcon = 'fas fa-check-circle';
                        } elseif ($startDate && $startDate->isPast()) {
                            $statusLabel = 'Sedang Berjalan';
                            $statusClasses = 'bg-amber-100 text-amber-700';
                            $statusHint = 'Pantau hingga akhir periode penelitian.';
                            $statusIcon = 'fas fa-play-circle';
                        } elseif ($startDate && $startDate->isFuture()) {
                            $statusLabel = 'Terjadwal';
                            $statusClasses = 'bg-blue-100 text-blue-700';
                            $statusHint = 'Belum dimulai, siapkan pendampingan.';
                            $statusIcon = 'far fa-clock';
                        }
                    @endphp
                    <article class="relative overflow-hidden rounded-2xl border border-[#cde3ff]/70 bg-white/95 shadow-sm hover:shadow-lg transition p-5">
                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-[#e7f5ff]/60 via-white to-[#fff8ed]/50 opacity-80"></div>
                        <div class="relative flex flex-wrap items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#0f3d73]">Judul</p>
                                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 leading-snug">{{ $r->judul }}</h2>
                                <div class="mt-2 flex flex-wrap gap-2 text-xs font-semibold text-[#0f3d73]">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-[#e7f5ff] px-3 py-1 shadow-sm"><i class="fas fa-user text-[10px]"></i>{{ $r->penulis }}</span>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-[#eaf9f3] px-3 py-1 text-emerald-700 ring-1 ring-emerald-100 shadow-sm"><i class="fas fa-tags text-[10px]"></i>{{ optional($r->field)->nama ?: '-' }}</span>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-[#f5f5ff] px-3 py-1 text-[#4338ca] ring-1 ring-[#e0e7ff] shadow-sm"><i class="fas fa-building text-[10px]"></i>{{ optional($r->institution)->nama ?: '-' }}</span>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-[#fff7e6] px-3 py-1 text-amber-700 ring-1 ring-amber-100 shadow-sm"><i class="fas fa-calendar text-[10px]"></i>{{ $r->tahun }}</span>
                                </div>
                            </div>
                            <div class="text-right text-xs text-gray-500 space-y-1">
                                <p class="font-semibold text-gray-900">Mulai</p>
                                <span class="inline-flex items-center gap-1 rounded-full bg-[#e7f5ff] px-3 py-1 font-semibold text-[#0f3d73] shadow-sm"><i class="far fa-calendar-alt text-[10px]"></i>{{ $startLabel }}</span>
                                <p class="font-semibold text-gray-900">Selesai</p>
                                <span class="inline-flex items-center gap-1 rounded-full bg-[#eaf9f3] px-3 py-1 font-semibold text-emerald-700 shadow-sm"><i class="far fa-calendar-check text-[10px]"></i>{{ $endLabel }}</span>
                            </div>
                        </div>
                        <div class="relative mt-4 flex items-center gap-3 text-xs">
                            <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 font-semibold {{ $statusClasses }} shadow-sm"><i class="{{ $statusIcon }} text-[11px]"></i> {{ $statusLabel }}</span>
                            <p class="text-gray-500">{{ $statusHint }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="px-6 pb-6">
                {{ $researches->links() }}
            </div>
        @else
            <div class="relative z-10 p-10 text-center text-sm text-gray-600">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-[#e7f5ff] text-[#0f3d73] shadow-inner shadow-[#cde3ff]/50 mb-3">
                    <i class="fas fa-info-circle text-lg"></i>
                </div>
                Belum ada penelitian disetujui yang ditayangkan.
            </div>
        @endif
    </section>
@endsection

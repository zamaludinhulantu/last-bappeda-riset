@extends('layouts.public')

@section('title', 'Tentang | '.config('app.name','Aplikasi'))

@section('hero')
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#f5f9ff] via-white to-[#fff7ed] p-4 sm:p-6 shadow-lg shadow-[#cde3ff]/40 ring-1 ring-[#cde3ff]/60">
        <div class="pointer-events-none absolute -left-20 -top-24 h-52 w-52 rounded-full bg-gradient-to-br from-[#ffd6a5] via-[#ffecd2] to-[#cde3ff] blur-3xl opacity-70"></div>
        <div class="pointer-events-none absolute -right-10 top-8 h-44 w-44 rounded-full bg-gradient-to-br from-[#cde3ff] via-[#e7f5ff] to-[#eaf9f3] blur-3xl opacity-70"></div>
        <div class="relative z-10 max-w-4xl space-y-3">
            <div class="inline-flex items-center gap-2 rounded-full bg-white/70 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-[#0f3d73] ring-1 ring-[#cde3ff] shadow-sm">
                <span class="h-1.5 w-1.5 rounded-full bg-[#0f3d73]"></span> Tentang Kami
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 leading-tight">Portal Penelitian Terbuka bapppeda</h1>
            <p class="text-gray-600 text-lg">Gerbang data riset terverifikasi yang menghubungkan peneliti, pemerintah, dan publik.</p>
        </div>
    </div>
@endsection

@section('content')
    <section class="relative overflow-hidden rounded-3xl border border-[#cde3ff] bg-white/95 backdrop-blur shadow-lg shadow-[#cde3ff]/30 p-6 sm:p-8">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(205,227,255,0.35),transparent_40%),radial-gradient(circle_at_bottom_right,_rgba(255,214,165,0.25),transparent_35%)]"></div>
        <div class="relative z-10 space-y-6">
            <div class="grid gap-6 md:grid-cols-2">
                <div class="space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Visi Kami</p>
                    <h2 class="text-2xl font-semibold text-gray-900">Data riset yang terbuka, terkurasi, dan kolaboratif</h2>
                    <p class="text-gray-600 leading-7">Kami memastikan setiap pengajuan riset terverifikasi Kesbangpol, terdokumentasi, dan dapat dipantau publik. Tujuannya: transparansi, akuntabilitas, dan pemanfaatan data untuk kebijakan berbasis bukti.</p>
                    <div class="grid gap-3 sm:grid-cols-2 text-sm text-gray-700">
                        <div class="rounded-xl border border-[#e7f5ff] bg-white/80 p-3 shadow-sm">
                            <p class="text-xs uppercase font-semibold text-[#0f3d73]">Integrasi</p>
                            <p class="mt-1">Pengajuan, verifikasi Kesbangpol, dan publikasi dalam satu alur.</p>
                        </div>
                        <div class="rounded-xl border border-[#eaf9f3] bg-white/80 p-3 shadow-sm">
                            <p class="text-xs uppercase font-semibold text-emerald-700">Transparansi</p>
                            <p class="mt-1">Status real-time, catatan revisi terdokumentasi.</p>
                        </div>
                    </div>
                </div>
                @php
                    $email = $contactInfo->value('surel');
                    $phone = $contactInfo->value('telepon');
                    $address = $contactInfo->value('alamat');
                @endphp
                <div class="rounded-2xl border border-[#cde3ff] bg-gradient-to-br from-[#e7f5ff] via-white to-[#eaf9f3] p-4 shadow-inner shadow-[#cde3ff]/30">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Hubungi Kami</p>
                    <h3 class="text-xl font-semibold text-gray-900 mt-1">{{ $contactInfo->value('judul') }}</h3>
                    <p class="text-gray-600 mt-2 leading-7">{{ $contactInfo->value('subjudul') }}</p>
                    <div class="mt-4 grid gap-3 text-sm text-gray-700">
                        @if($email)
                            <div class="flex items-start gap-3 rounded-xl bg-white/90 p-3 shadow-sm ring-1 ring-[#e7f5ff]">
                                <div class="mt-1 text-[#0f3d73]"><i class="fas fa-envelope text-sm"></i></div>
                                <div>
                                    <p class="text-xs uppercase font-semibold text-gray-500">Email</p>
                                    <p class="text-gray-900">{{ $email }}</p>
                                </div>
                            </div>
                        @endif
                        @if($phone)
                            <div class="flex items-start gap-3 rounded-xl bg-white/90 p-3 shadow-sm ring-1 ring-[#e7f5ff]">
                                <div class="mt-1 text-[#0f3d73]"><i class="fas fa-phone text-sm"></i></div>
                                <div>
                                    <p class="text-xs uppercase font-semibold text-gray-500">Telepon</p>
                                    <p class="text-gray-900">{{ $phone }}</p>
                                </div>
                            </div>
                        @endif
                        @if($address)
                            <div class="flex items-start gap-3 rounded-xl bg-white/90 p-3 shadow-sm ring-1 ring-[#e7f5ff]">
                                <div class="mt-1 text-[#0f3d73]"><i class="fas fa-map-marker-alt text-sm"></i></div>
                                <div>
                                    <p class="text-xs uppercase font-semibold text-gray-500">Alamat</p>
                                    <p class="text-gray-900">{{ $address }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-[#cde3ff]/60 bg-white/90 p-4 shadow-sm">
                <div class="flex flex-wrap items-center gap-3 text-xs font-semibold text-[#0f3d73]">
                    <span class="inline-flex items-center gap-1 rounded-full bg-[#e7f5ff] px-3 py-1 ring-1 ring-[#cde3ff] shadow-sm"><i class="fas fa-shield-halved text-[11px]"></i> Verifikasi Kesbangpol</span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-[#fff8ed] px-3 py-1 text-amber-700 ring-1 ring-amber-100 shadow-sm"><i class="fas fa-bolt text-[11px]"></i> Status real-time</span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-[#eaf9f3] px-3 py-1 text-emerald-700 ring-1 ring-emerald-200 shadow-sm"><i class="fas fa-database text-[11px]"></i> Arsip publik</span>
                </div>
            </div>
        </div>
    </section>
@endsection

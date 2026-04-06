@extends('layouts.public')

@section('title', 'Panduan | '.config('app.name','Aplikasi'))

@section('hero')
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#f5f9ff] via-white to-[#fff7ed] p-4 sm:p-6 shadow-lg shadow-[#cde3ff]/40 ring-1 ring-[#cde3ff]/60">
        <div class="pointer-events-none absolute -left-16 -top-24 h-48 w-48 rounded-full bg-gradient-to-br from-[#ffd6a5] via-[#ffecd2] to-[#cde3ff] blur-3xl opacity-70"></div>
        <div class="pointer-events-none absolute -right-10 top-6 h-40 w-40 rounded-full bg-gradient-to-br from-[#cde3ff] via-[#e7f5ff] to-[#eaf9f3] blur-3xl opacity-70"></div>
        <div class="relative z-10 max-w-4xl space-y-4">
            <div class="inline-flex items-center gap-2 rounded-full bg-white/70 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-[#0f3d73] ring-1 ring-[#cde3ff] shadow-sm">
                <span class="h-1.5 w-1.5 rounded-full bg-[#0f3d73]"></span> Panduan Singkat
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 leading-tight">Alur Pengajuan Penelitian ke BAPPPEDA</h1>
            <p class="text-gray-600 mt-1 text-lg">Ikuti langkah terbaru agar berkas cepat diverifikasi Kesbangpol dan riset terbit di portal publik.</p>
            <div class="flex flex-wrap gap-2 text-xs sm:text-sm text-[#0f3d73] font-semibold">
                <span class="inline-flex items-center gap-1 rounded-full bg-white/80 px-3 py-1 ring-1 ring-[#cde3ff] shadow-sm"><i class="fas fa-shield-halved text-[11px]"></i> Hanya riset berizin yang dipublikasikan</span>
                
            </div>
        </div>
    </div>
@endsection

@section('content')
    <section class="relative overflow-hidden rounded-3xl border border-[#cde3ff] bg-white/95 backdrop-blur shadow-lg shadow-[#cde3ff]/30 p-6 sm:p-8">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(205,227,255,0.35),transparent_40%),radial-gradient(circle_at_bottom_right,_rgba(255,214,165,0.25),transparent_35%)]"></div>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="md:col-span-2 space-y-5 relative z-10">
                <ol class="space-y-5">
                    <li class="group flex gap-4">
                        <div class="flex flex-col items-center">
                            <span class="h-10 w-10 rounded-full bg-gradient-to-br from-orange-200 to-orange-50 text-orange-700 font-semibold flex items-center justify-center shadow-sm shadow-orange-100">1</span>
                            <span class="flex-1 w-px bg-orange-100"></span>
                        </div>
                        <div class="flex-1 rounded-2xl bg-white/90 p-4 shadow-sm ring-1 ring-[#e7f5ff] transition duration-300 group-hover:-translate-y-1 group-hover:shadow-lg">
                            <div class="flex items-start gap-2">
                                <h2 class="text-lg font-semibold text-gray-900">Daftar & Lengkapi Profil</h2>
                                <span class="inline-flex items-center gap-1 rounded-full bg-[#eaf9f3] px-2 py-0.5 text-[11px] font-semibold text-emerald-700 ring-1 ring-emerald-200"><i class="fas fa-user-check text-[10px]"></i> Data valid</span>
                            </div>
                            <p class="text-gray-600 mt-1">Buat akun peneliti, isi data diri, NIK, institusi, dan nomor HP aktif.</p>
                            <div class="mt-2 inline-flex items-center gap-2 rounded-full bg-[#e7f5ff] px-3 py-1 text-xs font-semibold text-[#0f3d73] shadow-sm">
                                <i class="fas fa-envelope text-[11px]"></i>
                                Pakai email aktif agar verifikasi & notifikasi terkirim.
                            </div>
                        </div>
                    </li>
                    <li class="group flex gap-4">
                        <div class="flex flex-col items-center">
                            <span class="h-10 w-10 rounded-full bg-gradient-to-br from-orange-200 to-orange-50 text-orange-700 font-semibold flex items-center justify-center shadow-sm shadow-orange-100">2</span>
                            <span class="flex-1 w-px bg-orange-100"></span>
                        </div>
                        <div class="flex-1 rounded-2xl bg-white/90 p-4 shadow-sm ring-1 ring-[#e7f5ff] transition duration-300 group-hover:-translate-y-1 group-hover:shadow-lg">
                            <div class="flex items-start gap-2">
                                <h2 class="text-lg font-semibold text-gray-900">Ajukan Riset & Unggah Berkas</h2>
                                <span class="inline-flex items-center gap-1 rounded-full bg-[#fff4e6] px-2 py-0.5 text-[11px] font-semibold text-amber-700 ring-1 ring-amber-100"><i class="fas fa-upload text-[10px]"></i> Lampirkan lengkap</span>
                            </div>
                            <p class="text-gray-600 mt-1">Isi judul, Abstrak, lokasi, jadwal kegiatan, lalu unggah proposal. Untuk peneliti S1/S2/S3 wajib menyertakan surat pengantar dari universitas/instansi/lembaga pada unggahan awal.</p>

                        </div>
                    </li>
                    <li class="group flex gap-4">
                        <div class="flex flex-col items-center">
                            <span class="h-10 w-10 rounded-full bg-gradient-to-br from-orange-200 to-orange-50 text-orange-700 font-semibold flex items-center justify-center shadow-sm shadow-orange-100">3</span>
                            <span class="flex-1 w-px bg-orange-100"></span>
                        </div>
                        <div class="flex-1 rounded-2xl bg-white/90 p-4 shadow-sm ring-1 ring-[#e7f5ff] transition duration-300 group-hover:-translate-y-1 group-hover:shadow-lg">
                            <div class="flex items-start gap-2">
                                <h2 class="text-lg font-semibold text-gray-900">Verifikasi Kesbangpol</h2>
                                <span class="inline-flex items-center gap-1 rounded-full bg-[#e7f5ff] px-2 py-0.5 text-[11px] font-semibold text-[#0f3d73] ring-1 ring-[#cde3ff]"><i class="fas fa-shield-alt text-[10px]"></i> Verifikasi</span>
                            </div>
                            <p class="text-gray-600 mt-1">Admin meneruskan berkas ke Kesbangpol. Pantau status: Menunggu &rarr; Terverifikasi / Perlu Perbaikan di dashboard Anda.</p>
                           
                        </div>
                    </li>
                    <li class="group flex gap-4">
                        <div class="flex flex-col items-center">
                            <span class="h-10 w-10 rounded-full bg-gradient-to-br from-orange-200 to-orange-50 text-orange-700 font-semibold flex items-center justify-center shadow-sm shadow-orange-100">4</span>
                            <span class="flex-1 w-px bg-orange-100"></span>
                        </div>
                        <div class="flex-1 rounded-2xl bg-white/90 p-4 shadow-sm ring-1 ring-[#e7f5ff] transition duration-300 group-hover:-translate-y-1 group-hover:shadow-lg">
                            <div class="flex items-start gap-2">
                                <h2 class="text-lg font-semibold text-gray-900">Tindaklanjuti Catatan</h2>
                                <span class="inline-flex items-center gap-1 rounded-full bg-[#fef3f2] px-2 py-0.5 text-[11px] font-semibold text-rose-700 ring-1 ring-rose-100"><i class="fas fa-comment-dots text-[10px]"></i> Perbaiki cepat</span>
                            </div>
                            <p class="text-gray-600 mt-1">Jika ada koreksi dari admin/Kesbangpol, revisi berkas, unggah ulang, dan balas komentar sebagai bukti perbaikan.</p>
                        </div>
                    </li>
                    <li class="group flex gap-4">
                        <div class="flex flex-col items-center">
                            <span class="h-10 w-10 rounded-full bg-gradient-to-br from-orange-200 to-orange-50 text-orange-700 font-semibold flex items-center justify-center shadow-sm shadow-orange-100">5</span>
                        </div>
                        <div class="flex-1 rounded-2xl bg-white/90 p-4 shadow-sm ring-1 ring-[#e7f5ff] transition duration-300 group-hover:-translate-y-1 group-hover:shadow-lg">
                            <div class="flex items-start gap-2">
                                <h2 class="text-lg font-semibold text-gray-900">Terbitkan & Arsipkan</h2>
                                <span class="inline-flex items-center gap-1 rounded-full bg-[#eaf9f3] px-2 py-0.5 text-[11px] font-semibold text-emerald-700 ring-1 ring-emerald-200"><i class="fas fa-check-circle text-[10px]"></i> Selesai</span>
                            </div>
                            <p class="text-gray-600 mt-1">Setelah status Terverifikasi, unggah laporan akhir/hasil riset. Admin menerbitkan di portal publik dan statistik.</p>
                        </div>
                    </li>
                </ol>
            </div>
            <div class="space-y-4 relative z-10">
                <div class="rounded-2xl border border-[#cde3ff] bg-gradient-to-br from-[#e7f5ff] via-white to-[#eaf9f3] p-4 shadow-inner shadow-[#cde3ff]/30 transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Checklist Berkas</p>
                    <ul class="mt-2 space-y-2 text-sm text-gray-700">
                        <li class="flex items-start gap-2"><i class="fas fa-file-signature text-[#0f3d73] mt-0.5"></i> Surat pengantar/permohonan dari universitas/instansi/lembaga (wajib untuk peneliti S1, S2, S3 pada unggahan awal)</li>
                        <li class="flex items-start gap-2"><i class="fas fa-file-pdf text-[#0f3d73] mt-0.5"></i> Proposal/abstrak riset (PDF/Word)</li>
                        <li class="flex items-start gap-2"><i class="fas fa-id-card text-[#0f3d73] mt-0.5"></i> KTP/NIK & Data peneliti </li>
                        <li class="flex items-start gap-2"><i class="fas fa-file text-[#0f3d73] mt-0.5"></i>  Data Penelitian</li>
                    </ul>
                </div>
                <div class="rounded-2xl border border-[#cde3ff] bg-white p-4 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">FAQ Singkat</p>
                    <ul class="mt-2 space-y-2 text-sm text-gray-700">
                        <li><span class="font-semibold text-gray-900">Kapan status berubah?</span> Saat admin menerima feedback Kesbangpol, status langsung diperbarui.</li>
                        <li><span class="font-semibold text-gray-900">Bagaimana jika ditolak?</span> Perbaiki catatan, unggah ulang, lalu klik kirim ulang pengajuan.</li>
                        <li><span class="font-semibold text-gray-900">Siapa yang dipublikasikan?</span> Peneliti dengan dokumen lengkap, dan diverifikasi admin</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection

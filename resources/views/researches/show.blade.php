@php
    $attributes = $research->getAttributes();
    $fileLike = collect($attributes)->filter(fn($v) => is_string($v) && preg_match('/\.(pdf|docx?|xlsx?|pptx?|csv|jpg|jpeg|png|gif|svg|webp|txt|zip|rar)$/i', $v));
    $status = (string)($research->status ?? 'draft');
    $statusMap = [
        'approved' => ['label' => 'Disetujui', 'class' => 'bg-emerald-50 text-emerald-700'],
        'rejected' => ['label' => 'Ditolak', 'class' => 'bg-rose-50 text-rose-700'],
        'submitted' => ['label' => 'Diajukan', 'class' => 'bg-amber-50 text-amber-700'],
        'kesbang_verified' => ['label' => 'Disetujui Kesbang', 'class' => 'bg-cyan-50 text-cyan-700'],
        'default' => ['label' => 'Draft', 'class' => 'bg-gray-50 text-gray-600'],
    ];
    $statusInfo = $statusMap[$status] ?? $statusMap['default'];
    $user = auth()->user();
    $isSuperAdmin = $user?->isSuperAdmin();
    $isOwner = $user && (int) $user->id === (int) ($research->pengunggah_id ?? $research->user_id ?? 0);
    $canModerate = $user?->hasAdminAccess();
    $canUploadResults = $isOwner || $canModerate;
    $canUploadAfterKesbang = $canUploadResults && ($canModerate || (bool) $research->diverifikasi_kesbang_pada);
    $waitingKesbangForUpload = !$canModerate && $canUploadResults && !$research->diverifikasi_kesbang_pada;
    $hasResubmission = (bool) $research->diajukan_ulang_pada;
    $canVerifyKesbang = $user?->hasKesbangAccess() || $user?->hasAdminAccess();
    $canReverifyKesbang = $canVerifyKesbang && ($research->status === 'rejected' || $hasResubmission);
    $canRejectKesbang = $canVerifyKesbang && (!$research->diverifikasi_kesbang_pada || $hasResubmission) && !in_array($research->status, ['approved', 'rejected'], true);
    $canChangeDecision = $canVerifyKesbang && $research->diverifikasi_kesbang_pada && !$hasResubmission;
    $canFixRejected = ($isOwner || $isSuperAdmin) && $research->status === 'rejected';

    $formatDateTime = fn($value) => $value ? \Carbon\Carbon::parse($value)->format('d M Y') : null;
    $formatUser = function ($id, $relation = null) {
        return optional($relation)->nama ?: optional($relation)->surel ?: ($id ? 'ID ' . $id : null);
    };
    $researcherEmail = $research->email_peneliti ?: optional($research->submittedBy)->surel;
    $endDate = $research->tanggal_selesai ? \Carbon\Carbon::parse($research->tanggal_selesai) : null;
    $isResearchFinished = $endDate && $endDate->isPast();
    $canSendAutoReminder = ($canVerifyKesbang || $canModerate) && $researcherEmail;
    $attributeRows = [
        ['label' => 'ID', 'value' => $research->id],
        ['label' => 'Judul', 'value' => $research->judul],
        ['label' => 'Peneliti', 'value' => $research->penulis],
        ['label' => 'NIK Peneliti', 'value' => $research->nik_peneliti],
        ['label' => 'Kontak Peneliti', 'value' => $research->telepon_peneliti],
        ['label' => 'Email Peneliti', 'value' => $researcherEmail],
        ['label' => 'Institusi', 'value' => optional($research->institution)->nama],
        ['label' => 'Bidang', 'value' => optional($research->field)->nama],
        ['label' => 'Tahun', 'value' => $research->tahun],
        ['label' => 'Tanggal Mulai', 'value' => optional($research->tanggal_mulai)->format('d M Y')],
        ['label' => 'Tanggal Selesai', 'value' => optional($research->tanggal_selesai)->format('d M Y')],
        ['label' => 'Abstrak', 'value' => $research->abstrak],
        ['label' => 'Berkas Proposal', 'value' => $research->berkas_pdf, 'type' => 'file', 'key' => 'berkas_pdf'],
        ['label' => 'Surat Pengantar Universitas/Instansi/Lembaga', 'value' => $research->berkas_surat_kampus, 'type' => 'file', 'key' => 'berkas_surat_kampus'],
        ['label' => 'Surat Rekomendasi', 'value' => $research->berkas_surat_kesbang, 'type' => 'file', 'key' => 'berkas_surat_kesbang'],
        ['label' => 'Nomor Surat Rekomendasi', 'value' => $research->nomor_surat_kesbang],
        ['label' => 'Tanggal Surat Rekomendasi', 'value' => optional($research->tanggal_surat_kesbang)->format('d M Y')],
        ['label' => 'Status', 'type' => 'status'],
        ['label' => 'Diajukan Oleh', 'value' => $formatUser($research->pengunggah_id, $research->submittedBy ?? null)],
        ['label' => 'Diajukan Pada', 'value' => $formatDateTime($research->diajukan_pada)],
        ['label' => 'Diverifikasi Kesbang Pada', 'value' => $formatDateTime($research->diverifikasi_kesbang_pada)],
        ['label' => 'Diverifikasi Kesbang Oleh', 'value' => $formatUser($research->diverifikasi_kesbang_oleh, $research->kesbangVerifier ?? null)],
        ['label' => 'Disetujui Pada', 'value' => $formatDateTime($research->disetujui_pada)],
        ['label' => 'Hasil Diunggah Pada', 'value' => $formatDateTime($research->hasil_diunggah_pada)],
        ['label' => 'Berkas Hasil', 'value' => $research->berkas_hasil, 'type' => 'file', 'key' => 'berkas_hasil'],
        ['label' => 'Ditolak Pada', 'value' => $formatDateTime($research->ditolak_pada)],
        ['label' => 'Alasan Penolakan', 'value' => $research->alasan_penolakan],
        ['label' => 'Diajukan Ulang Setelah Ditolak', 'value' => $formatDateTime($research->diajukan_ulang_pada)],
        ['label' => 'Disetujui Oleh', 'value' => $formatUser($research->disetujui_oleh, $research->approver ?? null)],
        ['label' => 'Ditolak Oleh', 'value' => $formatUser($research->ditolak_oleh, $research->rejectedBy ?? null)],
        ['label' => 'Dibuat Pada', 'value' => $formatDateTime($research->dibuat_pada)],
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Detail</p>
                <h2 class="text-2xl font-semibold text-gray-900">{{ __('Detail Penelitian') }}</h2>
                <p class="text-sm text-gray-500">Pantau status dan unduh berkas penelitian Anda.</p>
            </div>
            <a href="{{ route('researches.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-white">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if($isSuperAdmin)
            <div class="flex flex-wrap justify-end gap-2">
                <a href="{{ route('researches.edit', $research->id) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-pen text-[11px]"></i> Edit data (Super Admin)
                </a>
                <form action="{{ route('researches.destroy', $research->id) }}" method="POST" onsubmit="return confirm('Hapus penelitian ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-xs font-semibold text-rose-700 hover:bg-white">
                        <i class="fas fa-trash text-[11px]"></i> Hapus
                    </button>
                </form>
            </div>
        @endif

        {{-- Keputusan Admin (BAPPPEDA) --}}
        <section class="rounded-3xl border border-cyan-50 bg-gradient-to-br from-white via-cyan-50/40 to-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-cyan-50 bg-white/70 backdrop-blur flex items-center justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-[11px] font-semibold text-cyan-700 ring-1 ring-cyan-100 shadow-sm">
                        <i class="fas fa-shield-halved text-[10px]"></i> Keputusan Admin
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mt-2">Tentukan hasil pengajuan</h3>
                    <p class="text-sm text-gray-600">Setelah Kesbangpol memverifikasi, admin dapat menyetujui atau menolak pengajuan.</p>
                </div>
                <p class="text-xs text-gray-500">
                    @if($research->diverifikasi_kesbang_pada)
                        Kesbangpol: <span class="font-semibold text-emerald-600">Sudah diverifikasi</span>
                    @else
                        Kesbangpol: <span class="font-semibold text-amber-600">Belum diverifikasi</span>
                    @endif
                </p>
            </div>
            <div class="px-6 py-5">
                @if($canModerate)
                    @if(!$research->diverifikasi_kesbang_pada)
                        <div class="rounded-xl border border-amber-100 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                            Menunggu verifikasi Kesbangpol sebelum dapat di-approve atau ditolak.
                        </div>
                    @else
                        @if($research->status === 'rejected')
                            <div class="rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                                Pengajuan ini sebelumnya ditolak. Anda dapat menyetujui kembali jika peneliti sudah memperbaiki data.
                            </div>
                        @endif
                        <div class="flex flex-col gap-4 md:flex-row">
                            <form action="{{ route('researches.approve', $research->id) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-500">
                                    <i class="fas fa-check-circle text-[13px]"></i> {{ $research->status === 'rejected' ? 'Setujui Kembali' : 'Setujui' }}
                                </button>
                            </form>
                            <form action="{{ route('researches.reject', $research->id) }}" method="POST" class="flex-1 flex flex-col gap-3" data-reject-form>
                                @csrf
                                <input type="hidden" name="alasan_penolakan" value="">
                                <input type="hidden" name="catatan_keputusan" value="">
                                <button type="button" class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 px-5 py-3 text-sm font-semibold text-white hover:bg-rose-500" data-reject-trigger>
                                    <i class="fas fa-times-circle text-[13px]"></i> Tolak
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    @if($research->status === 'rejected')
                        <div class="rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            Pengajuan ditolak. Silakan perbaiki data lalu ajukan ulang melalui tombol di atas.
                        </div>
                    @endif
                @endif
            </div>
        </section>

        <section class="rounded-3xl border border-orange-50 bg-gradient-to-br from-white via-orange-50/30 to-white p-6 shadow-sm">
            <div class="space-y-4">
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="space-y-1">
                            <p class="text-xs uppercase font-semibold text-gray-500">Judul Penelitian</p>
                            <h1 class="text-2xl font-semibold text-gray-900">{{ $research->judul ?? '-' }}</h1>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p>Penulis: <span class="font-semibold text-gray-800">{{ $research->penulis ?? '-' }}</span></p>
                                <p>Bidang: <span class="font-semibold text-gray-800">{{ optional($research->field)->nama ?? '-' }}</span></p>
                                <p>Institusi: <span class="font-semibold text-gray-800">{{ optional($research->institution)->nama ?? '-' }}</span></p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-2 text-right">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span>
                            <p class="text-xs text-gray-500">Diajukan: {{ optional($research->dibuat_pada)->format('d M Y H:i') ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @if($research->berkas_surat_kesbang)
                            <div class="flex flex-col gap-2 rounded-xl border border-cyan-100 bg-white/80 px-4 py-3 text-xs text-gray-700">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('researches.download', [$research, 'berkas_surat_kesbang']) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-md border border-gray-200 px-3 py-1.5 font-semibold text-gray-700 hover:bg-white">
                                        <i class="fas fa-file-download text-[11px]"></i> Surat Rekomendasi
                                    </a>
                                    <a href="{{ route('researches.download', [$research, 'berkas_surat_kesbang']) }}?download=1" class="text-[11px] font-semibold text-emerald-700 hover:underline">Unduh</a>
                                    <span class="inline-flex items-center gap-2 rounded-full bg-cyan-50 px-3 py-1 text-[11px] font-semibold text-cyan-700 ring-1 ring-cyan-100">
                                        <i class="fas fa-shield-halved text-[10px]"></i> Sudah diverifikasi Kesbangpol
                                    </span>
                                </div>
                                <div class="grid gap-2 sm:grid-cols-2">
                                    @if($research->nomor_surat_kesbang)
                                        <p class="text-[11px] text-gray-600">Nomor surat: <span class="font-semibold text-gray-800">{{ $research->nomor_surat_kesbang }}</span></p>
                                    @endif
                                    @if($research->tanggal_surat_kesbang)
                                        <p class="text-[11px] text-gray-600">Tanggal surat: <span class="font-semibold text-gray-800">{{ optional($research->tanggal_surat_kesbang)->format('d M Y') }}</span></p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($canVerifyKesbang)
                            <div class="flex flex-col gap-3">
                                @if(!$research->diverifikasi_kesbang_pada || $hasResubmission)
                                    <form action="{{ route('researches.kesbang.verify', $research->id) }}" method="POST" enctype="multipart/form-data" class="rounded-2xl border border-cyan-100 bg-white/90 p-4 shadow-sm space-y-4">
                                        @csrf
                                        <div class="flex items-center gap-2 text-sm font-semibold text-cyan-700">
                                            <i class="fas fa-file-shield text-[12px]"></i> Form Verifikasi Kesbangpol
                                        </div>
                                        <div class="grid gap-3">
                                            <div class="space-y-1">
                                                <label class="text-[11px] font-semibold uppercase tracking-wide text-cyan-700">Surat rekomendasi</label>
                                                <input type="file" name="kesbang_letter" accept=".pdf,.jpg,.jpeg,.png" class="w-full rounded-lg border border-cyan-200 text-sm focus:border-cyan-500 focus:ring-cyan-500" @unless($research->berkas_surat_kesbang) required @endunless>
                                                <p class="text-[11px] text-gray-500">Wajib saat verifikasi. Format PDF/JPG/PNG, maks 20 MB.</p>
                                                @error('kesbang_letter')
                                                    <p class="text-[11px] text-rose-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div class="grid gap-3 sm:grid-cols-3">
                                                <div class="space-y-1">
                                                    <label class="text-[11px] font-semibold text-cyan-700">Nomor surat</label>
                                                    <input type="text" name="nomor_surat_kesbang" value="{{ old('nomor_surat_kesbang', $research->nomor_surat_kesbang) }}" class="w-full rounded-lg border border-cyan-200 text-sm focus:border-cyan-500 focus:ring-cyan-500" @unless($research->nomor_surat_kesbang && $research->tanggal_surat_kesbang) required @endunless>
                                                    @error('nomor_surat_kesbang')
                                                        <p class="text-[11px] text-rose-600">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="space-y-1">
                                                    <label class="text-[11px] font-semibold text-cyan-700">Tanggal surat</label>
                                                    <input type="date" name="tanggal_surat_kesbang" value="{{ old('tanggal_surat_kesbang', optional($research->tanggal_surat_kesbang)->format('Y-m-d')) }}" class="w-full rounded-lg border border-cyan-200 text-sm focus:border-cyan-500 focus:ring-cyan-500" @unless($research->nomor_surat_kesbang && $research->tanggal_surat_kesbang) required @endunless>
                                                    @error('tanggal_surat_kesbang')
                                                        <p class="text-[11px] text-rose-600">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-cyan-500">
                                                <i class="fas fa-shield-halved text-[12px]"></i> Verifikasi
                                            </button>
                                            <span class="text-[11px] text-gray-500">Setelah disimpan, peneliti akan mendapat notifikasi.</span>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        @if($canRejectKesbang)
                            <form action="{{ route('researches.kesbang.reject', $research->id) }}" method="POST" class="flex flex-col sm:flex-row gap-2" data-reject-form>
                                @csrf
                                <input type="hidden" name="alasan_penolakan" value="">
                                <input type="hidden" name="catatan_keputusan" value="">
                                <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-500" data-reject-trigger>
                                    <i class="fas fa-ban text-[11px]"></i> Tolak Kesbangpol
                                </button>
                            </form>
                        @endif

                        @if($canChangeDecision)
                            <div class="mt-3 grid gap-3 md:grid-cols-2">
                                <form action="{{ route('researches.kesbang.verify', $research->id) }}" method="POST" enctype="multipart/form-data" class="rounded-xl border border-cyan-100 bg-white/80 p-3 space-y-2">
                                    @csrf
                                    <p class="text-[11px] font-semibold text-cyan-700 flex items-center gap-1"><i class="fas fa-rotate text-[11px]"></i> Ubah jadi verifikasi</p>
                                    <input type="file" name="kesbang_letter" accept=".pdf,.jpg,.jpeg,.png" class="w-full rounded-lg border border-cyan-200 text-xs focus:border-cyan-500 focus:ring-cyan-500">
                                    <div class="grid gap-2 sm:grid-cols-2">
                                        <input type="text" name="nomor_surat_kesbang" value="{{ old('nomor_surat_kesbang', $research->nomor_surat_kesbang) }}" placeholder="Nomor surat" class="w-full rounded-lg border border-cyan-200 text-xs focus:border-cyan-500 focus:ring-cyan-500">
                                        <input type="date" name="tanggal_surat_kesbang" value="{{ old('tanggal_surat_kesbang', optional($research->tanggal_surat_kesbang)->format('Y-m-d')) }}" class="w-full rounded-lg border border-cyan-200 text-xs focus-border-cyan-500 focus:ring-cyan-500">
                                    </div>
                                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-cyan-200 bg-white px-3 py-2 text-xs font-semibold text-cyan-700 hover:bg-cyan-50">
                                        <i class="fas fa-rotate text-[11px]"></i> Simpan perubahan
                                    </button>
                                </form>
                                <form action="{{ route('researches.kesbang.reject', $research->id) }}" method="POST" data-reject-form class="rounded-xl border border-rose-100 bg-white/80 p-3 space-y-2">
                                    @csrf
                                    <p class="text-[11px] font-semibold text-rose-700 flex items-center gap-1"><i class="fas fa-rotate text-[11px]"></i> Ubah jadi tolak</p>
                                    <input type="hidden" name="alasan_penolakan" value="">
                                    <input type="hidden" name="catatan_keputusan" value="">
                                    <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-rose-200 bg-white px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50" data-reject-trigger>
                                        <i class="fas fa-ban text-[11px]"></i> Simpan perubahan
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endif

                @if($canUploadAfterKesbang && !$canModerate)
                    <a href="{{ route('researches.results.edit', $research->id) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-white">
                        <i class="fas fa-file-upload text-[11px]"></i> Unggah Hasil Penelitian
                    </a>
                @endif
                @if($waitingKesbangForUpload && !$canModerate)
                    <span class="inline-flex items-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-4 py-2 text-xs font-semibold text-amber-700">
                        <i class="fas fa-hourglass-half text-[11px]"></i> Menunggu ACC Kesbangpol untuk unggah hasil
                    </span>
                @endif
                @if($canFixRejected)
                    <a href="{{ route('researches.edit', $research->id) }}" class="inline-flex items-center gap-2 rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-xs font-semibold text-rose-700 hover:bg-white">
                        <i class="fas fa-pen text-[11px]"></i> Perbaiki & Ajukan Ulang
                    </a>
                @endif
            </div>
        </section>

        @if($isResearchFinished)
            <section class="rounded-2xl border border-emerald-100 bg-emerald-50 p-6 shadow-sm flex flex-col gap-3">
                <div class="flex items-start gap-3">
                    <span class="h-10 w-10 rounded-full bg-white text-emerald-600 flex items-center justify-center ring-4 ring-emerald-100">
                        <i class="fas fa-bullhorn"></i>
                    </span>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">Periode penelitian telah selesai</p>
                        <p class="text-sm text-emerald-800">Hubungi peneliti untuk mengunggah hasil terbaru.</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if($researcherEmail)
                        @if($canSendAutoReminder)
                            <form action="{{ route('researches.remind-results', $research) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-emerald-200 bg-white px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50">
                                    <i class="fas fa-envelope text-[11px]"></i> Kirim email otomatis
                                </button>
                            </form>
                        @else
                            <a href="mailto:{{ $researcherEmail }}?subject=Permintaan%20unggah%20hasil%20penelitian&body={{ urlencode('Halo ' . ($research->penulis ?? 'Peneliti') . ',\n\nMohon unggah hasil penelitian Anda pada tautan berikut: ' . route('researches.results.edit', $research->id) . '\n\nTerima kasih.') }}" class="inline-flex items-center gap-2 rounded-lg border border-emerald-200 bg-white px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50">
                                <i class="fas fa-envelope text-[11px]"></i> Kirim email
                            </a>
                        @endif
                    @else
                        <span class="text-xs text-emerald-700">Email peneliti belum tersedia.</span>
                    @endif
                </div>
            </section>
        @endif

        {{-- Blok aksi admin sudah terwakili di atas, tidak perlu duplikasi --}}

        @if($fileLike->isNotEmpty())
            <section class="rounded-3xl border border-emerald-50 bg-gradient-to-br from-white via-emerald-50/30 to-white shadow-sm">
                <div class="px-6 py-4 border-b border-emerald-50 bg-white/60 flex items-center justify-between">
                    <div class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-[11px] font-semibold text-emerald-700 ring-1 ring-emerald-100 shadow-sm">
                        <i class="fas fa-cloud-download-alt text-[10px]"></i> Berkas Terlampir
                    </div>
                    <span class="text-[11px] text-gray-500">Unduh semua berkas yang diunggah</span>
                </div>
                <div class="p-6">
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($fileLike as $key => $path)
                            <div class="flex items-center justify-between rounded-2xl border border-emerald-100 bg-white/80 px-4 py-3">
                                <div class="space-y-1">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-700">{{ ucwords(str_replace('_', ' ', $key)) }}</p>
                                    <p class="text-xs text-gray-600 break-all">{{ basename((string)$path) }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('researches.download', [$research, $key]) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-white px-3 py-1.5 text-[11px] font-semibold text-emerald-700 hover:bg-emerald-50">
                                        <i class="fas fa-eye text-[10px]"></i> Lihat
                                    </a>
                                    <a href="{{ route('researches.download', [$research, $key]) }}?download=1" class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-600 px-3 py-1.5 text-[11px] font-semibold text-white hover:bg-emerald-500">
                                        <i class="fas fa-download text-[10px]"></i> Unduh
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="rounded-3xl border border-gray-50 bg-white/95 backdrop-blur shadow-sm">
            <div class="px-6 py-4 border-b border-gray-50 bg-white/60">
                <div class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-[11px] font-semibold text-gray-700 ring-1 ring-gray-100 shadow-sm">
                    <i class="fas fa-list-alt text-[10px]"></i> Semua Atribut
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-left">Atribut</th>
                            <th class="px-6 py-3 text-left">Nilai</th>
                        </tr>
                    </thead>
                    @php
                        $rows = collect($attributeRows)->reject(function ($row) {
                            $val = $row['value'] ?? null;
                            $isEmpty = is_null($val) || $val === '';
                            $hiddenLabels = [
                                'Ditolak Oleh',
                                'Disetujui Oleh',
                                'Diverifikasi Kesbang Oleh',
                                'Disetujui Pada',
                                'Ditolak Pada',
                                'Alasan Penolakan',
                                'Diajukan Ulang Setelah Ditolak',
                            ];
                            return in_array($row['label'], $hiddenLabels, true) && $isEmpty;
                        });
                    @endphp
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($rows as $row)
                            @php
                                $val = $row['value'] ?? null;
                                $type = $row['type'] ?? 'text';
                                $isEmpty = is_null($val) || $val === '' || $val === '-';
                                $display = $isEmpty ? '-' : $val;
                            @endphp
                            <tr class="hover:bg-cyan-50/30 transition">
                                <td class="px-6 py-4 text-gray-600">{{ $row['label'] }}</td>
                                <td class="px-6 py-4">
                                    @if($type === 'status')
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span>
                                    @elseif($type === 'file')
                                        @if(!$isEmpty)
                                            <a class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-white" href="{{ route('researches.download', [$research, $row['key'] ?? 'berkas_pdf']) }}" target="_blank" rel="noopener">
                                                <i class="fas fa-download text-[11px]"></i> Lihat ({{ basename((string)$val) }})
                                            </a>
                                            <a class="ml-2 text-[11px] font-semibold text-emerald-700 hover:underline" href="{{ route('researches.download', [$research, $row['key'] ?? 'berkas_pdf']) }}?download=1">Unduh</a>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    @else
                                        <span class="text-gray-800 break-words">{{ is_scalar($display) ? $display : json_encode($display) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Modal satu pintu untuk semua penolakan
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 z-50 hidden items-center justify-center bg-black/40 px-4';
            modal.innerHTML = `
                <div class="w-full max-w-lg rounded-2xl bg-white shadow-xl">
                    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-3">
                        <div class="flex items-center gap-2 text-rose-600">
                            <i class="fas fa-ban text-sm"></i>
                            <p class="text-sm font-semibold">Alasan penolakan</p>
                        </div>
                        <button type="button" class="text-gray-400 hover:text-gray-600" data-reject-close>
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="px-5 py-4 space-y-3">
                        <p class="text-xs text-gray-600">Tuliskan catatan singkat agar peneliti tahu apa yang perlu diperbaiki.</p>
                        <textarea rows="3" class="w-full rounded-lg border border-rose-200 focus:border-rose-500 focus:ring-rose-500 text-sm" data-reject-text placeholder="Contoh: Dokumen rekomendasi masih kosong."></textarea>
                        <p class="text-[11px] text-gray-500">Catatan ini tersimpan bersama keputusan.</p>
                    </div>
                    <div class="flex items-center justify-end gap-2 border-t border-gray-100 px-5 py-3 bg-gray-50/80 rounded-b-2xl">
                        <button type="button" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-800" data-reject-close>Batal</button>
                        <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-500" data-reject-confirm>
                            <i class="fas fa-paper-plane text-[12px]"></i> Kirim penolakan
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            let activeRejectForm = null;
            const textArea = modal.querySelector('[data-reject-text]');
            const closeButtons = modal.querySelectorAll('[data-reject-close]');
            const confirmBtn = modal.querySelector('[data-reject-confirm]');

            const openModal = (form) => {
                activeRejectForm = form;
                textArea.value = '';
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                textArea.focus();
            };
            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                activeRejectForm = null;
            };

            closeButtons.forEach(btn => btn.addEventListener('click', closeModal));
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });

            confirmBtn.addEventListener('click', () => {
                if (!activeRejectForm) return;
                const message = textArea.value.trim();
                if (!message) {
                    textArea.focus();
                    textArea.classList.add('ring-1', 'ring-rose-500');
                    return;
                }
                textArea.classList.remove('ring-1', 'ring-rose-500');
                let field = activeRejectForm.querySelector('input[name="alasan_penolakan"]');
                if (!field) {
                    field = document.createElement('input');
                    field.type = 'hidden';
                    field.name = 'alasan_penolakan';
                    activeRejectForm.appendChild(field);
                }
                field.value = message;
                let noteField = activeRejectForm.querySelector('input[name="catatan_keputusan"]');
                if (!noteField) {
                    noteField = document.createElement('input');
                    noteField.type = 'hidden';
                    noteField.name = 'catatan_keputusan';
                    activeRejectForm.appendChild(noteField);
                }
                noteField.value = message;
                activeRejectForm.submit();
                closeModal();
            });

            document.querySelectorAll('[data-reject-trigger]').forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    const form = e.currentTarget.closest('form');
                    if (form) openModal(form);
                });
            });
        });
    </script>
</x-app-layout>



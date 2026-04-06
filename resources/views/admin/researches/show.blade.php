@php
    $attributes = $research->getAttributes();
    $fileCandidates = collect($attributes)->filter(fn($v) => is_string($v) && preg_match('/\.(pdf|docx?|xlsx?|pptx?|csv|jpg|jpeg|png|gif|svg|webp|txt|zip|rar)$/i', $v));
    $fileRows = collect();
    foreach ([
        'berkas_pdf' => 'Berkas Proposal',
        'berkas_surat_kampus' => 'Surat Pengantar Universitas/Instansi/Lembaga',
        'berkas_hasil' => 'Berkas Hasil',
        'berkas_surat_kesbang' => 'Surat Rekomendasi'
    ] as $fieldName => $label) {
        $path = $fileCandidates[$fieldName] ?? null;
        if ($path) {
            $fileRows->push(compact('label', 'path', 'fieldName'));
        }
    }
    $viewer = auth()->user();
    $isBappedaAdmin = $viewer && $viewer->hasRole('admin') && !$viewer->hasRole(['superadmin', 'kesbangpol']);
    $status = (string)($research->status ?? 'draft');
    $statusMap = [
        'approved' => ['label' => 'Disetujui', 'class' => 'bg-emerald-50 text-emerald-700'],
        'rejected' => ['label' => 'Ditolak', 'class' => 'bg-rose-50 text-rose-700'],
        'submitted' => ['label' => 'Diajukan', 'class' => 'bg-amber-50 text-amber-700'],
        'kesbang_verified' => ['label' => 'Disetujui Kesbang', 'class' => 'bg-cyan-50 text-cyan-700'],
        'default' => ['label' => 'Draft', 'class' => 'bg-gray-50 text-gray-600'],
    ];
    $statusInfo = $statusMap[$status] ?? $statusMap['default'];
    $isFinalDecision = in_array($status, ['approved', 'rejected'], true);
    $decisionBadge = $status === 'approved'
        ? ['class' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100', 'icon' => 'fa-circle-check', 'title' => 'Pengajuan disetujui']
        : ['class' => 'bg-rose-50 text-rose-700 ring-1 ring-rose-100', 'icon' => 'fa-circle-xmark', 'title' => 'Pengajuan ditolak'];
    $startDate = $research->tanggal_mulai ? \Carbon\Carbon::parse($research->tanggal_mulai) : null;
    $endDate = $research->tanggal_selesai ? \Carbon\Carbon::parse($research->tanggal_selesai) : null;
    $isInResearchPeriod = $startDate && $endDate && now()->between($startDate, $endDate);
    $minutesLeft = $isInResearchPeriod ? now()->diffInMinutes($endDate, false) : null;
    $hoursLeftInt = !is_null($minutesLeft) ? max(0, (int) ceil($minutesLeft / 60)) : null;
    $daysLeft = !is_null($minutesLeft) ? max(0, (int) ceil($minutesLeft / 1440)) : null;
    $isResearchFinished = $endDate && now()->greaterThanOrEqualTo($endDate->copy()->endOfDay());
    $isResultsMissing = !$research->hasil_diunggah_pada;
    $isApproved = $status === 'approved';
    $shouldShowResultsReminder = $isResultsMissing && ($isResearchFinished || is_null($endDate) || $isApproved);
    $research->loadMissing(['submitter', 'institution', 'field', 'rejectedBy', 'kesbangVerifier', 'approver']);
    $submitter = optional($research->submitter);
    $contactPhone = $research->telepon_peneliti ?? $submitter->phone ?? $submitter->phone_number ?? null;
    $contactEmail = $research->email_peneliti ?? $submitter->surel ?? null;
    $waNumber = $contactPhone ? preg_replace('/\D+/', '', (string)$contactPhone) : null;
    $waLink = $waNumber ? 'https://wa.me/' . $waNumber . '?text=' . urlencode('Halo ' . ($research->penulis ?? $submitter->nama ?? 'Peneliti') . ', mohon unggah hasil penelitian melalui tautan berikut: ' . route('researches.results.edit', $research->id)) : null;
    $formatDateTime = fn($value) => $value ? \Carbon\Carbon::parse($value)->format('d M Y') : null;
    $formatUser = function ($id, $relation = null) {
        return optional($relation)->nama ?: optional($relation)->surel ?: ($id ? 'ID ' . $id : null);
    };
    $researcherEmail = $research->email_peneliti ?: optional($research->submitter)->surel;
    $showResultsReminder = $isResearchFinished && !$research->hasil_diunggah_pada;
    $attributeRows = [
        ['label' => 'ID', 'value' => $research->id],
        ['label' => 'Judul', 'value' => $research->judul],
        ['label' => 'Peneliti', 'value' => $research->penulis],
        ['label' => 'NIK Peneliti', 'value' => $research->nik_peneliti],
        ['label' => 'Kontak Peneliti', 'value' => $contactPhone],
        ['label' => 'Email Peneliti', 'value' => $researcherEmail],
        ['label' => 'Institusi', 'value' => optional($research->institution)->nama],
        ['label' => 'Bidang', 'value' => optional($research->field)->nama],
        ['label' => 'Tahun', 'value' => $research->tahun],
        ['label' => 'Tanggal Mulai', 'value' => optional($research->tanggal_mulai)->format('d M Y')],
        ['label' => 'Tanggal Selesai', 'value' => optional($research->tanggal_selesai)->format('d M Y')],
        ['label' => 'Abstrak', 'value' => $research->abstrak],
        ['label' => 'Berkas Proposal', 'value' => $research->berkas_pdf, 'type' => 'file', 'key' => 'berkas_pdf'],
        ['label' => 'Surat Pengantar Universitas/Instansi/Lembaga', 'value' => $research->berkas_surat_kampus, 'type' => 'file', 'key' => 'berkas_surat_kampus'],
        ['label' => 'Nomor Surat Rekomendasi', 'value' => $research->nomor_surat_kesbang],
        ['label' => 'Tanggal Surat Rekomendasi', 'value' => optional($research->tanggal_surat_kesbang)->format('d M Y')],
        ['label' => 'Status', 'type' => 'status'],
        ['label' => 'Diajukan Oleh', 'value' => $formatUser($research->pengunggah_id, $research->submitter ?? null)],
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
    @if(session('success'))
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-4">
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-start gap-2">
                <i class="fas fa-check-circle mt-0.5"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-4">
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 flex items-start gap-2">
                <i class="fas fa-info-circle mt-0.5"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Modul Admin</p>
                <h1 class="text-2xl font-semibold text-gray-900">Detail Penelitian #{{ $research->id }}</h1>
                <p class="text-sm text-gray-500">Periksa metadata, berkas, dan lakukan keputusan akhir.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if(auth()->user()?->isSuperAdmin())
                    <form action="{{ route('researches.destroy', $research) }}" method="POST" onsubmit="return confirm('Hapus penelitian ini? Tindakan tidak dapat dibatalkan.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-100">
                            <i class="fas fa-trash text-xs"></i> Hapus
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.researches.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-white">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Keputusan Admin (letakkan di atas) --}}
        <section class="rounded-3xl border border-gray-100 bg-gradient-to-br from-white via-orange-50/40 to-white shadow-sm p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Keputusan Admin</p>
                    <h3 class="text-lg font-semibold text-gray-900">Tentukan hasil pengajuan</h3>
                    <p class="text-sm text-gray-600">Setelah Kesbangpol memverifikasi, admin dapat menyetujui atau menolak pengajuan.</p>
                </div>
                <div class="text-sm text-gray-600 flex items-center gap-2">
                    <span>Status Kesbangpol:</span>
                    @if($research->diverifikasi_kesbang_pada)
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100">Sudah diverifikasi</span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-100">Menunggu verifikasi</span>
                    @endif
                </div>
            </div>
            <div class="mt-5">
                @php
                    $canDecideNow = $research->diverifikasi_kesbang_pada || $isBappedaAdmin;
                @endphp
                @if(!$canDecideNow)
                    <div class="rounded-xl border border-amber-100 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                        Menunggu verifikasi Kesbangpol sebelum dapat di-approve atau ditolak.
                    </div>
                @else
                    @if($isFinalDecision)
                        <div class="rounded-2xl border bg-white p-5 shadow-sm space-y-3 {{ $status === 'approved' ? 'border-emerald-100' : 'border-rose-100' }}">
                            <div class="flex items-start gap-3">
                                <span class="h-12 w-12 shrink-0 rounded-full {{ $status === 'approved' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center ring-8 ring-white">
                                    <i class="fas {{ $decisionBadge['icon'] }} text-lg"></i>
                                </span>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">{{ $decisionBadge['title'] }}</p>
                                    <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                                        <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 font-semibold {{ $decisionBadge['class'] }}">{{ $statusInfo['label'] }}</span>
                                        @if($research->disetujui_pada || $research->ditolak_pada)
                                            <span class="text-gray-600">pada {{ optional($research->disetujui_pada ?? $research->ditolak_pada)->format('d M Y H:i') }}</span>
                                        @endif
                                        @if($status === 'rejected' && $research->alasan_penolakan)
                                            <span class="text-gray-500">- {{ $research->alasan_penolakan }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 rounded-xl border border-dashed border-gray-200 bg-gray-50/60 px-4 py-3 text-xs text-gray-700 space-y-3">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="font-semibold text-gray-800">Butuh ubah keputusan?</p>
                                    <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50" data-reopen-trigger>
                                        <i class="fas fa-rotate text-[12px]"></i> Ubah keputusan
                                    </button>
                                </div>
                                <div class="grid gap-3 md:grid-cols-2 hidden" data-reopen-panel>
                                    <form action="{{ route('researches.approve', $research->id) }}" method="POST" class="rounded-lg border border-emerald-100 bg-white p-4 shadow-sm space-y-2" data-reopen-action>
                                        @csrf
                                        <p class="text-xs font-semibold text-gray-900">Ubah jadi disetujui</p>
                                        <input type="hidden" name="catatan_keputusan" value="Revisi keputusan ke disetujui" disabled>
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                                            <i class="fas fa-rotate text-[12px]"></i> Terapkan
                                        </button>
                                    </form>
                                    <form action="{{ route('researches.reject', $research->id) }}" method="POST" class="rounded-lg border border-rose-100 bg-white p-4 shadow-sm space-y-2" data-reopen-action data-reject-form>
                                        @csrf
                                        <p class="text-xs font-semibold text-gray-900">Ubah jadi ditolak</p>
                                        <input type="hidden" name="catatan_keputusan" value="Revisi keputusan ke ditolak" disabled>
                                        <input type="hidden" name="alasan_penolakan" value="" disabled>
                                        <button type="button" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-500" data-reject-trigger>
                                            <i class="fas fa-rotate text-[12px]"></i> Terapkan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="grid gap-4 md:grid-cols-2">
                            <form action="{{ route('researches.approve', $research->id) }}" method="POST" class="rounded-2xl border border-emerald-100 bg-white p-4 shadow-sm flex flex-col gap-3">
                                @csrf
                                <div class="flex items-start gap-3">
                                    <span class="h-10 w-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                        <i class="fas fa-check text-sm"></i>
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">Setujui Pengajuan</p>
                                        <p class="text-xs text-gray-600">Publikasikan setelah semua berkas dinyatakan valid.</p>
                                    </div>
                                </div>
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-500 shadow-md shadow-emerald-100">
                                    <i class="fas fa-check-circle text-[13px]"></i> Setujui
                                </button>
                            </form>
                            <form action="{{ route('researches.reject', $research->id) }}" method="POST" class="rounded-2xl border border-rose-100 bg-white p-4 shadow-sm flex flex-col gap-3" data-reject-form>
                                @csrf
                                <div class="flex items-start gap-3">
                                    <span class="h-10 w-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center">
                                        <i class="fas fa-times text-sm"></i>
                                    </span>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900">Tolak Pengajuan</p>
                                        <p class="text-xs text-gray-600">Berikan alasan jelas agar peneliti dapat memperbaiki.</p>
                                    </div>
                                </div>
                                <input type="hidden" name="alasan_penolakan" value="">
                                <input type="hidden" name="catatan_keputusan" value="">
                                <button type="button" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-3 text-sm font-semibold text-white hover:bg-rose-500 shadow-md shadow-rose-100" data-reject-trigger>
                                    <i class="fas fa-times-circle text-[13px]"></i> Tolak
                                </button>
                            </form>
                        </div>
                    @endif
                @endif
            </div>
        </section>

        <section class="rounded-2xl border border-orange-100 bg-white/90 p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs uppercase font-semibold tracking-wide text-gray-500">Judul Penelitian</p>
                    <h2 class="text-xl font-semibold text-gray-900 mt-1">{{ $research->judul ?? $research->judul ?? '-' }}</h2>
                    <p class="text-sm text-gray-500 mt-2">Peneliti: {{ $research->penulis ?? $submitter->nama ?? '-' }}</p>
                    <p class="text-sm text-gray-500">Institusi: {{ optional($submitter->institution)->nama ?? optional($research->institution)->nama ?? '-' }}</p>
                    @if($research->diajukan_ulang_pada)
                        <div class="mt-3 inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-100">
                            <i class="fas fa-rotate text-[11px]"></i>
                            Perbaikan setelah ditolak ({{ optional($research->diajukan_ulang_pada)->format('d M Y H:i') }})
                        </div>
                    @endif
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span>
                    <p class="text-xs text-gray-500">Diperbarui: {{ optional($research->diubah_pada)->format('d M Y H:i') ?? '-' }}</p>
                </div>
            </div>
            @if(!$research->diverifikasi_kesbang_pada)
                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 flex items-start gap-2">
                    <i class="fas fa-hourglass-half mt-0.5"></i>
                    <div>
                        <p class="font-semibold">Menunggu verifikasi Kesbangpol</p>
                        <p class="text-amber-700">Data sudah terlihat oleh BAPPPEDA, namun keputusan akhir hanya bisa dilakukan setelah Kesbangpol memverifikasi.</p>
                    </div>
                </div>
            @endif
            @if($isInResearchPeriod)
                <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-start gap-2">
                    <i class="fas fa-circle-play mt-0.5 text-emerald-500"></i>
                    <div>
                        <p class="font-semibold">Sedang dalam masa penelitian</p>
                        <p class="text-emerald-700">Periode aktif hingga {{ optional($endDate)->format('d M Y') }}.</p>
                    </div>
                </div>
            @endif
            @if($shouldShowResultsReminder)
                <div class="mt-4 rounded-xl border border-orange-200 bg-orange-50 px-4 py-3 text-sm text-orange-800 flex flex-col gap-3">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-bell mt-0.5 text-orange-600"></i>
                        <div>
                            <p class="font-semibold">
                                @if($isResearchFinished)
                                    Masa penelitian selesai
                                @elseif(is_null($endDate))
                                    Tanggal selesai belum diisi
                                @elseif($isApproved)
                                    Pengajuan sudah disetujui
                                @endif
                            </p>
                            <p class="text-orange-700">Hasil belum diunggah. Kirim email pengingat ke peneliti.</p>
                            @if($researcherEmail)
                                <p class="text-[11px] text-orange-700/80 mt-1">Email tujuan: {{ $researcherEmail }}</p>
                            @else
                                <p class="text-[11px] text-rose-700/80 mt-1">Email peneliti belum tersedia.</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <form action="{{ route('admin.researches.remind-results', $research) }}" method="POST" class="inline-flex">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-orange-600 px-4 py-2 text-xs font-semibold text-white hover:bg-orange-500 disabled:opacity-50" {{ $researcherEmail ? '' : 'disabled' }}>
                                <i class="fas fa-envelope text-[11px]"></i> Kirim email pengingat
                            </button>
                        </form>
                    </div>
                </div>
            @endif
            @if($research->berkas_surat_kesbang)
                <div class="mt-4">
                    <div class="rounded-xl border border-cyan-100 bg-white/90 px-4 py-3 space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('admin.researches.download', [$research, 'berkas_surat_kesbang']) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-white">
                                <i class="fas fa-file-signature text-[11px]"></i> Unduh Surat Rekomendasi
                            </a>
                            <span class="inline-flex items-center gap-2 rounded-full bg-cyan-50 px-3 py-1 text-[11px] font-semibold text-cyan-700 ring-1 ring-cyan-100">
                                <i class="fas fa-shield-halved text-[10px]"></i> Terunggah oleh Kesbangpol
                            </span>
                        </div>
                        <div class="grid gap-2 sm:grid-cols-2 text-xs text-gray-600">
                            @if($research->nomor_surat_kesbang)
                                <p>Nomor surat: <span class="font-semibold text-gray-800">{{ $research->nomor_surat_kesbang }}</span></p>
                            @endif
                            @if($research->tanggal_surat_kesbang)
                                <p>Tanggal surat: <span class="font-semibold text-gray-800">{{ optional($research->tanggal_surat_kesbang)->format('d M Y') }}</span></p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            @if($research->alasan_penolakan)
                @php
                    $rejector = optional($research->rejectedBy);
                    $rejectorRole = $rejector?->hasKesbangAccess() ? 'Kesbangpol' : ($rejector?->hasAdminAccess() ? 'BAPPPEDA' : 'Admin');
                    $rejectTime = optional($research->ditolak_pada)->format('d M Y H:i');
                    $isActiveReject = $research->status === 'rejected';
                @endphp
                <div class="mt-4 rounded-xl border px-4 py-3 text-sm {{ $isActiveReject ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-amber-200 bg-amber-50 text-amber-700' }}">
                    <div class="font-semibold flex items-center gap-1">
                        <i class="fas {{ $isActiveReject ? 'fa-circle-xmark' : 'fa-info-circle' }} text-[11px]"></i>
                        <span>{{ $isActiveReject ? 'Pengajuan ditolak' : 'Catatan penolakan sebelumnya' }}</span>
                    </div>
                    <p class="mt-1">{{ $research->alasan_penolakan }}</p>
                    <p class="mt-2 text-[11px] text-current/80">Ditolak oleh {{ $rejectorRole }}{{ $rejectTime ? ' pada ' . $rejectTime : '' }}</p>
                    @unless($isActiveReject)
                        <p class="mt-1 text-[11px] text-current/70">Catatan ini tampil sampai diverifikasi ulang.</p>
                    @endunless
                </div>
            @endif
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Atribut Lengkap</h3>
                <span class="text-xs text-gray-500">{{ count($attributeRows) }} kolom</span>
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
                                'Disetujui Oleh',
                                'Ditolak Oleh',
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
                            <tr class="hover:bg-orange-50/30 transition">
                                <td class="px-6 py-4 text-gray-600">{{ $row['label'] }}</td>
                                <td class="px-6 py-4">
                                    @if($type === 'status')
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span>
                                    @elseif($type === 'file')
                                        @if(!$isEmpty)
                                            <a class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-gray-800" href="{{ route('admin.researches.download', [$research, $row['key'] ?? 'berkas_pdf']) }}">
                                                <i class="fas fa-download text-[11px]"></i> Lihat ({{ basename((string)$val) }})
                                            </a>
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

            const reopenTrigger = document.querySelector('[data-reopen-trigger]');
            const reopenPanel = document.querySelector('[data-reopen-panel]');
            if (reopenTrigger && reopenPanel) {
                reopenTrigger.addEventListener('click', () => {
                    reopenPanel.classList.remove('hidden');
                    reopenTrigger.classList.add('hidden');
                    reopenPanel.querySelectorAll('[data-reopen-action]').forEach((form) => {
                        form.querySelectorAll('input,button').forEach((el) => el.removeAttribute('disabled'));
                    });
                });
            }
        });
    </script>
</x-app-layout>



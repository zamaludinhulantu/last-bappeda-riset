<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Riset Saya</p>
                <h2 class="text-2xl font-semibold text-gray-900">{{ __('Daftar Penelitian') }}</h2>
                <p class="text-sm text-gray-500">Pantau status pengajuan dan buka detail untuk memperbarui informasi.</p>
            </div>
            <a href="{{ route('researches.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
                <i class="fas fa-plus text-xs"></i> Unggah Penelitian
            </a>
        </div>
    </x-slot>

    @php
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser?->isSuperAdmin();
        $isPaginator = method_exists($researches, 'firstItem');
        $startNumber = $isPaginator ? $researches->firstItem() : 1;
        $endNumber = $isPaginator ? $researches->lastItem() : $researches->count();
        $total = method_exists($researches, 'total') ? $researches->total() : $researches->count();
    @endphp

    <div class="space-y-6">
        @if($currentUser?->hasKesbangAccess() || $currentUser?->hasAdminAccess())
            <section class="rounded-2xl border border-cyan-100 bg-white/90 p-4 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-600">Export</p>
                        <h3 class="text-base font-semibold text-gray-900">Unduh CSV penelitian</h3>
                        <p class="text-xs text-gray-500">Filter opsional: tahun/bulan & dasar tanggal pengajuan/verifikasi.</p>
                    </div>
                    <form action="{{ route('researches.export') }}" method="GET" class="flex flex-wrap items-end gap-2">
                        <div>
                            <label class="text-[11px] font-semibold text-gray-700">Tahun</label>
                            <input type="number" name="tahun" min="2000" max="{{ date('Y') }}" class="mt-1 w-24 rounded-lg border border-gray-200 bg-white text-sm focus:border-cyan-500 focus:ring-cyan-500" placeholder="Tahun">
                        </div>
                            <div>
                                <label class="text-[11px] font-semibold text-gray-700">Bulan</label>
                            <select name="month" class="mt-1 rounded-lg border border-gray-200 bg-white text-sm focus:border-cyan-500 focus:ring-cyan-500">
                                <option value="">-</option>
                                @foreach(range(1,12) as $m)
                                    <option value="{{ $m }}">{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label class="inline-flex items-center gap-2 text-[11px] font-semibold text-gray-700">
                            <input type="checkbox" name="all" value="1" class="rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                            Semua data
                        </label>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-cyan-600 px-4 py-2 text-xs font-semibold text-white hover:bg-cyan-500">
                            <i class="fas fa-download text-[11px]"></i> Export CSV
                        </button>
                        <a href="{{ route('researches.export.pdf', request()->only(['tahun','month','all'])) }}" class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-500">
                            <i class="fas fa-file-pdf text-[11px]"></i> Export PDF
                        </a>
                        <span class="text-[11px] text-gray-500">Isi tahun/bulan atau centang semua data sebelum ekspor.</span>
                    </form>
                </div>
            </section>
        @endif

        <section class="rounded-3xl border border-cyan-50 bg-gradient-to-br from-white via-cyan-50/30 to-white shadow-sm overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-cyan-50 bg-white/60 backdrop-blur">
                <div>
                    <p class="text-xs uppercase font-semibold tracking-wide text-gray-500">Total Pengajuan</p>
                    <p class="text-sm text-gray-600">Menampilkan {{ $startNumber }}-{{ $endNumber }} dari {{ $total }} penelitian</p>
                </div>
                <div class="flex items-center gap-2 text-xs font-semibold">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-cyan-700 ring-1 ring-cyan-100 shadow-sm">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Status realtime
                    </span>
                    <span class="hidden sm:inline text-gray-400">•</span>
                    <span class="hidden sm:inline text-gray-500">Perbarui info dari detail penelitian</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-white text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-left">Judul</th>
                            <th class="px-6 py-3 text-left">Bidang</th>
                            <th class="px-6 py-3 text-left">Institusi</th>
                            <th class="px-6 py-3 text-left">Periode</th>
                            <th class="px-6 py-3 text-left">Kontak</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($researches as $research)
                            @php
                                $status = (string)($research->status ?? 'submitted');
                                $statusMap = [
                                    'approved' => ['label' => 'Disetujui', 'class' => 'bg-emerald-50 text-emerald-700'],
                                    'rejected' => ['label' => 'Ditolak', 'class' => 'bg-rose-50 text-rose-700'],
                                    'submitted' => ['label' => 'Diajukan', 'class' => 'bg-amber-50 text-amber-700'],
                                    'kesbang_verified' => ['label' => 'Disetujui Kesbang', 'class' => 'bg-cyan-50 text-cyan-700'],
                                    'default' => ['label' => 'Diajukan', 'class' => 'bg-gray-50 text-gray-600'],
                                ];
                                $statusInfo = $statusMap[$status] ?? $statusMap['default'];
                                $canEdit = $isSuperAdmin || in_array($status, ['submitted', 'rejected'], true);
                                $canDelete = $isSuperAdmin || in_array($status, ['submitted'], true);
                                $startDate = optional($research->tanggal_mulai ?? $research->diajukan_pada ?? $research->dibuat_pada)->format('d M Y');
                                $endDate = optional($research->tanggal_selesai ?? $research->diajukan_pada ?? $research->dibuat_pada)->format('d M Y');
                                $yearLabel = $research->tahun
                                    ?: optional($research->tanggal_mulai)->format('Y')
                                    ?: optional($research->tanggal_selesai)->format('Y')
                                    ?: optional($research->diajukan_pada ?? $research->dibuat_pada)->format('Y')
                                    ?: 'Belum diisi';
                                $periodLabel = $startDate && $endDate
                                    ? $startDate . ' s/d ' . $endDate
                                    : ($startDate ?: ($endDate ?: 'Belum diisi'));
                            @endphp
                            <tr class="hover:bg-cyan-50/30 transition">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900 line-clamp-2">{{ $research->judul }}</p>
                                    <p class="text-xs text-gray-500 mt-1">Peneliti: {{ $research->penulis ?? '-' }}</p>
                                    <p class="text-[11px] text-gray-400 mt-1">ID: {{ $research->id }}</p>
                                </td>
                                <td class="px-6 py-4 text-gray-700">{{ optional($research->field)->nama ?? '-' }}</td>
                                <td class="px-6 py-4 text-gray-700">
                                    <p class="font-semibold text-gray-900">{{ optional($research->institution)->nama ?? 'Institusi belum diisi' }}</p>
                                    @if($research->kata_kunci)
                                        <p class="text-xs text-gray-500 line-clamp-1 mt-1">{{ $research->kata_kunci }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    <div class="space-y-1 text-xs">
                                        <span class="inline-flex items-center rounded-full bg-cyan-50 px-3 py-1 font-semibold text-cyan-700 ring-1 ring-cyan-100">Periode: {{ $periodLabel }}</span>
                                        <span class="inline-flex items-center rounded-full bg-slate-50 px-2.5 py-1 text-[11px] font-semibold text-slate-600 ring-1 ring-slate-100">Tahun {{ $yearLabel }}</span>
                                        @if($research->tanggal_mulai && $research->tanggal_selesai && now()->between($research->tanggal_mulai, $research->tanggal_selesai))
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 ring-1 ring-emerald-100">
                                                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span> Berjalan
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    <div class="space-y-1 text-xs text-gray-600">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-id-card text-[10px] text-gray-400"></i>
                                            <span>{{ $research->nik_peneliti ?? '-' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-phone text-[10px] text-gray-400"></i>
                                            <span>{{ $research->telepon_peneliti ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusInfo['class'] }}">
                                        {{ $statusInfo['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ route('researches.show', $research->id) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-white">
                                            <i class="fas fa-eye text-[11px]"></i> Detail
                                        </a>
                                        @if($canEdit)
                                            <a href="{{ route('researches.edit', $research->id) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-white">
                                                <i class="fas fa-pen text-[11px]"></i> Edit
                                            </a>
                                        @endif
                                        @if($canDelete)
                                            <form action="{{ route('researches.destroy', $research->id) }}" method="POST" onsubmit="return confirm('Hapus penelitian ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">
                                                    <i class="fas fa-trash text-[11px]"></i> Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-6 text-center text-gray-500 text-sm">Belum ada penelitian. Unggah penelitian pertama Anda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-5 bg-white/70">
                {{ $researches->links() }}
            </div>
        </section>
    </div>
</x-app-layout>


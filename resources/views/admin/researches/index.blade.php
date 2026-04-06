<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">BAPPPEDA Riset</p>
                <h1 class="text-2xl font-semibold text-gray-900">Katalog Penelitian Disetujui</h1>
                <p class="text-sm text-gray-500">Hanya menampilkan penelitian yang sudah diverifikasi Kesbangpol dan siap diputuskan/ditayangkan admin.</p>
            </div>
            @if(auth()->user()?->peran !== 'admin')
                <a href="{{ route('researches.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
                    <i class="fas fa-upload text-xs"></i> Unggah Baru
                </a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6 max-w-4xl mx-auto px-4 lg:px-0">
        <section class="rounded-2xl border border-slate-100 bg-gradient-to-br from-slate-50 via-white to-slate-50 p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Katalog</p>
                    <h2 class="text-xl font-semibold text-gray-900">Lihat Katalog</h2>
                    <p class="text-sm text-gray-600">Judul atau Penulis, Bidang Penelitian, Tahun, Institusi</p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.researches.index') }}" class="mt-6 grid gap-3 grid-cols-1 md:grid-cols-2 lg:grid-cols-12 items-end">
                <div class="lg:col-span-4 md:col-span-2">
                    <label for="q" class="text-sm font-medium text-gray-700">Judul atau Penulis</label>
                    <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Masukkan kata kunci" class="mt-1 w-full rounded-lg border-gray-200 bg-white focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div class="lg:col-span-3 md:col-span-2">
                    <label for="bidang_id" class="text-sm font-medium text-gray-700">Bidang Penelitian</label>
                    <select id="bidang_id" name="bidang_id" class="mt-1 w-full rounded-lg border-gray-200 bg-white focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Semua bidang</option>
                        @foreach($fields ?? [] as $field)
                            <option value="{{ $field->id }}" @selected(request('bidang_id') == $field->id)>{{ $field->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2 md:col-span-1">
                    <label for="tahun" class="text-sm font-medium text-gray-700">Tahun</label>
                    <select id="tahun" name="tahun" class="mt-1 w-full rounded-lg border-gray-200 bg-white focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Semua tahun</option>
                        @foreach(($years ?? []) as $year)
                            <option value="{{ $year }}" @selected(request('tahun') == $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-6 md:col-span-2">
                    <label for="institution" class="text-sm font-medium text-gray-700">Institusi</label>
                    <input type="text" id="institution" name="institution" value="{{ request('institution') }}" placeholder="Nama institusi" class="mt-1 w-full rounded-lg border-gray-200 bg-white focus:border-orange-500 focus:ring-orange-500">
                </div>
                <div class="lg:col-span-6 md:col-span-2 flex flex-wrap justify-start md:justify-end items-center gap-2">
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-5 py-2 text-sm font-semibold text-white hover:bg-gray-800 shadow-sm w-full md:w-auto">Terapkan Filter</button>
                    @if(request()->hasAny(['q','status','bidang_id','tahun','institution']))
                        <a href="{{ route('admin.researches.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 w-full md:w-auto">Reset</a>
                    @endif
                </div>
            </form>
        </section>

        <section class="rounded-2xl border border-cyan-100 bg-white/95 p-4 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-600">Export</p>
                    <h3 class="text-base font-semibold text-gray-900">Unduh CSV per tahun/bulan</h3>
                    <p class="text-xs text-gray-500">Hanya untuk admin atau Kesbangpol.</p>
                </div>
                <form action="{{ route('researches.export') }}" method="GET" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="text-xs font-semibold text-gray-700">Tahun</label>
                    <select name="tahun" class="mt-1 rounded-lg border border-gray-200 bg-white text-sm focus:border-cyan-500 focus:ring-cyan-500">
                        <option value="">Semua</option>
                        @foreach($years ?? [] as $year)
                                <option value="{{ $year }}" @selected(request('tahun') == $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700">Bulan</label>
                        <select name="month" class="mt-1 rounded-lg border border-gray-200 bg-white text-sm focus:border-cyan-500 focus:ring-cyan-500">
                            <option value="">Semua</option>
                            @foreach(range(1,12) as $m)
                                <option value="{{ $m }}" @selected(request('month') == $m)>{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <label class="inline-flex items-center gap-2 text-xs font-semibold text-gray-700">
                        <input type="checkbox" name="all" value="1" class="rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                        Semua data (abaikan tahun/bulan)
                    </label>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-cyan-600 px-4 py-2 text-xs font-semibold text-white hover:bg-cyan-500">
                        <i class="fas fa-download text-[11px]"></i> Export CSV
                    </button>
                    <a href="{{ route('researches.export.pdf', request()->only(['tahun','month','all'])) }}" class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-500">
                        <i class="fas fa-file-pdf text-[11px]"></i> Export PDF
                    </a>
                    <span class="text-[11px] text-gray-500">Isi tahun/bulan atau centang semua data lalu klik Export.</span>
                </form>
            </div>
        </section>

        @php
            $isPaginator = method_exists($researches, 'firstItem');
            $startNumber = $isPaginator ? $researches->firstItem() : 1;
            $endNumber = $isPaginator ? $researches->lastItem() : $researches->count();
            $total = method_exists($researches, 'total') ? $researches->total() : $researches->count();
        @endphp
        <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur p-0 shadow">
            <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-gray-100">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Daftar Penelitian</p>
                    <p class="text-sm text-gray-500">Menampilkan {{ $startNumber }}-{{ $endNumber }} dari {{ $total }} entri</p>
                </div>
                <div class="inline-flex items-center gap-2 rounded-full bg-orange-50 px-3 py-1 text-xs font-semibold text-orange-700">
                    <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                    Sinkron real-time
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-left">Judul</th>
                            <th class="px-6 py-3 text-left">Peneliti</th>
                            <th class="px-6 py-3 text-left">Bidang</th>
                            <th class="px-6 py-3 text-left">Institusi</th>
                            <th class="px-6 py-3 text-left">Periode</th>
                            <th class="px-6 py-3 text-left">Kontak</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($researches as $research)
                            @php
                                $status = (string)($research->status ?? 'submitted');
                                $statusMap = [
                                    'approved' => ['label' => 'Disetujui', 'class' => 'bg-emerald-50 text-emerald-700'],
                                    'rejected' => ['label' => 'Ditolak', 'class' => 'bg-rose-50 text-rose-700'],
                                    'submitted' => ['label' => 'Diajukan', 'class' => 'bg-amber-50 text-amber-700'],
                                    'kesbang_verified' => ['label' => 'Disetujui Kesbang', 'class' => 'bg-cyan-50 text-cyan-700'],
                                    'default' => ['label' => 'Draft', 'class' => 'bg-gray-50 text-gray-600'],
                                ];
                                $statusInfo = $statusMap[$status] ?? $statusMap['default'];
                            @endphp
                            <tr class="hover:bg-orange-50/40 transition">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900 line-clamp-2">{{ $research->judul ?? $research->judul ?? '-' }}</p>
                                    <p class="text-xs text-gray-500 mt-1">ID #{{ $research->id }}</p>
                                </td>
                                <td class="px-6 py-4 text-gray-700">{{ $research->penulis ?? optional($research->user)->nama ?? '-' }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ optional($research->field)->nama ?? '-' }}</td>
                                <td class="px-6 py-4 text-gray-700">
                                    <p class="font-semibold text-gray-900">{{ optional($research->institution)->nama ?? 'Institusi belum diisi' }}</p>
                                    @if($research->kata_kunci)
                                        <p class="text-xs text-gray-500 line-clamp-1 mt-1">{{ $research->kata_kunci }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    @php
                                        $startDate = optional($research->tanggal_mulai)->format('d M Y');
                                        $endDate = optional($research->tanggal_selesai)->format('d M Y');
                                        $periodLabel = $startDate && $endDate
                                            ? $startDate . ' s/d ' . $endDate
                                            : ($startDate ?? ($endDate ?? '-'));
                                    @endphp
                                    <div class="space-y-1 text-xs">
                                        <span class="inline-flex items-center rounded-full bg-orange-50 px-2.5 py-1 text-[11px] font-semibold text-orange-700 ring-1 ring-orange-100">Periode: {{ $periodLabel }}</span>
                                        <span class="inline-flex items-center rounded-full bg-slate-50 px-2 py-1 text-[11px] font-semibold text-slate-600 ring-1 ring-slate-100">Tahun {{ $research->tahun ?? '-' }}</span>
                                        @if($research->tanggal_mulai && $research->tanggal_selesai && now()->between($research->tanggal_mulai, $research->tanggal_selesai))
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 ring-1 ring-emerald-100">
                                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Berjalan
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
                                    @if($research->diajukan_ulang_pada)
                                        <span class="mt-1 inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-700 ring-1 ring-amber-100">
                                            <i class="fas fa-rotate text-[10px] mr-1"></i> Perbaikan peneliti
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex flex-col items-end gap-2">
                                        <a href="{{ route('admin.researches.show', $research) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-eye text-[11px]"></i> Detail
                                        </a>
                                        <div class="flex flex-wrap justify-end gap-1.5">
                                            @if($research->berkas_pdf)
                                                <a href="{{ route('admin.researches.download', [$research, 'berkas_pdf']) }}" class="inline-flex items-center gap-1 rounded-full border border-gray-200 px-3 py-1 text-[11px] font-semibold text-gray-700 hover:bg-gray-50">
                                                    <i class="fas fa-file-alt text-[10px]"></i> Proposal
                                                </a>
                                            @endif
                                            @if($research->berkas_surat_kesbang)
                                                <a href="{{ route('admin.researches.download', [$research, 'berkas_surat_kesbang']) }}" class="inline-flex items-center gap-1 rounded-full border border-cyan-200 px-3 py-1 text-[11px] font-semibold text-cyan-700 hover:bg-cyan-50">
                                                    <i class="fas fa-file-signature text-[10px]"></i> Surat Rekom
                                                </a>
                                            @endif
                                            @if(auth()->user()?->isSuperAdmin())
                                                <form action="{{ route('researches.destroy', $research) }}" method="POST" onsubmit="return confirm('Hapus penelitian ini? Tindakan tidak dapat dibatalkan.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center gap-1 rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-[11px] font-semibold text-rose-700 hover:bg-rose-100">
                                                        <i class="fas fa-trash text-[10px]"></i> Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-6 text-center text-sm text-gray-500">Belum ada data yang memenuhi filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div>
            {{ $researches->links() }}
        </div>
    </div>
</x-app-layout>


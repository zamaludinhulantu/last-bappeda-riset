@php use Illuminate\Support\Str; @endphp
@php
    $statusStyles = [
        'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
        'rejected' => 'bg-rose-50 text-rose-700 ring-rose-100',
        'submitted' => 'bg-amber-50 text-amber-700 ring-amber-100',
        'kesbang_verified' => 'bg-blue-50 text-blue-700 ring-blue-100',
        'draft' => 'bg-gray-50 text-gray-600 ring-gray-100',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Pengumuman</p>
                <h2 class="text-2xl font-semibold text-gray-900">{{ __('Status Pengajuan & Hasil') }}</h2>
                <p class="text-sm text-gray-500">Rangkuman keputusan terbaru tanpa perlu membuka dashboard utama.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-white">
                    <i class="fas fa-gauge text-xs"></i> Kembali ke Dashboard
                </a>
                <a href="{{ $isAdminPanel ? route('admin.researches.index') : route('researches.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
                    <i class="fas fa-database text-xs"></i> Lihat Data
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="rounded-2xl border border-orange-100 bg-white/90 p-6 shadow-sm">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4">
                    <p class="text-xs uppercase font-semibold tracking-wide text-emerald-600">Disetujui</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $counts['approved'] ?? 0 }}</p>
                    <p class="text-xs text-gray-600 mt-1">Siap dipublikasikan atau diunduh.</p>
                </div>
                <div class="rounded-xl border border-amber-100 bg-amber-50 p-4">
                    <p class="text-xs uppercase font-semibold tracking-wide text-amber-600">Menunggu Keputusan</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $counts['submitted'] ?? 0 }}</p>
                    <p class="text-xs text-gray-600 mt-1">Proses verifikasi BAPPPEDA / Kesbangpol.</p>
                </div>
                <div class="rounded-xl border border-rose-100 bg-rose-50 p-4">
                    <p class="text-xs uppercase font-semibold tracking-wide text-rose-600">Ditolak</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $counts['rejected'] ?? 0 }}</p>
                    <p class="text-xs text-gray-600 mt-1">Perlu revisi sebelum ajukan ulang.</p>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-gray-100">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Update Terbaru</p>
                    <h3 class="text-xl font-semibold text-gray-900">Pengumuman Persetujuan / Penolakan</h3>
                    <p class="text-sm text-gray-500">Urut berdasarkan tanggal keputusan atau pengajuan terakhir.</p>
                </div>
                <div class="flex items-center gap-2 text-sm">
                    <span class="inline-flex items-center gap-2 rounded-full bg-orange-50 px-3 py-1 font-semibold text-orange-700">
                        <i class="fas fa-bell text-xs"></i> Real-time
                    </span>
                </div>
            </div>

            @if($announcements->count())
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 p-6">
                    @foreach($announcements as $item)
                        @php
                            $status = $item['status'] ?? 'draft';
                            $badgeClass = $statusStyles[$status] ?? 'bg-gray-50 text-gray-600 ring-gray-100';
                            $eventDate = $item['event_at'];
                        @endphp
                        <article class="rounded-xl border border-gray-100 bg-gray-50/70 p-4 hover:bg-white hover:shadow-sm transition">
                            <div class="flex items-center justify-between gap-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $badgeClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $eventDate ? $eventDate->translatedFormat('d M Y') : 'Tanggal belum tersedia' }}
                                </span>
                            </div>
                            <h4 class="mt-3 text-base font-semibold text-gray-900 line-clamp-2">{{ $item['title'] }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $item['author'] }}</p>
                            <div class="mt-3 text-xs text-gray-500 space-y-1">
                                <p><i class="fas fa-building text-[10px] mr-1.5"></i>{{ $item['institution'] ?? 'Institusi belum diisi' }}</p>
                                <p><i class="fas fa-tags text-[10px] mr-1.5"></i>{{ $item['field'] ?? 'Bidang umum' }}</p>
                            </div>
                            @if(!empty($item['note']))
                                <p class="mt-3 text-xs text-gray-600 border-t border-dashed border-gray-200 pt-2 leading-relaxed">
                                    {{ Str::limit($item['note'], 140) }}
                                </p>
                            @endif
                            <div class="mt-4 flex items-center justify-between text-xs font-semibold text-orange-700">
                                <a href="{{ route($isAdminPanel ? 'admin.researches.show' : 'researches.show', $item['id']) }}" class="inline-flex items-center gap-1 hover:text-orange-600">
                                    Buka detail <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                                <span class="inline-flex items-center gap-1 text-[11px] text-gray-500">
                                    <i class="fas fa-clock text-[10px]"></i> Terbaru
                                </span>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="p-6 text-center text-sm text-gray-600">
                    <p class="font-semibold text-gray-900">Belum ada pengumuman.</p>
                    <p>Keputusan akan muncul otomatis setelah admin memproses pengajuan Anda.</p>
                </div>
            @endif
        </section>
    </div>
</x-app-layout>


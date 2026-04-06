@php
    $user = Auth::user();
    $currentRole = $user->peran;
    $isAdminPanel = $user->hasRole(['admin', 'superadmin']);
    $isKesbang = $user->hasRole('kesbangpol');
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Ringkasan</p>
                <h2 class="text-2xl font-semibold text-gray-900">{{ __('Dashboard') }}</h2>
                <p class="text-sm text-gray-500">Pantau progres pengajuan dan status persetujuan penelitian.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-2 rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700 capitalize">
                    <span class="h-2 w-2 rounded-full bg-orange-500"></span>{{ str_replace('_',' ',Auth::user()->peran) }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="relative overflow-hidden rounded-3xl border border-cyan-100 bg-white shadow-sm">
            <div class="absolute inset-0 opacity-70" aria-hidden="true" style="background-image: radial-gradient(circle at 18% 20%, rgba(14,165,233,0.08) 0, transparent 35%), radial-gradient(circle at 80% 0%, rgba(249,115,22,0.12) 0, transparent 30%), linear-gradient(120deg, rgba(14,165,233,0.08), rgba(249,115,22,0.05));"></div>
            <div class="relative p-6 sm:p-8 space-y-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="space-y-2">
                        <p class="text-sm font-semibold text-cyan-700">Halo, {{ Auth::user()->nama }}</p>
                        <p class="text-sm text-gray-700">
                            @if($isAdminPanel)
                                Gunakan angka ini untuk memantau antrian verifikasi dan publikasi.
                            @elseif($isKesbang)
                                Ringkasan status verifikasi Kesbangpol; data otomatis terbarui saat admin BAPPPEDA memberi keputusan.
                            @else
                                Ringkasan status penelitian Anda akan diperbarui otomatis setiap ada feedback dari admin.
                            @endif
                        </p>
                        <div class="inline-flex items-center gap-2 rounded-full bg-white/70 px-3 py-1 text-[11px] font-semibold text-cyan-700 ring-1 ring-white/60 shadow-sm">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Terakhir diakses: {{ now()->translatedFormat('d F Y') }}
                        </div>
                    </div>
                    <div class="flex flex-col items-end gap-2 text-sm">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/80 px-3 py-1 text-xs font-semibold text-gray-800 ring-1 ring-gray-100">
                            <i class="fas fa-user-shield text-[11px] text-cyan-600"></i>
                            {{ Str::title(str_replace('_',' ',Auth::user()->peran)) }}
                        </span>
                        <span class="text-xs text-gray-500">SIPRISDA • Status langsung</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="rounded-2xl border border-white/80 bg-gradient-to-br from-cyan-50 via-white to-white p-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <span class="h-10 w-10 rounded-xl bg-white text-cyan-600 flex items-center justify-center shadow-sm ring-1 ring-cyan-100">
                                <i class="fas fa-database"></i>
                            </span>
                            <div>
                                <p class="text-xs uppercase font-semibold tracking-wide text-cyan-700">Total Penelitian</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $total }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-white/80 bg-gradient-to-br from-emerald-50 via-white to-white p-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <span class="h-10 w-10 rounded-xl bg-white text-emerald-600 flex items-center justify-center shadow-sm ring-1 ring-emerald-100">
                                <i class="fas fa-check-circle"></i>
                            </span>
                            <div>
                                <p class="text-xs uppercase font-semibold tracking-wide text-emerald-700">Disetujui</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $approved }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-white/80 bg-gradient-to-br from-rose-50 via-white to-white p-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <span class="h-10 w-10 rounded-xl bg-white text-rose-600 flex items-center justify-center shadow-sm ring-1 ring-rose-100">
                                <i class="fas fa-times-circle"></i>
                            </span>
                            <div>
                                <p class="text-xs uppercase font-semibold tracking-wide text-rose-700">Ditolak</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $rejected }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-white/80 bg-gradient-to-br from-amber-50 via-white to-white p-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <span class="h-10 w-10 rounded-xl bg-white text-amber-600 flex items-center justify-center shadow-sm ring-1 ring-amber-100">
                                <i class="fas fa-paper-plane"></i>
                            </span>
                            <div>
                                <p class="text-xs uppercase font-semibold tracking-wide text-amber-700">Diajukan</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $submitted }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @php
            $summaryValues = [
                ['label' => 'Total', 'value' => $total ?? 0, 'color' => '#0f3d73'],
                ['label' => 'Disetujui', 'value' => $approved ?? 0, 'color' => '#22c55e'],
                ['label' => 'Ditolak', 'value' => $rejected ?? 0, 'color' => '#ef4444'],
                ['label' => 'Diajukan', 'value' => $submitted ?? 0, 'color' => '#0ea5e9'],
            ];
            $maxSummary = max(1, collect($summaryValues)->max('value'));
        @endphp
        <section class="rounded-3xl border border-cyan-50 bg-gradient-to-br from-white via-cyan-50/40 to-white shadow-sm p-6 sm:p-8">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-[11px] font-semibold text-cyan-700 ring-1 ring-cyan-100 shadow-sm">
                        <i class="fas fa-signal text-[10px]"></i> Ringkasan Visual
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Distribusi status penelitian</h3>
                    <p class="text-sm text-gray-600">Total, disetujui, ditolak, dan diajukan dalam satu grafik.</p>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <i class="fas fa-bolt text-amber-500"></i> Data diperbarui real-time
                </div>
            </div>
            <div class="mt-5 space-y-3">
                @foreach($summaryValues as $item)
                    <div class="rounded-2xl bg-white/80 ring-1 ring-gray-100 p-3 shadow-sm">
                        <div class="flex items-center justify-between text-sm font-semibold text-gray-800">
                            <div class="inline-flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $item['color'] }};"></span>
                                <span>{{ $item['label'] }}</span>
                            </div>
                            <span class="inline-flex items-center gap-1 text-xs font-semibold" style="color: {{ $item['color'] }}">
                                {{ $item['value'] }}
                            </span>
                        </div>
                        <div class="mt-2 w-full h-2.5 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full transition-all duration-300" style="width: {{ ($item['value'] / $maxSummary) * 100 }}%; background: linear-gradient(90deg, {{ $item['color'] }}, {{ $item['color'] }}90);"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Grafik Jumlah Penelitian per Bidang --}}
        <section class="rounded-3xl border border-orange-50 bg-gradient-to-br from-white via-orange-50/30 to-white shadow-sm p-6 sm:p-8">
            <div class="flex items-center justify-between gap-3">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-[11px] font-semibold text-orange-700 ring-1 ring-orange-100 shadow-sm">
                        <i class="fas fa-chart-bar text-[10px]"></i> Distribusi
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Jumlah Penelitian per Bidang</h3>
                    <p class="text-sm text-gray-600">Lihat konsentrasi riset per bidang utama.</p>
                </div>
                <div class="text-xs text-gray-500">Top 6 bidang</div>
            </div>
            @php($perFieldChart = ($perField ?? collect()))
            <div class="mt-4 space-y-3">
                @if($perFieldChart->isEmpty())
                    <div class="rounded-2xl border border-dashed border-orange-100 bg-orange-50/50 p-5 flex flex-col gap-3" id="fieldChartFallback">
                        <p class="text-sm font-semibold text-orange-800">Belum ada data bidang yang disetujui.</p>
                        <p class="text-xs text-orange-700">Tambahkan atau verifikasi penelitian untuk melihat grafik.</p>
                    </div>
                @else
                    <div class="rounded-2xl border border-orange-100 bg-gradient-to-br from-white via-orange-50/50 to-orange-100/40 p-4">
                        <canvas id="fieldBarChart" class="w-full h-[360px]"></canvas>
                    </div>
                    @once
                        <script>
                            (function() {
                                const labels = @json($perFieldChart->pluck('nama'));
                                const data = @json($perFieldChart->pluck('total')->map(fn($v) => (int)$v));

                                const render = () => {
                                    const el = document.getElementById('fieldBarChart');
                                    if (!el || el.dataset.chartRendered === '1' || !window.Chart) return false;
                                    if (el._chartInstance) {
                                        el._chartInstance.destroy();
                                    } else {
                                        const ctx = el.getContext('2d');
                                        if (ctx) ctx.clearRect(0, 0, el.width, el.height);
                                    }
                                    el._chartInstance = new Chart(el, {
                                        type: 'bar',
                                        data: { labels, datasets: [{ label: 'Jumlah Penelitian', data, backgroundColor: '#1d4ed8', borderRadius: 8, maxBarThickness: 64 }] },
                                        options: { responsive: true, maintainAspectRatio: false, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { stepSize: 1, precision: 0 } } } }
                                    });
                                    el.dataset.chartRendered = '1';
                                    return true;
                                };

                                const wait = (tries = 30) => {
                                    if (render()) return;
                                    if (tries <= 0) {
                                        console.error('Chart.js belum siap saat render grafik bidang.');
                                        return;
                                    }
                                    setTimeout(() => wait(tries - 1), 100);
                                };

                                const init = async () => {
                                    if (window.loadChart) {
                                        try { await window.loadChart(); } catch (e) { console.error('Gagal memuat Chart.js', e); }
                                    }
                                    wait();
                                };

                                if (document.readyState === 'complete' || document.readyState === 'interactive') {
                                    init();
                                } else {
                                    document.addEventListener('DOMContentLoaded', init);
                                }
                            })();
                        </script>
                    @endonce
                @endif
            </div>
        </section>

        {{-- Grafik Tren Per Tahun --}}
        <section class="rounded-3xl border border-cyan-50 bg-gradient-to-br from-white via-cyan-50/30 to-white shadow-sm p-6 sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-[11px] font-semibold text-cyan-700 ring-1 ring-cyan-100 shadow-sm">
                        <i class="fas fa-chart-line text-[10px]"></i> Tren
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Jumlah Penelitian per Tahun</h3>
                    <p class="text-sm text-gray-600">Garis waktu jumlah pengajuan per tahun.</p>
                </div>
                <div class="text-xs text-gray-500">Sumbu Y diskala otomatis</div>
            </div>
            @php($perYearData = ($perYear ?? collect()))
            <div class="mt-4 space-y-3">
                @if($perYearData->isEmpty())
                    <div class="rounded-2xl border border-dashed border-cyan-100 bg-cyan-50/40 p-5 text-sm text-cyan-700">
                        Belum ada data tahun untuk ditampilkan.
                    </div>
                @else
                    <div class="rounded-2xl border border-cyan-100 bg-gradient-to-br from-white via-cyan-50/40 to-white p-4">
                        <canvas id="yearLineChart" class="w-full h-[360px]"></canvas>
                    </div>
                    @once
                        <script>
                            (() => {
                                const labels = @json($perYearData->pluck('tahun'));
                                const data = @json($perYearData->pluck('total')->map(fn($v) => (int)$v));
                                const maxY = Math.max(...data, 0);

                                const render = () => {
                                    const el = document.getElementById('yearLineChart');
                                    if (!el || el.dataset.chartRendered === '1' || !window.Chart) return false;
                                    if (el._chartInstance) {
                                        el._chartInstance.destroy();
                                    } else {
                                        const ctx = el.getContext('2d');
                                        if (ctx) ctx.clearRect(0, 0, el.width, el.height);
                                    }
                                    el._chartInstance = new Chart(el, {
                                        type: 'line',
                                        data: {
                                            labels,
                                            datasets: [{
                                                label: 'Jumlah Penelitian',
                                                data,
                                                borderColor: '#0ea5e9',
                                                backgroundColor: '#cdeffd',
                                                fill: {
                                                    target: 'origin',
                                                    above: '#cdeffd',
                                                    below: '#cdeffd'
                                                },
                                                cubicInterpolationMode: 'monotone',
                                                tension: 0.35,
                                                borderWidth: 3,
                                                pointRadius: 4,
                                                pointBackgroundColor: '#0ea5e9',
                                                pointBorderWidth: 0
                                            }]
                                        },
                                        options: {
                                            animation: false,
                                            responsiveAnimationDuration: 0,
                                            animations: {
                                                tension: { duration: 0 }
                                            },
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: { legend: { display: true, position: 'top' } },
                                            scales: {
                                                x: { grid: { display: true, color: '#e5e7eb' } },
                                                y: {
                                                    beginAtZero: true,
                                                    suggestedMax: maxY + 0.3,
                                                    grid: { display: true, color: '#e5e7eb' },
                                                    ticks: { stepSize: 1, precision: 0 }
                                                }
                                            }
                                        }
                                    });
                                    el.dataset.chartRendered = '1';
                                    return true;
                                };

                                const wait = (tries = 30) => {
                                    if (render()) return;
                                    if (tries <= 0) {
                                        console.error('Chart.js belum siap saat render grafik per tahun.');
                                        return;
                                    }
                                    setTimeout(() => wait(tries - 1), 100);
                                };

                                const init = async () => {
                                    if (window.loadChart) {
                                        try { await window.loadChart(); } catch (e) { console.error('Gagal memuat Chart.js', e); }
                                    }
                                    wait();
                                };

                                if (document.readyState === 'complete' || document.readyState === 'interactive') {
                                    init();
                                } else {
                                    document.addEventListener('DOMContentLoaded', () => init());
                                }
                            })();
                        </script>
                    @endonce
                @endif
            </div>
        </section>

{{-- Monitoring tabel dihapus sesuai permintaan --}}
</div>
</x-app-layout>

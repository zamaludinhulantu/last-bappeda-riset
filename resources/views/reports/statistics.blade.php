<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Statistik Penelitian') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-lg font-semibold mb-4">Jumlah Penelitian per Bidang</h3>
                <canvas id="chartField" height="100"></canvas>

                <h3 class="text-lg font-semibold mt-10 mb-4">Jumlah Penelitian per Tahun</h3>
                <canvas id="chartYear" height="100"></canvas>
            </div>
        </div>
    </div>

    <script>
        (async () => {
            if (window.loadChart) {
                try { await window.loadChart(); } catch (e) { console.error('Gagal memuat Chart.js', e); return; }
            }

            // Data untuk chart bidang
            const fieldLabels = @json($perField->pluck('nama'));
            const fieldData = @json($perField->pluck('researches_count'));

            const ctx1 = document.getElementById('chartField');
            if (ctx1) {
                new Chart(ctx1, {
                    type: 'bar',
                    data: {
                        labels: fieldLabels,
                        datasets: [{
                            label: 'Jumlah Penelitian',
                            data: fieldData,
                            borderWidth: 1,
                            backgroundColor: '#3b82f6'
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }

            // Data untuk chart tahun
            const yearLabels = @json($perYear->pluck('tahun'));
            const yearData = @json($perYear->pluck('total'));

            const ctx2 = document.getElementById('chartYear');
            if (ctx2) {
                new Chart(ctx2, {
                    type: 'line',
                    data: {
                        labels: yearLabels,
                        datasets: [{
                            label: 'Jumlah Penelitian per Tahun',
                            data: yearData,
                            borderColor: '#10b981',
                            tension: 0.3,
                            fill: true,
                            backgroundColor: 'rgba(16,185,129,0.2)',
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }
        })();
    </script>
</x-app-layout>

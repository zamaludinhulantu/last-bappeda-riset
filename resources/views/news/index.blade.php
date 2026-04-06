<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Berita & Dokumentasi</p>
                <h2 class="text-2xl font-semibold text-gray-900">Kegiatan BAPPPEDA</h2>
                <p class="text-sm text-gray-500">Publikasikan seminar, kunjungan, atau dokumentasi untuk tampil di halaman depan.</p>
            </div>
            <a href="{{ route('news.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
                <i class="fas fa-plus text-xs"></i> Tambah Berita
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="rounded-2xl border border-orange-100 bg-white/90 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Daftar</p>
                    <h3 class="text-lg font-semibold text-gray-900">Berita terbaru</h3>
                </div>
                <span class="text-xs text-gray-500">{{ $items->total() }} entri</span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($items as $news)
                    <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="min-w-0 space-y-1">
                            <p class="text-sm font-semibold text-gray-900 line-clamp-1">{{ $news->judul }}</p>
                            <p class="text-xs text-gray-500 line-clamp-2">{{ $news->cuplikan ?? $news->ringkasan ?? 'Ringkasan belum diisi' }}</p>
                            <p class="text-[11px] text-gray-400">
                                Dipublikasikan {{ $news->dipublikasikan_pada ? $news->dipublikasikan_pada->translatedFormat('d M Y') : 'belum dijadwalkan' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('news.edit', $news) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-white">
                                <i class="fas fa-pen text-[10px]"></i> Ubah
                            </a>
                            <form action="{{ route('news.destroy', $news) }}" method="POST" onsubmit="return confirm('Hapus berita ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50">
                                    <i class="fas fa-trash text-[10px]"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center text-sm text-gray-600">
                        Belum ada berita. Tambahkan dokumentasi pertama Anda.
                    </div>
                @endforelse
            </div>
            <div class="px-6 py-4">
                {{ $items->links() }}
            </div>
        </section>
    </div>
</x-app-layout>


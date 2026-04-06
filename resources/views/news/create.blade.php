<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Berita</p>
                <h2 class="text-2xl font-semibold text-gray-900">{{ __('Tambah Dokumentasi Kegiatan') }}</h2>
                <p class="text-sm text-gray-500">Isi ringkasan dan detail agar muncul di halaman depan.</p>
            </div>
            <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-white">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-5xl">
        <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm p-6">
            @include('news.form')
        </section>
    </div>
</x-app-layout>


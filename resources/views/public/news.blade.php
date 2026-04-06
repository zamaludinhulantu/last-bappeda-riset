@php use Illuminate\Support\Facades\Storage; @endphp
@extends('layouts.public')

@section('title', 'Berita & Dokumentasi | '.config('app.name', 'SIPRISDA'))

@section('content')
    <section class="rounded-3xl border border-[#cde3ff] bg-white shadow-sm overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-6 border-b border-gray-100 bg-gradient-to-r from-[#e7f5ff] via-white to-[#eaf9f3]">
            <div class="space-y-1">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Berita & Dokumentasi</p>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Kegiatan terbaru dari Bapppeda</h1>
                <p class="text-sm text-gray-600">Artikel yang dipublikasikan admin Bapppeda akan tampil di sini.</p>
                @if($items->count())
                    <div class="inline-flex items-center gap-2 text-xs font-semibold text-[#0f3d73]">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span> {{ $items->total() ?? $items->count() }} berita
                    </div>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-white">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali
                </a>
              
            </div>
        </div>

        @if($items->count())
            <div class="px-6 pb-6 space-y-4 mt-6">
                @foreach($items as $news)
                    @php $cover = $news->gambar_sampul ?? $news->berkas_sampul; @endphp
                    <article class="rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-md transition grid md:grid-cols-3 gap-0 bg-white">
                        <div class="md:col-span-1">
                            @if($cover)
                                <a href="{{ route('public.news.show', $news->slug) }}">
                                    <img src="{{ Storage::disk('public')->url($cover) }}" alt="{{ $news->judul }}" class="h-full w-full object-cover">
                                </a>
                            @else
                                <div class="h-full min-h-[220px] w-full bg-gradient-to-r from-[#e7f5ff] via-white to-[#eaf9f3] flex items-center justify-center text-xs text-gray-500">Dokumentasi</div>
                            @endif
                        </div>
                        <div class="md:col-span-2 p-5 space-y-3">
                            <div class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.2em] text-[#0f3d73]">
                                <span class="h-2 w-2 rounded-full bg-emerald-400"></span> Sorotan
                                @if($news->dipublikasikan_pada)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-[#e7f5ff] px-3 py-1 text-[11px] font-semibold text-[#0f3d73]">{{ $news->dipublikasikan_pada->translatedFormat('d M Y') }}</span>
                                @endif
                            </div>
                            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 leading-snug">
                                <a href="{{ route('public.news.show', $news->slug) }}" class="hover:underline">{{ $news->judul }}</a>
                            </h2>
                            <p class="text-sm sm:text-base text-gray-700 line-clamp-4">{{ $news->cuplikan ?? $news->ringkasan ?? \Illuminate\Support\Str::limit(strip_tags($news->isi), 220) }}</p>
                            <a href="{{ route('public.news.show', $news->slug) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#0f3d73] hover:underline">
                                Baca selengkapnya <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="px-6 pb-6">
                {{ $items->links() }}
            </div>
        @else
            <div class="py-10 text-center text-sm text-gray-600">Belum ada berita yang dipublikasikan.</div>
        @endif
    </section>
@endsection

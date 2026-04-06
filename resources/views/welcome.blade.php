@extends('layouts.public')

@section('title', config('app.name', 'Aplikasi').' | Katalog Penelitian')

@php use Illuminate\Support\Str; use Illuminate\Support\Facades\Storage; @endphp
@php
    $researchCollection = isset($researches) ? collect($researches->items()) : collect();
    $totalResearchCount = isset($researches)
        ? (method_exists($researches, 'total') ? $researches->total() : $researchCollection->count())
        : 0;
    $totalInstitutionCount = $researchCollection->pluck('institusi_id')->filter()->unique()->count();
    $fieldCollection = isset($fields) ? collect($fields) : collect();
    $totalFieldCount = $fieldCollection->count();
    $hasFields = $totalFieldCount > 0;
    $heroHighlights = collect([
        ['number' => 1, 'text' => 'Hanya riset yang disetujui BAPPPEDA & Kesbangpol'],
        ['number' => 2, 'text' => 'Filter tema, bidang, tahun, dan institusi'],
        

    ]);
    $newsCollection = isset($newsItems) ? collect($newsItems) : collect();
    $newsHeroSlides = $newsCollection
        ->take(3)
        ->map(function($item){
            $cover = $item->gambar_sampul ?? $item->berkas_sampul;
            return [
                'img' => $cover ? Storage::disk('public')->url($cover) : asset('img/provgo.jpg'),
                'caption' => $item->judul,
                'date' => $item->dipublikasikan_pada ? $item->dipublikasikan_pada->translatedFormat('d M Y') : null,
                'link' => route('public.news.show', $item->slug),
            ];
        });
    $defaultHeroSlides = collect([
        ['img' => asset('img/provgo.jpg'), 'caption' => 'Kunjungan lapangan & monitoring riset daerah'],
        ['img' => asset('img/logo.png'), 'caption' => 'Kolaborasi BAPPPEDA dengan perguruan tinggi'],
        ['img' => asset('img/logo-original.png'), 'caption' => 'Data riset terintegrasi untuk publik'],
    ]);
    $heroSlides = $newsHeroSlides->count() ? $newsHeroSlides : $defaultHeroSlides;
@endphp

@section('hero')
    <div class="space-y-6">
        <div class="rounded-3xl border border-[#cde3ff] bg-white/90 shadow-lg shadow-[#cde3ff]/40 overflow-hidden">
            <div class="relative" x-data='{
                heroSlides: @json($heroSlides->values()),
                idx: 0,
                play() {
                    if (!this.heroSlides.length) return;
                    setInterval(() => { this.idx = (this.idx + 1) % this.heroSlides.length }, 3800);
                }
            }' x-init="play()">
                <div class="aspect-[16/6] bg-gradient-to-r from-[#e7f5ff] via-white to-[#eaf9f3] relative">
                    <template x-for="(slide, i) in heroSlides" :key="i">
                        <img :src="slide.img" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition-opacity duration-700" :class="i === idx ? 'opacity-100' : 'opacity-0'" alt="">
                    </template>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/25 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 px-6 sm:px-10 lg:px-14 pb-6 sm:pb-8" x-show="heroSlides.length" x-transition>
                        <div class="max-w-3xl space-y-2 text-white">
                            <div class="flex items-center gap-2">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                <span class="text-[11px] font-semibold uppercase tracking-[0.3em] bg-white/15 px-3 py-1 rounded-full">Berita Terbaru</span>
                                <span class="text-xs bg-white/10 px-2 py-1 rounded-full" x-text="heroSlides[idx]?.date || ''"></span>
                            </div>
                            <a :href="heroSlides[idx]?.link || '#'" class="block">
                                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold leading-tight hover:underline decoration-white/80" x-text="heroSlides[idx]?.caption || 'Lihat berita'"></h2>
                                <p class="mt-1 text-sm sm:text-base text-white/80">Klik untuk buka detail berita.</p>
                            </a>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <template x-for="(slide, i) in heroSlides" :key="i">
                                <button type="button" class="h-2.5 w-10 rounded-full transition"
                                        :class="i === idx ? 'bg-white' : 'bg-white/40'" @click="idx = i"></button>
                            </template>
                        </div>
                    </div>
                    <div class="absolute right-4 bottom-4 flex gap-2">
                        <template x-for="(slide, i) in heroSlides" :key="i">
                            <button type="button" class="h-2 w-8 rounded-full bg-white/60 hover:bg-white transition" :class="i === idx ? 'bg-white' : 'opacity-60'" @click="idx = i"></button>
                        </template>
                    </div>
                </div>
                <div class="p-6 lg:p-8">
                    <div class="space-y-2 mb-4">
                        <p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-[#0f3d73]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#0f3d73]"></span>
                            Portal Penelitian Terbuka
                        </p>
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold leading-tight text-gray-900">Eksplorasi Riset Terbaru di {{ config('app.name', 'Aplikasi') }}</h1>
                        <p class="text-base sm:text-lg text-gray-600" x-text="heroSlides[idx]?.caption"></p>
                    </div>
                    <div class="rounded-2xl border border-[#cde3ff] bg-white/90 shadow-sm p-5 space-y-4">
                        <form method="GET" action="{{ url('/').'#hasil-pencarian' }}" class="space-y-3">
                            <div class="flex items-center gap-3 rounded-2xl border border-[#cde3ff] bg-white px-4 py-3 shadow-sm focus-within:ring-2 focus-within:ring-[#1d5fbf]">
                                <i class="fas fa-magnifying-glass text-[#1d5fbf] text-lg"></i>
                                <input id="q" type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul, penulis, atau kata kunci penelitian..." class="w-full border-0 focus:ring-0 text-lg placeholder:text-gray-400">
                                @if(request()->hasAny(['q','bidang_id','tahun','institusi']))
                                    <a href="{{ url('/') }}" class="text-xs font-semibold text-gray-500 hover:text-gray-800">Reset</a>
                                @endif
                            </div>
                            <div class="grid sm:grid-cols-3 gap-3 text-sm">
                                <div class="flex items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2">
                                    <i class="fas fa-filter text-gray-500 text-xs"></i>
                                    <select id="bidang_id" name="bidang_id" class="w-full border-0 bg-transparent focus:ring-0">
                                        <option value="">Semua bidang</option>
                                        @foreach($fieldCollection as $f)
                                            <option value="{{ $f->id }}" @selected(request('bidang_id') == $f->id)>{{ $f->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2">
                                    <i class="fas fa-calendar text-gray-500 text-xs"></i>
                                    <input id="tahun" type="number" name="tahun" value="{{ request('tahun') }}" min="2000" max="{{ date('Y') }}" class="w-full border-0 bg-transparent focus:ring-0" placeholder="Tahun">
                                </div>
                                <div class="flex items-center gap-2 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2">
                                    <i class="fas fa-building text-gray-500 text-xs"></i>
                                    <input id="institusi" type="text" name="institusi" value="{{ request('institusi') }}" placeholder="Institusi" class="w-full border-0 bg-transparent focus:ring-0">
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white hover:bg-gray-800">
                                    Telusuri Sekarang
                                </button>
                                <a href="#katalog" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-900 hover:bg-white">
                                    <i class="fas fa-layer-group text-xs"></i> Lihat daftar cepat
                                </a>
                            </div>
                        </form>
                        <ul class="grid sm:grid-cols-2 gap-4 text-sm text-gray-700">
                            @foreach($heroHighlights as $highlight)
                                <li class="flex items-start gap-3">
                                    <span class="mt-1 h-5 w-5 rounded-full bg-[#e7f5ff] text-[#0f3d73] flex items-center justify-center text-xs font-semibold">{{ $highlight['number'] }}</span>
                                    {{ $highlight['text'] }}
                                </li>
                            @endforeach
                        </ul>
                        <div class="flex flex-wrap gap-3">
                            <a href="#katalog" class="inline-flex items-center rounded-lg bg-[#0f3d73] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#174f9e]">Lihat Katalog Publik</a>
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-200 hover:bg-gray-50">Buka Dashboard</a>
                                @else
                                    <div class="flex flex-wrap gap-3 md:hidden">
                                        <a href="{{ route('login') }}" class="inline-flex items-center rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-200 hover:bg-gray-50">Masuk</a>
                                        @if(Route::has('register'))
                                            <a href="{{ route('register') }}" class="inline-flex items-center rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800">Daftar</a>
                                        @endif
                                    </div>
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
                @if(request()->hasAny(['q','bidang_id','tahun','institusi']))
                    <div id="hasil-pencarian"></div>
                    @if($researchCollection->count())
                        <div class="mt-6 rounded-2xl border border-gray-100 bg-white/90 p-5 shadow-sm">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Hasil Pencarian</p>
                                    <h3 class="text-lg font-semibold text-gray-900">Judul yang ditemukan</h3>
                                    <p class="text-sm text-gray-500">Menampilkan maksimal 6 hasil. Untuk daftar lengkap buka menu Pengumuman.</p>
                                </div>
                                <a href="{{ route('public.announcements') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#0f3d73] hover:underline">
                                    Lihat semua <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                            <div class="mt-4 grid gap-4 md:grid-cols-2">
                                @foreach($researchCollection->take(6) as $item)
                                    <article class="rounded-xl border border-gray-100 bg-gray-50 p-4 shadow-sm">
                                        <p class="text-xs uppercase font-semibold text-orange-600">#{{ $loop->iteration }}</p>
                                        <h4 class="mt-1 text-base font-semibold text-gray-900 line-clamp-2">{{ $item->judul }}</h4>
                                        <p class="text-xs text-gray-600 mt-1 line-clamp-1">{{ $item->penulis }}</p>
                                        <div class="mt-2 text-[11px] text-gray-500 space-y-0.5">
                                            <p><i class="fas fa-tags text-[9px] mr-1.5"></i>{{ optional($item->field)->nama ?: 'Umum' }}</p>
                                            <p><i class="fas fa-building text-[9px] mr-1.5"></i>{{ optional($item->institution)->nama ?: '-' }}</p>
                                        </div>
                                        <div class="mt-3 inline-flex items-center gap-1 rounded-full bg-white px-3 py-1 text-[11px] font-semibold text-[#0f3d73] ring-1 ring-[#cde3ff]">
                                            <i class="fas fa-calendar text-[10px]"></i> {{ $item->tahun ?: 'TBA' }}
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                            @if($researches?->total() > 6)
                                <div class="mt-3 text-xs text-gray-500">Hanya menampilkan 6 entri pertama.</div>
                            @endif
                        </div>
                    @else
                        <div class="mt-6 rounded-2xl border border-[#cde3ff] bg-gradient-to-r from-[#e7f5ff] via-white to-[#eaf9f3] p-6 shadow-sm">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 h-10 w-10 rounded-full bg-white text-[#0f3d73] flex items-center justify-center border border-[#cde3ff] shadow-sm">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <div class="text-sm text-gray-700">
                                        <p class="font-semibold text-gray-900 text-base">Tidak ada hasil yang cocok</p>
                                        <p class="mt-1">Coba ubah kata kunci atau kosongkan filter bidang, tahun, dan institusi untuk menampilkan lebih banyak data.</p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2 text-sm">
                                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 rounded-lg border border-[#cde3ff] bg-white px-3 py-2 font-semibold text-[#0f3d73] hover:bg-[#f4f8ff]">
                                        <i class="fas fa-rotate-left text-xs"></i> Reset pencarian
                                    </a>
                                    <a href="{{ route('public.announcements') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#0f3d73] px-3 py-2 font-semibold text-white shadow-sm hover:bg-[#174f9e]">
                                        <i class="fas fa-list text-xs"></i> Buka Pengumuman
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection

@section('content')
    @if($highlightedResearches->count())
        @php
            $researchSlides = $highlightedResearches->take(9)->map(function($item){
                return [
                    'judul' => $item->judul,
                    'penulis' => $item->penulis,
                    'tahun' => $item->tahun,
                    'field' => optional($item->field)->nama ?: 'Umum',
                    'institution' => optional($item->institution)->nama ?: '-',
                ];
            })->chunk(3)->values();
        @endphp
        <section class="rounded-2xl border border-[#cde3ff] bg-white shadow-sm p-6 mb-6" x-data='{
            slides: @json($researchSlides),
            idx: 0,
            play() {
                if (!this.slides.length) return;
                setInterval(() => { this.idx = (this.idx + 1) % this.slides.length }, 4000);
            }
        }' x-init="play()">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="space-y-1">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Penelitian</p>
                    <h3 class="text-2xl font-semibold text-gray-900">Penelitian terbaru</h3>
                    <p class="text-sm text-gray-500">Bergerak otomatis, 3 kartu setiap slide.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" class="h-9 w-9 rounded-full border border-[#cde3ff] text-[#0f3d73] hover:bg-[#f4f8ff]" @click="idx = (idx - 1 + slides.length) % slides.length">
                        <i class="fas fa-arrow-left text-sm"></i>
                    </button>
                    <button type="button" class="h-9 w-9 rounded-full border border-[#cde3ff] text-[#0f3d73] hover:bg-[#f4f8ff]" @click="idx = (idx + 1) % slides.length">
                        <i class="fas fa-arrow-right text-sm"></i>
                    </button>
                    <a href="#katalog" class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-[#0f3d73] hover:underline">
                        Lihat katalog <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
            <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <template x-for="(slide, slideIndex) in slides" :key="slideIndex">
                    <template x-if="slideIndex === idx">
                        <template x-for="(item, i) in slide" :key="i">
                            <article class="rounded-xl border border-gray-100 bg-gray-50 p-4 shadow-sm hover:shadow transition">
                                <div class="flex items-center justify-between text-[11px] text-gray-500 uppercase font-semibold tracking-[0.12em]">
                                    <span class="inline-flex items-center gap-1"><i class="fas fa-calendar text-[10px]"></i><span x-text="item.tahun || 'TBA'"></span></span>
                                    <span class="inline-flex items-center gap-1 text-orange-600"><i class="fas fa-bolt text-[10px]"></i>Terbaru</span>
                                </div>
                                <h4 class="mt-2 text-lg font-semibold text-gray-900 line-clamp-2" x-text="item.judul"></h4>
                                <p class="text-sm text-gray-600 mt-1 line-clamp-1" x-text="item.penulis"></p>
                                <div class="mt-2 space-y-1 text-sm text-gray-600">
                                    <p class="flex items-center gap-2"><i class="fas fa-tags text-xs text-gray-500"></i><span x-text="item.field"></span></p>
                                    <p class="flex items-center gap-2"><i class="fas fa-building text-xs text-gray-500"></i><span x-text="item.institution"></span></p>
                                </div>
                            </article>
                        </template>
                    </template>
                </template>
            </div>
            <div class="mt-4 flex items-center gap-2">
                <template x-for="(slide, i) in slides" :key="i">
                    <button type="button" class="h-2 w-8 rounded-full transition" :class="i === idx ? 'bg-[#0f3d73]' : 'bg-gray-300'" @click="idx = i"></button>
                </template>
            </div>
        </section>
    @endif

    @if($newsCollection->count())
        <section class="rounded-2xl border border-[#cde3ff] bg-white shadow-sm p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="space-y-1">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Berita</p>
                    <h3 class="text-2xl font-semibold text-gray-900">Kegiatan terbaru BAPPPEDA</h3>
                    <p class="text-sm text-gray-500">Sorotan berita terkini dengan foto sampul.</p>
                </div>
                <a href="{{ route('public.news') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-[#0f3d73] hover:underline">
                    Lihat semua <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
            <div class="mt-4 space-y-4">
                @foreach($newsCollection as $news)
                    @php $cover = $news->gambar_sampul ?? $news->berkas_sampul; @endphp
                    <article class="flex gap-4 p-3 sm:p-4 rounded-xl border border-gray-100 hover:border-[#cde3ff] bg-white shadow-sm hover:shadow transition">
                        <div class="w-28 sm:w-32 h-20 sm:h-24 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                            @if($cover)
                                <a href="{{ route('public.news.show', $news->slug) }}">
                                    <img src="{{ Storage::disk('public')->url($cover) }}" alt="{{ $news->judul }}" class="w-full h-full object-cover">
                                </a>
                            @else
                                <div class="w-full h-full bg-gradient-to-r from-[#e7f5ff] via-white to-[#eaf9f3] flex items-center justify-center text-[11px] text-gray-500">Tanpa sampul</div>
                            @endif
                        </div>
                        <div class="min-w-0 space-y-1">
                            @if($news->dipublikasikan_pada)
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#0f3d73] uppercase tracking-[0.12em]">
                                    {{ $news->dipublikasikan_pada->translatedFormat('d M Y') }}
                                </span>
                            @endif
                            <h4 class="text-base sm:text-lg font-semibold text-gray-900 leading-snug line-clamp-2">
                                <a href="{{ route('public.news.show', $news->slug) }}" class="hover:underline">{{ $news->judul }}</a>
                            </h4>
                            <p class="text-sm text-gray-600 line-clamp-2 sm:line-clamp-3">{{ $news->cuplikan ?? $news->ringkasan ?? Str::limit(strip_tags($news->isi), 160) }}</p>
                            <div class="flex items-center gap-2 pt-1 text-xs text-gray-500">
                                <a href="{{ route('public.news.show', $news->slug) }}" class="inline-flex items-center gap-1 font-semibold text-[#0f3d73] hover:underline">
                                    Baca selengkapnya <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                                @if($loop->first)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 text-emerald-700 px-2 py-0.5 text-[11px] font-semibold">Terbaru</span>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Katalog publik tabel dihapus sesuai permintaan --}}
@endsection


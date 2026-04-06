@php use Illuminate\Support\Facades\Storage; @endphp
@extends('layouts.public')

@section('title', ($news->judul ?? 'Berita').' | '.config('app.name', 'SIPRISDA'))

@section('content')
    <article class="rounded-3xl border border-[#cde3ff] bg-white shadow-sm overflow-hidden">
        @php $cover = $news->gambar_sampul ?? $news->berkas_sampul; @endphp
        @if($cover)
            <img src="{{ Storage::disk('public')->url($cover) }}" alt="{{ $news->judul }}" class="w-full h-64 object-cover">
        @endif
        <div class="p-6 sm:p-8 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="space-y-1">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Berita & Dokumentasi</p>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $news->judul }}</h1>
                    <p class="text-sm text-gray-500">{{ $news->dipublikasikan_pada ? $news->dipublikasikan_pada->translatedFormat('d F Y') : 'Tanggal publikasi belum diatur' }}</p>
                </div>
                <a href="{{ route('public.news') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-white">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali
                </a>
            </div>
            @if($news->cuplikan ?? $news->ringkasan)
                <p class="text-base text-gray-600 leading-relaxed">{{ $news->cuplikan ?? $news->ringkasan }}</p>
            @endif
            @if($news->isi)
                <div class="prose max-w-none">
                    {!! nl2br(e($news->isi)) !!}
                </div>
            @endif
        </div>
    </article>
@endsection

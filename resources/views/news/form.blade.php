@php
    $isEdit = isset($news);
    $publishValue = $isEdit && $news->dipublikasikan_pada ? $news->dipublikasikan_pada->format('Y-m-d\TH:i') : '';
@endphp

<form action="{{ $isEdit ? route('news.update', $news) : route('news.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif
    <div class="grid gap-4 md:grid-cols-2">
        <div class="md:col-span-2">
            <label class="text-sm font-medium text-gray-700">Judul</label>
            <input type="text" name="judul" value="{{ old('judul', $news->judul ?? '') }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
            @error('judul')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="md:col-span-2">
            <label class="text-sm font-medium text-gray-700">Ringkasan Singkat</label>
            <textarea name="ringkasan" rows="2" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" placeholder="Paragraf pendek untuk tampil di landing">{{ old('ringkasan', $news->ringkasan ?? $news->cuplikan ?? '') }}</textarea>
            @error('ringkasan')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="md:col-span-2">
            <label class="text-sm font-medium text-gray-700">Isi Berita / Dokumentasi</label>
            <textarea name="isi" rows="6" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" placeholder="Tuliskan detail kegiatan, lokasi, narasumber, dan hasil">{{ old('isi', $news->isi ?? '') }}</textarea>
            @error('isi')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700">Tanggal Publikasi</label>
            <input type="datetime-local" name="dipublikasikan_pada" value="{{ old('dipublikasikan_pada', $publishValue) }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500">
            @error('dipublikasikan_pada')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700">Sampul (opsional)</label>
            <input type="file" name="cover" accept="image/*" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500">
            @error('cover')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            @if($isEdit && ($news->gambar_sampul ?? $news->berkas_sampul))
                <p class="text-xs text-gray-500 mt-1">Sampul saat ini: <a class="text-orange-600 hover:underline" href="{{ asset('storage/'.($news->gambar_sampul ?? $news->berkas_sampul)) }}" target="_blank" rel="noopener">lihat</a></p>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white hover:bg-gray-800">
            <i class="fas fa-save text-xs"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Berita' }}
        </button>
        <a href="{{ route('news.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Batal</a>
    </div>
</form>

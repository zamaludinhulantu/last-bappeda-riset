<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index()
    {
        $items = News::latest('dipublikasikan_pada')
            ->latest()
            ->paginate(12);

        return view('news.index', compact('items'));
    }

    public function create()
    {
        return view('news.create');
    }

    public function store(Request $request)
    {
        $this->validatedData($request);
        $payload = [
            'judul' => $request->judul,
            'slug' => $this->generateSlug($request->judul),
            'ringkasan' => $request->ringkasan,
            'cuplikan' => $request->ringkasan,
            'isi' => $request->filled('isi') ? $request->isi : '',
            'dipublikasikan_pada' => $request->filled('dipublikasikan_pada') ? $request->dipublikasikan_pada : now(),
            'status' => 'published',
            'penulis_id' => Auth::id(),
        ];

        $coverColumn = $this->coverColumn();
        if ($coverColumn && $request->hasFile('cover')) {
            $payload[$coverColumn] = $request->file('cover')->store('news', 'public');
        }

        News::create($payload);

        return redirect()->route('news.index')->with('success', 'Berita berhasil disimpan.');
    }

    public function edit(News $news)
    {
        return view('news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $this->validatedData($request, $news->id);
        $payload = [
            'judul' => $request->judul,
            'slug' => $this->generateSlug($request->judul, $news->id),
            'ringkasan' => $request->ringkasan,
            'cuplikan' => $request->ringkasan,
            'isi' => $request->filled('isi') ? $request->isi : '',
            'dipublikasikan_pada' => $request->filled('dipublikasikan_pada') ? $request->dipublikasikan_pada : $news->dipublikasikan_pada,
            'status' => $news->status ?? 'published',
        ];

        $coverColumn = $this->coverColumn();
        if ($coverColumn && $request->hasFile('cover')) {
            $payload[$coverColumn] = $request->file('cover')->store('news', 'public');
            $this->deleteIfExists($news->{$coverColumn});
        }

        $news->update($payload);

        return redirect()->route('news.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(News $news)
    {
        $this->deleteIfExists($news->berkas_sampul);
        $this->deleteIfExists($news->gambar_sampul);
        $news->delete();

        return redirect()->route('news.index')->with('success', 'Berita dihapus.');
    }

    protected function validatedData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'judul' => 'required|string|max:255',
            'ringkasan' => 'nullable|string|max:500',
            'isi' => 'nullable|string',
            'dipublikasikan_pada' => 'nullable|date',
            'cover' => 'nullable|image|max:2048',
        ]);
    }

    protected function generateSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'berita';
        $slug = $base;
        $counter = 1;

        while (
            News::where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }

    protected function coverColumn(): ?string
    {
        static $col = null;
        if ($col !== null) {
            return $col;
        }

        $col = Schema::hasColumn('berita', 'berkas_sampul')
            ? 'berkas_sampul'
            : (Schema::hasColumn('berita', 'gambar_sampul') ? 'gambar_sampul' : null);

        return $col;
    }

    protected function deleteIfExists(?string $path): void
    {
        if (!$path) {
            return;
        }

        $normalized = ltrim($path, '/');
        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, strlen('storage/'));
        }

        if (Storage::disk('public')->exists($normalized)) {
            Storage::disk('public')->delete($normalized);
        }
    }

}

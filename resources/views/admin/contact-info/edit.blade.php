<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Kontak Publik</p>
                <h2 class="text-2xl font-semibold text-gray-900">Hubungi Kami</h2>
                <p class="text-sm text-gray-500">Atur informasi kontak publik yang tampil di halaman depan.</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="rounded-2xl border border-orange-100 bg-white/95 shadow-sm p-6">
            <form action="{{ route('contact-info.update') }}" method="POST" class="space-y-5">
                @csrf
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="surel" value="{{ old('surel', $contact->value('surel')) }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" placeholder="publikasi@bapppeda.go.id">
                        @error('surel')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Telepon</label>
                        <input type="text" name="telepon" value="{{ old('telepon', $contact->value('telepon')) }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" placeholder="(0435) 123-456">
                        @error('telepon')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-700">Alamat</label>
                        <textarea name="alamat" rows="2" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" placeholder="Jl. Pembangunan No. 1, Kota Gorontalo">{{ old('alamat', $contact->value('alamat')) }}</textarea>
                        @error('alamat')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white hover:bg-gray-800">
                        <i class="fas fa-save text-xs"></i> Simpan
                    </button>
                    <p class="text-xs text-gray-500">Jika dikosongkan, sistem akan memakai teks bawaan.</p>
                </div>
            </form>
        </section>
    </div>
</x-app-layout>

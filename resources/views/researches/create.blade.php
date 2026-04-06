<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-orange-500">Formulir</p>
                <h2 class="text-2xl font-semibold text-gray-900">{{ __('Unggah Penelitian Baru') }}</h2>
                <p class="text-sm text-gray-500">Lengkapi informasi inti penelitian. Anda masih dapat memperbarui detail setelah disimpan.</p>
            </div>
            <a href="{{ route('researches.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-white">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-5xl">
        <section class="rounded-2xl border border-orange-100 bg-white/90 p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900">Langkah Pengisian</h3>
            <ol class="mt-4 grid gap-3 text-sm text-gray-600 sm:grid-cols-3">
                <li class="rounded-xl border border-gray-100 bg-white p-3">
                    <p class="font-semibold text-gray-900">1. Identitas</p>
                    <p>Judul, peneliti, kontak.</p>
                </li>
                <li class="rounded-xl border border-gray-100 bg-white p-3">
                    <p class="font-semibold text-gray-900">2. Rincian Riset</p>
                    <p>Bidang, instansi, lokasi, jadwal.</p>
                </li>
                <li class="rounded-xl border border-gray-100 bg-white p-3">
                    <p class="font-semibold text-gray-900">3. Dokumen Pendukung</p>
                    <p>Proposal PDF (opsional) sebelum disetujui.</p>
                </li>
            </ol>
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white/95 backdrop-blur shadow-sm p-6">
            <form action="{{ route('researches.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-700">Judul Penelitian</label>
                        <input type="text" name="judul" value="{{ old('judul') }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                        @error('judul')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-700">Peneliti (bisa lebih dari satu)</label>
                            <button type="button" class="text-xs font-semibold text-orange-600 hover:text-orange-700" data-author-add>+ Tambah peneliti</button>
                        </div>
                        <input type="hidden" name="penulis" value="{{ old('penulis') }}" data-authors-hidden>
                        <div class="space-y-2" data-authors-wrapper>
                            @php
                                $authorsOld = collect(preg_split('/[;,]+/', old('penulis', '')))->map(fn($v) => trim($v))->filter()->values();
                                if ($authorsOld->isEmpty()) {
                                    $authorsOld = collect(['']);
                                }
                            @endphp
                            @foreach($authorsOld as $idx => $authorVal)
                                <div class="flex items-center gap-2" data-author-row>
                                    <input type="text" name="daftar_penulis[]" value="{{ $authorVal }}" class="flex-1 rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" data-author-input placeholder="Nama peneliti">
                                    <button type="button" class="text-xs text-gray-500 hover:text-rose-600" data-author-remove title="Hapus peneliti">&#10005;</button>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-[11px] text-gray-500">Nama akan digabung otomatis. Isi minimal satu peneliti.</p>
                        @error('penulis')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">NIK Peneliti</label>
                        <input type="text" name="nik_peneliti" value="{{ old('nik_peneliti') }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                        @error('nik_peneliti')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Nomor Telepon</label>
                        <input type="text" name="telepon_peneliti" value="{{ old('telepon_peneliti') }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                        @error('telepon_peneliti')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Bidang Penelitian</label>
                        <select name="bidang_id" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" data-field-select>
                            <option value="">Pilih bidang</option>
                            @foreach ($fields as $field)
                                <option value="{{ $field->id }}" @selected(old('bidang_id') == $field->id)>{{ $field->nama }}</option>
                            @endforeach
                            <option value="__other" @selected(old('bidang_lain'))>Lainnya (isi manual)</option>
                        </select>
                        @error('bidang_id')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                        <div class="mt-3 space-y-1 hidden" data-field-other-wrapper>
                            <label class="text-xs font-semibold text-gray-700">Bidang lain (isi jika tidak ada di daftar)</label>
                            <input type="text" name="bidang_lain" value="{{ old('bidang_lain') }}" placeholder="Contoh: Bioteknologi Kelautan" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 text-sm" data-field-other>
                            @error('bidang_lain')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                            <p class="text-[11px] text-gray-500">Isi salah satu: pilih daftar atau tulis bidang lain.</p>
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Asal (Universitas/Instansi/Lembaga)</label>
                        <input type="text" name="nama_institusi" value="{{ old('nama_institusi') }}" placeholder="Nama asal instansi lengkap" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                        @error('nama_institusi')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Lokasi Penelitian</label>
                        <input type="text" name="lokasi" value="{{ old('lokasi') }}" placeholder="Contoh: Kota/Kabupaten atau desa/kecamatan" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                        @error('lokasi')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Tahun</label>
                        <input type="number" name="tahun" value="{{ old('tahun') }}" min="2000" max="{{ date('Y') }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                        @error('tahun')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-700">Abstrak Proposal</label>
                        <textarea name="abstrak" rows="4" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" placeholder="Ringkasan singkat sebelum mengunggah file lengkap">{{ old('abstrak') }}</textarea>
                        @error('abstrak')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:col-span-2">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                            @error('tanggal_mulai')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" required>
                            @error('tanggal_selesai')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="grid gap-3 rounded-xl border border-[#dfeafe] bg-white/90 p-4 shadow-sm">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-2 text-[#0f3d73]">
                                <i class="fas fa-file-signature text-[13px]"></i>
                                <label class="text-sm font-semibold">Surat Pengantar Universitas/Instansi/Lembaga (unggahan awal)</label>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <input type="file" name="campus_letter" accept=".pdf,.jpg,.jpeg,.png" class="w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500 text-sm">
                            <p class="text-[12px] text-gray-600 leading-relaxed">Lampirkan surat pengantar/permohonan dari universitas/instansi/lembaga saat pengajuan awal. Format PDF/JPG/PNG, maks 20 MB.</p>
                            @error('campus_letter')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">Proposal Penelitian (opsional)</label>
                        <input type="file" name="pdf_file" accept="application/pdf" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500">
                        <p class="text-xs text-gray-500 mt-1">Format PDF, maksimum 20 MB.</p>
                        @error('pdf_file')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    @if(auth()->user()?->hasAdminAccess())
                        <div>
                            <label class="text-sm font-medium text-gray-700">Hasil Penelitian (opsional, admin)</label>
                            <input type="file" name="results_file" accept="application/pdf" class="mt-1 w-full rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500">
                            <p class="text-xs text-gray-500 mt-1">Khusus admin Bappeda: unggah hasil langsung bila sudah ada. PDF maks 20 MB.</p>
                            @error('results_file')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid gap-3 rounded-xl border border-cyan-100 bg-white/70 p-4">
                            <p class="text-sm font-semibold text-cyan-700 flex items-center gap-2"><i class="fas fa-file-shield text-[11px]"></i> Surat Rekomendasi Kesbangpol (opsional, admin)</p>
                            <input type="file" name="kesbang_letter" accept=".pdf,.jpg,.jpeg,.png" class="w-full rounded-lg border border-cyan-200 text-sm focus:border-cyan-500 focus:ring-cyan-500">
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-gray-700">Nomor Surat</label>
                                    <input type="text" name="nomor_surat_kesbang" value="{{ old('nomor_surat_kesbang') }}" class="w-full rounded-lg border-gray-200 text-sm focus:border-cyan-500 focus:ring-cyan-500" placeholder="Contoh: 070/Kesbangpol/IX/2025">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-gray-700">Tanggal Surat</label>
                                    <input type="date" name="tanggal_surat_kesbang" value="{{ old('tanggal_surat_kesbang') }}" class="w-full rounded-lg border-gray-200 text-sm focus:border-cyan-500 focus:ring-cyan-500">
                                </div>
                            </div>
                            <p class="text-[11px] text-gray-500">Jika diunggah, status langsung menjadi diverifikasi Kesbang.</p>
                            @error('kesbang_letter')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                            @error('nomor_surat_kesbang')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                            @error('tanggal_surat_kesbang')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white hover:bg-gray-800">
                        <i class="fas fa-cloud-upload-alt text-xs"></i> Simpan & Kirim
                    </button>
                    <p class="text-xs text-gray-500">Unggah berkas final melalui menu hasil setelah penelitian selesai.</p>
                </div>
            </form>
        </section>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const select = document.querySelector('[data-field-select]');
            const other = document.querySelector('[data-field-other]');
            const wrapper = document.querySelector('[data-field-other-wrapper]');
            if (!select || !other) return;

            const sync = () => {
                const isOther = select.value === '__other';
                if (isOther) {
                    wrapper?.classList.remove('hidden');
                    select.value = '';
                    other.required = true;
                    other.focus();
                } else {
                    wrapper?.classList.add('hidden');
                    other.required = false;
                    if (!other.value) {
                        other.value = '';
                    }
                }
            };

            if (other.value) {
                select.value = '__other';
            }
            select.addEventListener('change', sync);
            sync();

            // Peneliti jamak
            const authorsWrapper = document.querySelector('[data-authors-wrapper]');
            const authorsHidden = document.querySelector('[data-authors-hidden]');
            const addBtn = document.querySelector('[data-author-add]');

            const syncAuthors = () => {
                if (!authorsWrapper || !authorsHidden) return;
                const inputs = authorsWrapper.querySelectorAll('[data-author-input]');
                const names = Array.from(inputs).map(inp => inp.value.trim()).filter(Boolean);
                authorsHidden.value = names.join('; ');
            };

            const ensureAtLeastOne = () => {
                if (!authorsWrapper) return;
                const rows = authorsWrapper.querySelectorAll('[data-author-row]');
                if (rows.length === 0) {
                    addRow('');
                }
            };

            const addRow = (value = '') => {
                if (!authorsWrapper) return;
                const row = document.createElement('div');
                row.className = 'flex items-center gap-2';
                row.setAttribute('data-author-row', '');
                row.innerHTML = `
                    <input type="text" name="daftar_penulis[]" value="${value.replace(/"/g, '&quot;')}" class="flex-1 rounded-lg border-gray-200 focus:border-orange-500 focus:ring-orange-500" data-author-input placeholder="Nama peneliti">
                    <button type="button" class="text-xs text-gray-500 hover:text-rose-600" data-author-remove title="Hapus peneliti">&#10005;</button>
                `;
                authorsWrapper.appendChild(row);
                row.querySelector('[data-author-input]')?.addEventListener('input', syncAuthors);
                row.querySelector('[data-author-remove]')?.addEventListener('click', () => {
                    row.remove();
                    ensureAtLeastOne();
                    syncAuthors();
                });
            };

            addBtn?.addEventListener('click', () => {
                addRow('');
            });

            authorsWrapper?.querySelectorAll('[data-author-input]')?.forEach((input) => {
                input.addEventListener('input', syncAuthors);
            });
            authorsWrapper?.querySelectorAll('[data-author-remove]')?.forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    const row = e.currentTarget.closest('[data-author-row]');
                    row?.remove();
                    ensureAtLeastOne();
                    syncAuthors();
                });
            });

            ensureAtLeastOne();
            syncAuthors();
        });
    </script>
</x-app-layout>


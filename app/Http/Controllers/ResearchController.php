<?php

namespace App\Http\Controllers;

use App\Mail\ResearchResultReminder;
use App\Mail\ResearchRecommendationReady;
use App\Models\Research;
use App\Models\Field;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Exceptions\HttpResponseException;
use Barryvdh\DomPDF\Facade\Pdf;

class ResearchController extends Controller
{
    /**
     * Menampilkan daftar penelitian
     */
    public function index()
    {
        $query = Research::select([
                'id',
                'judul',
                'penulis',
                'institusi_id',
                'bidang_id',
                'status',
                'tahun',
                'tanggal_mulai',
                'tanggal_selesai',
                'dibuat_pada',
                'diajukan_pada',
                'nik_peneliti',
                'telepon_peneliti',
                'berkas_surat_kesbang',
            ])
            ->with(['institution:id,nama', 'field:id,nama'])
            ->latest();

        if (auth()->check()) {
            $user = auth()->user();
            if (!$user->hasRole(['admin', 'kesbangpol', 'superadmin'])) {
                $query->where('pengunggah_id', $user->id);
            } elseif ($user->hasRole('admin') && !$user->hasRole(['superadmin', 'kesbangpol'])) {
                // Admin BAPPEDA melihat yang sudah diverifikasi + unggahan mereka sendiri meski belum diverifikasi
                $query->where(function ($builder) use ($user) {
                    $builder->whereNotNull('diverifikasi_kesbang_pada')
                        ->orWhere('pengunggah_id', $user->id);
                });
            }
        }

        $researches = $query->paginate(15);
        return view('researches.index', compact('researches'));
    }

    /**
     * Menampilkan detail penelitian
     */
    public function show(Research $research)
    {
        if (!auth()->check()) {
            abort(403, 'Anda tidak berhak mengakses penelitian ini.');
        }

        $user = auth()->user();
        if (!$user->hasRole(['admin', 'kesbangpol', 'superadmin']) && (int) $research->pengunggah_id !== (int) $user->id) {
            abort(403, 'Anda tidak berhak mengakses penelitian ini.');
        }

        // Admin BAPPPEDA tidak boleh melihat jika belum diverifikasi Kesbang
        if ($user->hasRole('admin') && !$user->hasRole(['superadmin', 'kesbangpol']) && is_null($research->diverifikasi_kesbang_pada)) {
            abort(404);
        }

        $research->loadMissing([
            'rejectedBy',
            'submittedBy',
            'kesbangVerifier',
            'approver',
        ]);
        return view('researches.show', compact('research'));
    }

    /**
     * Form edit penelitian
     */
    public function edit(Research $research)
    {
        $this->ensureEditableByOwner($research);
        $fields = Field::all();
        return view('researches.edit', compact('research', 'fields'));
    }

    /**
     * Update penelitian
     */
    public function update(Request $request, Research $research)
    {
        $this->ensureEditableByOwner($research);
        $isAdmin = $request->user()?->hasAdminAccess();
        $campusRule = $isAdmin
            ? 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480'
            : ($research->berkas_surat_kampus ? 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480' : 'required|file|mimes:pdf,jpg,jpeg,png|max:20480');

        // Normalisasi peneliti menjadi string tunggal (pisah dengan "; ")
        $request->merge(['penulis' => $this->normalizeAuthors($request)]);

        $request->validate([
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'nik_peneliti' => 'required|string|min:8|max:32',
            'telepon_peneliti' => 'required|string|max:32',
            'bidang_id' => 'nullable|required_without:bidang_lain|exists:bidang,id',
            'bidang_lain' => 'nullable|required_without:bidang_id|string|max:255',
            'nama_institusi' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'tahun' => 'required|digits:4|integer|min:2000|max:' . date('Y'),
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'pdf_file' => 'nullable|mimes:pdf|max:20480',
            'campus_letter' => $campusRule,
            'results_file' => $isAdmin ? 'nullable|mimes:pdf|max:20480' : 'nullable',
            'abstrak' => 'nullable|string',
            'kata_kunci' => 'nullable|string',
            'kesbang_letter' => $isAdmin ? 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480' : 'nullable',
            'nomor_surat_kesbang' => $isAdmin ? 'nullable|string|max:255' : 'nullable',
            'tanggal_surat_kesbang' => $isAdmin ? 'nullable|date' : 'nullable',
        ], [
            'pdf_file.max' => 'Ukuran berkas proposal maksimal 20 MB.',
            'results_file.max' => 'Ukuran berkas hasil maksimal 20 MB.',
            'kesbang_letter.max' => 'Ukuran surat rekomendasi maksimal 20 MB.',
            'campus_letter.max' => 'Ukuran surat kampus maksimal 20 MB.',
        ]);

        $institution = Institution::firstOrCreate(['nama' => trim($request->nama_institusi)]);

        $field = null;
        if ($request->filled('bidang_lain')) {
            $field = Field::firstOrCreate(['nama' => trim($request->bidang_lain)]);
        } elseif ($request->filled('bidang_id') && $request->bidang_id !== '__other') {
            $field = Field::find($request->bidang_id);
        }

        $wasRejected = $research->status === 'rejected';

        $data = [
            'judul' => $request->judul,
            'penulis' => $request->penulis,
            'nik_peneliti' => $request->nik_peneliti,
            'telepon_peneliti' => $request->telepon_peneliti,
            'bidang_id' => $field?->id,
            'institusi_id' => $institution->id,
            'lokasi' => $request->lokasi,
            'tahun' => $request->tahun,
            'abstrak' => $request->abstrak,
            'kata_kunci' => $request->kata_kunci,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
        ];

        $pendingDeletion = [];

        if ($request->hasFile('pdf_file')) {
            $newPdfPath = $this->storeFile($request->file('pdf_file'), 'penelitian', 'lampiran-penelitian');
            if ($newPdfPath) {
                $data['berkas_pdf'] = $newPdfPath;
                if ($research->berkas_pdf) {
                    $pendingDeletion[] = $research->berkas_pdf;
                }
            }
        }
        if ($request->hasFile('campus_letter')) {
            $campusPath = $this->storeFile(
                $request->file('campus_letter'),
                'penelitian/surat-kampus',
                'surat-kampus'
            );
            if ($campusPath) {
                $data['berkas_surat_kampus'] = $campusPath;
                if ($research->berkas_surat_kampus) {
                    $pendingDeletion[] = $research->berkas_surat_kampus;
                }
            }
        }

        if ($isAdmin && $request->hasFile('results_file')) {
            $resultsPath = $this->storeFile($request->file('results_file'), 'penelitian/hasil', 'hasil-penelitian');
            if ($resultsPath) {
                $data['berkas_hasil'] = $resultsPath;
                $data['hasil_diunggah_pada'] = now();
                if ($research->berkas_hasil) {
                    $pendingDeletion[] = $research->berkas_hasil;
                }
            }
        }
        if ($isAdmin && $request->hasFile('kesbang_letter')) {
            $kesbangPath = $this->storeFile(
                $request->file('kesbang_letter'),
                'penelitian/surat-rekomendasi',
                'surat-rekomendasi'
            );
            if ($kesbangPath) {
                $data['berkas_surat_kesbang'] = $kesbangPath;
                $data['nomor_surat_kesbang'] = $request->nomor_surat_kesbang;
                $data['tanggal_surat_kesbang'] = $request->tanggal_surat_kesbang;
                $data['diverifikasi_kesbang_pada'] = now();
                $data['diverifikasi_kesbang_oleh'] = $request->user()->id;
                $data['status'] = 'kesbang_verified';
                if ($research->berkas_surat_kesbang) {
                    $pendingDeletion[] = $research->berkas_surat_kesbang;
                }
            }
        } elseif ($isAdmin && ($request->filled('nomor_surat_kesbang') || $request->filled('tanggal_surat_kesbang'))) {
            $data['nomor_surat_kesbang'] = $request->nomor_surat_kesbang;
            $data['tanggal_surat_kesbang'] = $request->tanggal_surat_kesbang;
        }

        if ($wasRejected) {
            // Reset siklus approval dan kosongkan surat rekomendasi lama
            if ($research->berkas_surat_kesbang) {
                $pendingDeletion[] = $research->berkas_surat_kesbang;
            }
            $data = array_merge($data, [
                'status' => 'submitted',
                'diajukan_pada' => now(),
                'disetujui_pada' => null,
                'disetujui_oleh' => null,
                'catatan_keputusan' => null,
                'diajukan_ulang_pada' => now(),
                'berkas_surat_kesbang' => null,
            ]);
        }

        $research->update($data);

        foreach ($pendingDeletion as $path) {
            $this->deleteIfExists($path);
        }

        $message = $wasRejected
            ? 'Perubahan disimpan dan pengajuan dikirim ulang ke admin.'
            : 'Penelitian berhasil diperbarui.';

        return redirect()->route('researches.index')->with('success', $message);
    }

    /**
     * Menyetujui penelitian (admin saja)
     */
    public function approve(Request $request, Research $research)
    {
        $user = auth()->user();
        if (!$user || !$user->hasAdminAccess()) {
            abort(403, 'Akses ditolak. Hanya admin yang bisa menyetujui penelitian.');
        }

        $isBappedaAdmin = $user->hasRole('admin') && !$user->hasRole(['kesbangpol', 'superadmin']);
        $canAutoVerify = $isBappedaAdmin && is_null($research->diverifikasi_kesbang_pada);

        // Admin BAPPEDA dapat langsung menganggap sudah diverifikasi Kesbang jika belum
        if (is_null($research->diverifikasi_kesbang_pada) && !$canAutoVerify) {
            abort(422, 'Tidak dapat menyetujui: belum diverifikasi Kesbangpol.');
        }

        $request->validate([
            'catatan_keputusan' => 'nullable|string|max:2000',
        ]);

        $wasRejected = $research->status === 'rejected';
        $note = $request->catatan_keputusan;

        $payload = [
            'status' => 'approved',
            'disetujui_pada' => now(),
            'disetujui_oleh' => $user->id,
            'catatan_keputusan' => $note,
            'alasan_penolakan' => null,
            'ditolak_pada' => null,
            'ditolak_oleh' => null,
            'diajukan_ulang_pada' => null,
        ];

        if ($canAutoVerify) {
            $payload['diverifikasi_kesbang_pada'] = now();
            $payload['diverifikasi_kesbang_oleh'] = $user->id;
        }

        $research->update($payload);

        return redirect()->back()->with('success', $wasRejected ? 'Penelitian disetujui kembali setelah perbaikan.' : 'Penelitian disetujui.');
    }

    /**
     * Menolak penelitian (admin saja)
     */
    public function reject(Request $request, Research $research)
    {
        if (!auth()->user()->hasAdminAccess()) {
            abort(403, 'Akses ditolak. Hanya admin yang bisa menolak penelitian.');
        }

        if (is_null($research->diverifikasi_kesbang_pada)) {
            abort(422, 'Tidak dapat menolak: belum diverifikasi Kesbangpol.');
        }

        $request->validate([
            'alasan_penolakan' => 'required|string|max:2000',
            'catatan_keputusan' => 'nullable|string|max:2000',
        ]);

        $research->update([
            'status' => 'rejected',
            'ditolak_pada' => now(),
            'ditolak_oleh' => auth()->id(),
            'alasan_penolakan' => $request->alasan_penolakan,
            'catatan_keputusan' => $request->catatan_keputusan ?? $request->alasan_penolakan,
            'diajukan_ulang_pada' => null,
        ]);

        return redirect()->back()->with('error', 'Penelitian ditolak.');
    }

    /**
     * Form tambah penelitian
     */
    public function create()
    {
        $role = auth()->user()->peran ?? null;
        if (in_array($role, ['kesbangpol'], true)) {
            abort(403, 'Akses read-only. Kesbang tidak dapat mengunggah penelitian.');
        }
        $fields = Field::all();
        return view('researches.create', compact('fields'));
    }

    /**
     * Simpan penelitian baru
     */
    public function store(Request $request)
    {
        $role = auth()->user()->peran ?? null;
        $isAdmin = auth()->user()?->hasAdminAccess();
        $campusRule = $isAdmin
            ? 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480'
            : 'required|file|mimes:pdf,jpg,jpeg,png|max:20480';
        if (in_array($role, ['kesbangpol'], true)) {
            abort(403, 'Akses read-only. Kesbang tidak dapat mengunggah penelitian.');
        }
        // Normalisasi peneliti menjadi string tunggal (pisah dengan "; ")
        $request->merge(['penulis' => $this->normalizeAuthors($request)]);

        $request->validate([
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'nik_peneliti' => 'required|string|min:8|max:32',
            'telepon_peneliti' => 'required|string|max:32',
            'bidang_id' => 'nullable|required_without:bidang_lain|exists:bidang,id',
            'bidang_lain' => 'nullable|required_without:bidang_id|string|max:255',
            'nama_institusi' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'tahun' => 'required|digits:4|integer|min:2000|max:' . date('Y'),
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'pdf_file' => 'nullable|mimes:pdf|max:20480',
            'campus_letter' => $campusRule,
            'results_file' => $isAdmin ? 'nullable|mimes:pdf|max:20480' : 'nullable',
            'kesbang_letter' => $isAdmin ? 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480' : 'nullable',
            'abstrak' => 'nullable|string',
            'kata_kunci' => 'nullable|string',
        ], [
            'pdf_file.max' => 'Ukuran berkas proposal maksimal 20 MB.',
            'results_file.max' => 'Ukuran berkas hasil maksimal 20 MB.',
            'kesbang_letter.max' => 'Ukuran surat rekomendasi maksimal 20 MB.',
            'campus_letter.max' => 'Ukuran surat kampus maksimal 20 MB.',
        ]);

        $institution = Institution::firstOrCreate(['nama' => trim($request->nama_institusi)]);

        $field = null;
        if ($request->filled('bidang_lain')) {
            $field = Field::firstOrCreate(['nama' => trim($request->bidang_lain)]);
        } elseif ($request->filled('bidang_id') && $request->bidang_id !== '__other') {
            $field = Field::find($request->bidang_id);
        }

        $pdfPath = '';
        if ($request->hasFile('pdf_file')) {
            $pdfPath = $this->storeFile($request->file('pdf_file'), 'penelitian', 'lampiran-penelitian');
        }
        $campusLetterPath = '';
        if ($request->hasFile('campus_letter')) {
            $campusLetterPath = $this->storeFile(
                $request->file('campus_letter'),
                'penelitian/surat-kampus',
                'surat-kampus'
            );
        }
        $resultsPath = '';
        $resultsUploadedAt = null;
        if ($isAdmin && $request->hasFile('results_file')) {
            $resultsPath = $this->storeFile($request->file('results_file'), 'penelitian/hasil', 'hasil-penelitian');
            $resultsUploadedAt = now();
        }
        $kesbangPath = '';
        $kesbangVerifiedAt = null;
        $kesbangVerifiedBy = null;
        $status = 'submitted';
        $user = Auth::user();
        $isBappedaAdmin = $user && $user->hasRole('admin') && !$user->hasRole(['kesbangpol', 'superadmin']);

        if ($isAdmin && $request->hasFile('kesbang_letter')) {
            $kesbangPath = $this->storeFile(
                $request->file('kesbang_letter'),
                'penelitian/surat-rekomendasi',
                'surat-rekomendasi'
            );
            $kesbangVerifiedAt = now();
            $kesbangVerifiedBy = Auth::id();
            $status = 'kesbang_verified';
        } elseif ($isBappedaAdmin) {
            // Unggahan admin BAPPEDA langsung dianggap terverifikasi Kesbang
            $kesbangVerifiedAt = now();
            $kesbangVerifiedBy = $user->id;
            $status = 'kesbang_verified';
        }

        Research::create([
            'judul' => $request->judul,
            'penulis' => $request->penulis,
            'nik_peneliti' => $request->nik_peneliti,
            'telepon_peneliti' => $request->telepon_peneliti,
            'bidang_id' => $field?->id,
            'institusi_id' => $institution->id,
            'lokasi' => $request->lokasi,
            'tahun' => $request->tahun,
            'abstrak' => $request->abstrak,
            'kata_kunci' => $request->kata_kunci,
            'berkas_pdf' => $pdfPath,
            'berkas_surat_kampus' => $campusLetterPath,
            'berkas_hasil' => $resultsPath,
            'hasil_diunggah_pada' => $resultsUploadedAt,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => $status,
            'pengunggah_id' => Auth::id(),
            'diajukan_pada' => now(),
            'berkas_surat_kesbang' => $kesbangPath,
            'nomor_surat_kesbang' => $request->nomor_surat_kesbang,
            'tanggal_surat_kesbang' => $request->tanggal_surat_kesbang,
            'diverifikasi_kesbang_pada' => $kesbangVerifiedAt,
            'diverifikasi_kesbang_oleh' => $kesbangVerifiedBy,
        ]);

        return redirect()->route('researches.index')->with('success', 'Penelitian berhasil diunggah!');
    }

    /**
     * Gabungkan banyak peneliti menjadi string tunggal.
     */
    protected function normalizeAuthors(Request $request): string
    {
        $daftarPenulis = $request->input('daftar_penulis');
        if (is_array($daftarPenulis)) {
            $daftarPenulis = array_values(array_filter(array_map('trim', $daftarPenulis), fn($v) => $v !== ''));
            if (!empty($daftarPenulis)) {
                return implode('; ', $daftarPenulis);
            }
        }

        return trim((string) $request->input('penulis', ''));
    }

    /**
     * Konfirmasi verifikasi oleh Kesbangpol
     */
    public function verifyKesbang(Request $request, Research $research)
    {
        $user = auth()->user();
        if (!$user || (!$user->hasKesbangAccess() && !$user->hasAdminAccess())) {
            abort(403, 'Akses ditolak. Hanya Kesbangpol atau Admin Bappeda yang dapat memverifikasi.');
        }

        if ($research->status === 'approved') {
            return back()->with('error', 'Pengajuan sudah diputuskan BAPPPEDA.');
        }

        $alreadyVerified = (bool) $research->diverifikasi_kesbang_pada;
        $wasRejected = $research->status === 'rejected';
        $hasExistingLetter = (bool) $research->berkas_surat_kesbang;
        $hasExistingMeta = $research->nomor_surat_kesbang && $research->tanggal_surat_kesbang;

        $request->validate([
            'kesbang_letter' => ($hasExistingLetter ? 'nullable' : 'required') . '|file|mimes:pdf,jpg,jpeg,png|max:20480',
            'nomor_surat_kesbang' => ($hasExistingMeta ? 'nullable' : 'required') . '|string|max:255',
            'tanggal_surat_kesbang' => ($hasExistingMeta ? 'nullable' : 'required') . '|date',
        ], [
            'kesbang_letter.required' => 'Unggah surat rekomendasi sebelum memverifikasi.',
            'nomor_surat_kesbang.required' => 'Nomor surat rekomendasi wajib diisi.',
            'tanggal_surat_kesbang.required' => 'Tanggal surat rekomendasi wajib diisi.',
            'kesbang_letter.max' => 'Ukuran surat rekomendasi maksimal 20 MB.',
        ]);

        $payload = [
            'status' => 'kesbang_verified',
            'diverifikasi_kesbang_pada' => $wasRejected ? now() : ($research->diverifikasi_kesbang_pada ?? now()),
            'diverifikasi_kesbang_oleh' => $user->id,
            'alasan_penolakan' => $wasRejected ? null : $research->alasan_penolakan,
            'ditolak_pada' => $wasRejected ? null : $research->ditolak_pada,
            'ditolak_oleh' => $wasRejected ? null : $research->ditolak_oleh,
            'catatan_keputusan' => $wasRejected ? null : $research->catatan_keputusan,
            'nomor_surat_kesbang' => $request->nomor_surat_kesbang ?: $research->nomor_surat_kesbang,
            'tanggal_surat_kesbang' => $request->tanggal_surat_kesbang ?: $research->tanggal_surat_kesbang,
        ];
        $pendingDeletion = [];

        if ($request->hasFile('kesbang_letter')) {
            $payload['berkas_surat_kesbang'] = $this->storeFile(
                $request->file('kesbang_letter'),
                'penelitian/surat-rekomendasi',
                'surat-rekomendasi'
            );
            if ($research->berkas_surat_kesbang) {
                $pendingDeletion[] = $research->berkas_surat_kesbang;
            }
        }

        $research->update($payload);

        foreach ($pendingDeletion as $path) {
            $this->deleteIfExists($path);
        }

        $message = $wasRejected
            ? 'Pengajuan dikembalikan dan diverifikasi ulang oleh Kesbangpol.'
            : ($alreadyVerified
                ? 'Data sudah diverifikasi Kesbangpol.'
                : 'Data diverifikasi oleh Kesbangpol dan diteruskan ke BAPPPEDA.');

        $research->refresh();
        $notificationSent = false;
        $contactEmail = $research->email_peneliti ?? optional($research->submittedBy)->surel;
        if ($research->berkas_surat_kesbang && $contactEmail) {
            try {
                Mail::to($contactEmail)->send(new ResearchRecommendationReady($research));
                $notificationSent = true;
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $message .= $notificationSent
            ? ' Notifikasi email terkirim ke peneliti.'
            : ($research->berkas_surat_kesbang ? ' Email peneliti belum tersedia.' : '');

        return back()->with('success', trim($message));
    }

    /**
     * Penolakan oleh Kesbangpol dengan alasan.
     */
    public function rejectKesbang(Request $request, Research $research)
    {
        $user = auth()->user();
        if (!$user || !$user->hasKesbangAccess()) {
            abort(403, 'Akses ditolak. Hanya Kesbangpol yang dapat menolak.');
        }

        if (in_array($research->status, ['approved', 'rejected'], true)) {
            return back()->with('error', 'Pengajuan sudah diputuskan BAPPPEDA.');
        }

        $request->validate([
            'alasan_penolakan' => 'required|string|max:2000',
            'catatan_keputusan' => 'nullable|string|max:2000',
        ]);

        $research->update([
            'status' => 'rejected',
            'alasan_penolakan' => $request->alasan_penolakan,
            'catatan_keputusan' => $request->catatan_keputusan ?? $request->alasan_penolakan,
            'ditolak_pada' => now(),
            'ditolak_oleh' => $user->id,
            'diverifikasi_kesbang_pada' => now(),
            'diverifikasi_kesbang_oleh' => $user->id,
            'diajukan_ulang_pada' => null,
        ]);

        return back()->with('error', 'Pengajuan ditolak oleh Kesbangpol.');
    }

    /**
     * Unggah hasil penelitian (setelah waktu penelitian selesai)
     */
    public function uploadResults(Request $request, Research $research)
    {
        $this->ensureResultsUploadable($research);

        $request->validate([
            'abstrak' => 'nullable|string',
            'kata_kunci' => 'nullable|string',
            'pdf_file' => 'nullable|mimes:pdf|max:20480',
        ], [
            'pdf_file.max' => 'Ukuran berkas hasil maksimal 20 MB.',
        ]);

        $data = [
            'abstrak' => $request->abstrak ?? $research->abstrak,
            'kata_kunci' => $request->kata_kunci ?? $research->kata_kunci,
            'hasil_diunggah_pada' => now(),
        ];
        $pendingDeletion = [];

        if ($request->hasFile('pdf_file')) {
            $uploaded = $request->file('pdf_file');
            $originalName = pathinfo($uploaded->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = strtolower($uploaded->getClientOriginalExtension());
            $safeBase = Str::slug($originalName);
            $fileName = $safeBase . '-' . now()->format('Ymd-His') . '.' . $extension;
            $directory = 'penelitian';
            $data['berkas_hasil'] = $uploaded->storeAs($directory, $fileName, 'public');
            if ($research->berkas_hasil) {
                $pendingDeletion[] = $research->berkas_hasil;
            }
        }

        $research->update($data);

        foreach ($pendingDeletion as $path) {
            $this->deleteIfExists($path);
        }

        return back()->with('success', 'Hasil penelitian berhasil diunggah.');
    }

    /**
     * Halaman terpisah untuk unggah hasil penelitian
     */
    public function editResults(Research $research)
    {
        $this->ensureResultsUploadable($research);

        return view('researches.results', compact('research'));
    }

    /**
     * Kirim email pengingat unggah hasil (akses Kesbangpol/Admin).
     */
    public function remindResults(Request $request, Research $research)
    {
        $user = $request->user();
        if (!$user || (!$user->hasKesbangAccess() && !$user->hasAdminAccess())) {
            abort(403, 'Hanya Kesbangpol atau admin yang dapat mengirim pengingat.');
        }

        $research->loadMissing('submitter');
        $contactEmail = $research->email_peneliti ?? optional($research->submitter)->surel;

        if (!$contactEmail) {
            return back()->with('error', 'Email peneliti belum tersedia.');
        }

        if ($research->hasil_diunggah_pada) {
            return back()->with('error', 'Hasil sudah diunggah, pengingat tidak diperlukan.');
        }

        Mail::to($contactEmail)->send(new ResearchResultReminder($research));

        return back()->with('success', 'Email pengingat unggah hasil telah dikirim.');
    }

    /**
     * Daftar penelitian milik user untuk unggah hasil
     */
    public function myResults()
    {
        if (!auth()->check()) { abort(403); }
        $researches = Research::where('pengunggah_id', auth()->id())
            ->orderByDesc('id')
            ->with(['institution:id,nama', 'field:id,nama'])
            ->get();
        return view('researches.results_index', compact('researches'));
    }

    /**
     * Hapus penelitian oleh pemilik
     */
    public function destroy(Research $research)
    {
        $this->ensureEditableByOwner($research);
        $this->deleteIfExists($research->berkas_pdf);
        $this->deleteIfExists($research->berkas_surat_kampus);
        $this->deleteIfExists($research->berkas_surat_kesbang);
        $research->delete();

        return redirect()->route('researches.index')->with('success', 'Penelitian berhasil dihapus.');
    }

    /**
     * Pastikan user berhak mengubah/ menghapus.
     */
    protected function ensureEditableByOwner(Research $research): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'Anda tidak dapat mengubah penelitian ini.');
        }

        if ($user->isSuperAdmin()) {
            return;
        }

        if ((int) $user->id !== (int) ($research->pengunggah_id ?? 0)) {
            abort(403, 'Anda tidak dapat mengubah penelitian ini.');
        }

        $editableStatuses = ['draft', 'submitted', 'rejected'];
        if (!in_array((string) $research->status, $editableStatuses, true)) {
            abort(403, 'Penelitian yang sudah diproses tidak dapat diubah atau dihapus.');
        }

        if ($research->status !== 'rejected' && $research->diverifikasi_kesbang_pada) {
            abort(403, 'Penelitian yang sudah diproses tidak dapat diubah atau dihapus.');
        }
    }

    /**
     * Pastikan hasil hanya bisa diunggah setelah disetujui Kesbangpol.
     */
    protected function ensureResultsUploadable(Research $research): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Anda tidak berhak mengunggah hasil untuk penelitian ini.');
        }

        $isOwner = (int) $user->id === (int) ($research->pengunggah_id ?? 0);
        $isAdmin = $user->hasAdminAccess();
        if (!$isOwner && !$isAdmin) {
            abort(403, 'Anda tidak berhak mengunggah hasil untuk penelitian ini.');
        }

        // Admin BAPPEDA dapat unggah hasil tanpa menunggu kesbang
        if (!$isAdmin && !$research->diverifikasi_kesbang_pada) {
            abort(403, 'Unggah hasil tersedia setelah disetujui Kesbangpol.');
        }

        if ($research->status === 'rejected' && !$isAdmin) {
            throw new HttpResponseException(
                redirect()->route('researches.results.my')
                    ->with('error', 'Pengajuan yang ditolak tidak dapat mengunggah hasil.')
            );
        }
    }

    /**
     * Hapus file lama jika ada.
     */
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
            return;
        }

        if (Storage::exists($normalized)) {
            Storage::delete($normalized);
        }
    }

    /**
     * Simpan file terunggah dengan nama aman.
     */
    protected function storeFile(UploadedFile $file, string $directory, string $fallbackBase = 'lampiran'): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = strtolower($file->getClientOriginalExtension());
        $safeBase = Str::slug($originalName) ?: $fallbackBase;
        $fileName = $safeBase . '-' . now()->format('Ymd-His') . '.' . $extension;
        $relativePath = $directory . '/' . $fileName;

        while (Storage::disk('public')->exists($relativePath)) {
            $fileName = $safeBase . '-' . now()->format('Ymd-His') . '-' . Str::random(6) . '.' . $extension;
            $relativePath = $directory . '/' . $fileName;
        }

        return $file->storeAs($directory, $fileName, 'public');
    }

    /**
     * Export data penelitian (Kesbangpol/Admin) per tahun/bulan ke CSV.
     */
    public function export(Request $request)
    {
        $user = $request->user();
        if (!$user || (!$user->hasKesbangAccess() && !$user->hasAdminAccess())) {
            abort(403, 'Hanya Kesbangpol atau admin yang dapat mengekspor data.');
        }

        $request->validate([
            'tahun' => 'nullable|digits:4',
            'month' => 'nullable|integer|min:1|max:12',
            'status' => 'nullable|in:submitted,kesbang_verified,approved,rejected',
            'all' => 'sometimes|boolean',
        ]);

        $showAll = $request->boolean('all') || (!$request->filled('tahun') && !$request->filled('month'));
        $tahun = $showAll ? null : $request->input('tahun');
        $month = $showAll ? null : $request->input('month');
        $status = $request->input('status');
        $dateField = 'diajukan_pada';

        if (!$showAll && !$tahun && !$month) {
            return back()->with('error', 'Isi tahun/bulan atau centang semua data sebelum ekspor.');
        }

        $query = Research::with(['field:id,nama', 'institution:id,nama'])
            ->select([
                'id',
                'judul',
                'penulis',
                'bidang_id',
                'institusi_id',
                'tanggal_mulai',
                'tanggal_selesai',
                'tahun',
                'status',
                'diajukan_pada',
                'diverifikasi_kesbang_pada',
                'disetujui_pada',
                'nomor_surat_kesbang',
                'tanggal_surat_kesbang',
                'telepon_peneliti',
                'nik_peneliti',
            ])
            ->when($status, fn($q) => $q->where('status', $status));

        if ($dateField === 'diverifikasi_kesbang_pada') {
            $query->whereNotNull('diverifikasi_kesbang_pada');
        }

        if ($tahun) {
            $query->whereYear($dateField, (int) $tahun);
        }

        if ($month) {
            $query->whereMonth($dateField, (int) $month);
        }

        $records = $query->orderByDesc($dateField)->orderByDesc('id')->get();

        $periodLabel = $tahun ? ('-' . $tahun . ($month ? '-' . str_pad((string) $month, 2, '0', STR_PAD_LEFT) : '')) : '-all';
        $filename = 'penelitian-' . $dateField . $periodLabel . '.csv';

        $columns = [
            'Judul',
            'Nama Peneliti',
            'Bidang',
            'Institusi',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Kontak',
            'Status',
            'Nomor Surat Rekomendasi',
            'Tanggal Surat Rekomendasi',
        ];

        $callback = function () use ($records, $columns) {
            $handle = fopen('php://output', 'w');
            // BOM supaya Excel mengenali UTF-8
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $columns);

            $formatDate = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d/m/Y') : '';
            $asText = fn($v) => ($v !== null && $v !== '') ? '="' . $v . '"' : '';

            foreach ($records as $row) {
                fputcsv($handle, [
                    $row->judul,
                    $row->penulis,
                    optional($row->field)->nama,
                    optional($row->institution)->nama,
                    $formatDate($row->tanggal_mulai),
                    $formatDate($row->tanggal_selesai),
                    $asText($row->telepon_peneliti),
                    $row->status,
                    $asText($row->nomor_surat_kesbang),
                    $formatDate($row->tanggal_surat_kesbang),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export PDF daftar penelitian (admin/Kesbang).
     */
    public function exportPdf(Request $request)
    {
        $user = $request->user();
        if (!$user || (!$user->hasKesbangAccess() && !$user->hasAdminAccess())) {
            abort(403, 'Hanya Kesbangpol atau admin yang dapat mengekspor data.');
        }

        $request->validate([
            'tahun' => 'nullable|digits:4',
            'month' => 'nullable|integer|min:1|max:12',
            'status' => 'nullable|in:submitted,kesbang_verified,approved,rejected',
            'all' => 'sometimes|boolean',
        ]);

        $showAll = $request->boolean('all') || (!$request->filled('tahun') && !$request->filled('month'));
        $tahun = $showAll ? null : $request->input('tahun');
        $month = $showAll ? null : $request->input('month');
        $status = $request->input('status');
        $dateField = 'diajukan_pada';

        if (!$showAll && !$tahun && !$month) {
            return back()->with('error', 'Isi tahun/bulan atau centang semua data sebelum ekspor.');
        }

        $query = Research::with(['field:id,nama', 'institution:id,nama'])
            ->select([
                'id',
                'judul',
                'penulis',
                'bidang_id',
                'institusi_id',
                'tahun',
                'status',
                'tanggal_mulai',
                'tanggal_selesai',
                'diajukan_pada',
                'diverifikasi_kesbang_pada',
                'disetujui_pada',
                'nomor_surat_kesbang',
                'tanggal_surat_kesbang',
                'telepon_peneliti',
                'nik_peneliti',
            ])
            ->when($status, fn($q) => $q->where('status', $status));

        if ($tahun) {
            $query->whereYear($dateField, (int) $tahun);
        }

        if ($month) {
            $query->whereMonth($dateField, (int) $month);
        }

        $records = $query->orderByDesc($dateField)->orderByDesc('id')->get();
        $filename = 'penelitian-' . ($tahun ?: 'all') . ($month ? '-' . str_pad((string) $month, 2, '0', STR_PAD_LEFT) : '') . '.pdf';

        $pdf = Pdf::loadView('exports.researches_pdf', [
            'records' => $records,
            'formatDate' => fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d/m/Y') : '-',
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }
}








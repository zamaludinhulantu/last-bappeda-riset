<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ResearchResultReminder;
use App\Models\Field;
use App\Models\Research;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ResearchAdminController extends Controller
{
    /**
     * Display a listing of the research records for admins.
     */
    public function index(Request $request)
    {
        $query = Research::query()
            ->with(['field:id,nama', 'institution:id,nama']);

        // Untuk admin BAPPEDA: tampilkan yang sudah diverifikasi Kesbangpol, plus entri yang mereka unggah sendiri meski belum diverifikasi
        $user = $request->user();
        if ($user && $user->hasRole('admin') && !$user->hasRole(['superadmin', 'kesbangpol'])) {
            $query->where(function ($builder) use ($user) {
                $builder->whereNotNull('diverifikasi_kesbang_pada')
                    ->orWhere('pengunggah_id', $user->id);
            });
        } else {
            // Peran lain tetap hanya melihat yang sudah diverifikasi
            $query->whereNotNull('diverifikasi_kesbang_pada');
        }

        $search = trim((string) $request->input('q', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('judul', 'like', "%{$search}%")
                    ->orWhere('penulis', 'like', "%{$search}%")
                    ->orWhere('nik_peneliti', 'like', "%{$search}%")
                    ->orWhere('telepon_peneliti', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($fieldId = $request->input('bidang_id')) {
            $query->where('bidang_id', $fieldId);
        }

        if ($year = $request->input('tahun')) {
            $query->where('tahun', (int) $year);
        }

        if ($institution = trim((string) $request->input('institution'))) {
            $query->whereHas('institution', function ($builder) use ($institution) {
                $builder->where('nama', 'like', '%' . $institution . '%');
            });
        }

        $researches = $query->latest()->paginate(15)->withQueryString();

        $fields = Field::orderBy('nama')->get(['id', 'nama']);
        $years = Research::select('tahun')->whereNotNull('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');

        return view('admin.researches.index', compact('researches', 'fields', 'years'));
    }

    /**
     * Display the specified research record with complete attributes.
     */
    public function show(Research $research)
    {
        $user = request()->user();
        $isBappedaAdmin = $user
            && $user->hasRole('admin')
            && !$user->hasRole(['superadmin', 'kesbangpol']);
        $isUploader = $isBappedaAdmin && (int) $research->pengunggah_id === (int) $user->id;

        // Tampilkan ke admin BAPPEDA meski belum diverifikasi jika itu unggahan mereka sendiri
        if (is_null($research->diverifikasi_kesbang_pada) && !$isUploader) {
            abort(404);
        }

        $research->load(['submitter', 'institution', 'field', 'rejectedBy']);
        return view('admin.researches.show', compact('research'));
    }

    /**
     * Download a file attribute from a research record (admin access).
     */
    public function download(Research $research, string $field)
    {
        $value = data_get($research, $field);

        if (!$value || !is_string($value)) {
            abort(404);
        }

        $path = ltrim($value, '/');
        $forceDownload = request()->boolean('download');

        // Try common disks/paths safely
        $buildResponse = function (string $disk, string $relative) use ($forceDownload) {
            $storage = Storage::disk($disk);
            $absolute = $storage->path($relative);
            $mime = $storage->mimeType($relative) ?? 'application/octet-stream';
            $filename = basename($relative);

            if ($forceDownload) {
                return response()->download($absolute, $filename, [
                    'Content-Type' => $mime,
                ]);
            }

            return response()->file($absolute, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
        };

        if (str_starts_with($path, 'storage/')) {
            $relative = substr($path, strlen('storage/'));
            if (Storage::disk('public')->exists($relative)) {
                return $buildResponse('public', $relative);
            }
        }

        if (Storage::disk('public')->exists($path)) {
            return $buildResponse('public', $path);
        }

        if (Storage::exists($path)) {
            return $buildResponse('local', $path);
        }

        // Fallback to public asset if looks like public URL
        if (preg_match('/\.(pdf|docx?|xlsx?|pptx?|csv|jpg|jpeg|png|gif|svg|webp|txt|zip|rar)$/i', $path)) {
            return redirect()->to(asset($value));
        }

        abort(404);
    }

    /**
     * Remove a file attribute from a research record (admin only).
     */
    public function destroyFile(Research $research, string $field)
    {
        $value = data_get($research, $field);

        if ($value && is_string($value)) {
            $path = ltrim($value, '/');

            if (str_starts_with($path, 'storage/')) {
                $relative = substr($path, strlen('storage/'));
                if (Storage::disk('public')->exists($relative)) {
                    Storage::disk('public')->delete($relative);
                }
            } elseif (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            } elseif (Storage::exists($path)) {
                Storage::delete($path);
            }
        }

        // Clear the attribute regardless of file presence
        $research->{$field} = null;
        $research->save();

        return back()->with('success', 'Berkas berhasil dihapus.');
    }

    /**
     * Send an email reminder asking the researcher to upload final results.
     */
    public function remindResults(Request $request, Research $research)
    {
        $research->loadMissing('submitter');
        $contactEmail = $research->email_peneliti ?? optional($research->submitter)->surel;

        if (!$contactEmail) {
            return back()->with('error', 'Email peneliti belum tersedia.');
        }

        Mail::to($contactEmail)->send(new ResearchResultReminder($research));

        return back()->with('success', 'Email pengingat unggah hasil telah dikirim.');
    }
}

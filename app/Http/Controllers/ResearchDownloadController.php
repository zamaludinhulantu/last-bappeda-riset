<?php

namespace App\Http\Controllers;

use App\Models\Research;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResearchDownloadController extends Controller
{
    /**
     * Download a file attribute from a research record.
     * Researchers can only download their own; admins can download any.
     */
    public function download(Research $research, string $field)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        // Normalize to integers to avoid strict type mismatches from DB string IDs
        $isOwner = (int) ($research->pengunggah_id ?? 0) === (int) $user->id;
        $hasPower = $user->hasAdminAccess() || $user->hasKesbangAccess();

        if (!$hasPower && !$isOwner) {
            abort(403);
        }

        $value = data_get($research, $field);
        if (!$value || !is_string($value)) {
            abort(404);
        }

        $path = ltrim($value, '/');
        $forceDownload = request()->boolean('download');

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

        if (preg_match('/\.(pdf|docx?|xlsx?|pptx?|csv|jpg|jpeg|png|gif|svg|webp|txt|zip|rar)$/i', $path)) {
            return redirect()->to(asset($value));
        }

        abort(404);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Research;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isAdminPanel = $user->hasRole(['admin', 'superadmin', 'kesbangpol']);

        $baseQuery = Research::with(['field:id,nama', 'institution:id,nama'])
            ->select([
                'id',
                'judul',
                'penulis',
                'institusi_id',
                'bidang_id',
                'status',
                'disetujui_pada',
                'ditolak_pada',
                'diajukan_pada',
                'diubah_pada',
                'catatan_keputusan',
                'alasan_penolakan',
            ]);

        if (!$isAdminPanel) {
            $baseQuery->where('pengunggah_id', $user->id);
        }

        $latestItems = (clone $baseQuery)
            ->get()
            ->sortByDesc(function ($research) {
                return $research->disetujui_pada
                    ?? $research->ditolak_pada
                    ?? $research->diajukan_pada
                    ?? $research->diubah_pada;
            })
            ->take(15)
            ->values()
            ->map(function ($research) {
                return [
                    'id' => $research->id,
                    'title' => $research->judul,
                    'author' => $research->penulis,
                    'field' => optional($research->field)->nama,
                    'institution' => optional($research->institution)->nama,
                    'status' => $research->status ?? 'draft',
                    'note' => $research->catatan_keputusan ?? $research->alasan_penolakan,
                    'event_at' => $research->disetujui_pada
                        ?? $research->ditolak_pada
                        ?? $research->diajukan_pada
                        ?? $research->diubah_pada,
                ];
            });

        $counts = [
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'submitted' => (clone $baseQuery)->where('status', 'submitted')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];

        return view('announcements.index', [
            'announcements' => $latestItems,
            'counts' => $counts,
            'isAdminPanel' => $isAdminPanel,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Research;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $query = Research::query();

        if (Auth::check()) {
            $user = Auth::user();
            if (!$user->hasRole(['admin', 'kesbangpol', 'superadmin'])) {
                $query = $query->where('pengunggah_id', $user->id);
            }
        }

        $total = (clone $query)->count();
        $approved = (clone $query)->where('status', 'approved')->count();
        $rejected = (clone $query)->where('status', 'rejected')->count();
        $submitted = (clone $query)->where('status', 'submitted')->count();
        $draft = (clone $query)->where('status', 'draft')->count();
        $verified = (clone $query)->where('status', 'kesbang_verified')->count();

        $statusBreakdown = [
            'Disetujui' => $approved,
            'Ditolak' => $rejected,
            'Diajukan' => $submitted,
            'Terverifikasi Kesbang' => $verified,
            'Draft' => $draft,
        ];

        $timelineCollection = (clone $query)
            ->orderBy('dibuat_pada')
            ->get(['dibuat_pada', 'status'])
            ->groupBy(function ($item) {
                return optional($item->dibuat_pada)?->format('Y-m') ?? 'unknown';
            })
            ->sortKeys()
            ->map(function (Collection $items) {
                $labelDate = optional($items->first()?->dibuat_pada);
                $label = $labelDate ? $labelDate->translatedFormat('M Y') : 'Tanpa Tanggal';

                return [
                    'key' => $label,
                    'label' => $label,
                    'total' => $items->count(),
                    'submitted' => $items->where('status', 'submitted')->count(),
                    'approved' => $items->where('status', 'approved')->count(),
                ];
            })
            ->values();

        $perField = (clone $query)
            ->leftJoin('bidang', 'penelitian.bidang_id', '=', 'bidang.id')
            ->selectRaw('COALESCE(bidang.nama, "Umum") as nama, COUNT(*) as total')
            ->groupBy('nama')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $perYear = (clone $query)
            ->selectRaw('tahun, COUNT(*) as total')
            ->whereNotNull('tahun')
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get();

        $perFieldStatus = (clone $query)
            ->leftJoin('bidang', 'penelitian.bidang_id', '=', 'bidang.id')
            ->selectRaw('COALESCE(bidang.nama, "Umum") as nama')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN penelitian.status = "approved" THEN 1 ELSE 0 END) as approved_total')
            ->selectRaw('SUM(CASE WHEN penelitian.status = "submitted" THEN 1 ELSE 0 END) as submitted_total')
            ->selectRaw('SUM(CASE WHEN penelitian.status = "rejected" THEN 1 ELSE 0 END) as rejected_total')
            ->selectRaw('SUM(CASE WHEN penelitian.status = "kesbang_verified" THEN 1 ELSE 0 END) as verified_total')
            ->groupBy('nama')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $recentResearches = (clone $query)
            ->latest('diubah_pada')
            ->take(6)
            ->get();

        $fields = collect();
        $years = collect();
        if (Auth::check() && Auth::user()->hasRole(['admin', 'superadmin'])) {
            $fields = Field::orderBy('nama')->get(['id', 'nama']);
            $years = Research::select('tahun')
                ->whereNotNull('tahun')
                ->distinct()
                ->orderByDesc('tahun')
                ->pluck('tahun');
        }

        return view('dashboard', compact(
            'total',
            'approved',
            'rejected',
            'submitted',
            'recentResearches',
            'fields',
            'years',
            'statusBreakdown',
            'timelineCollection',
            'perField',
            'perYear',
            'perFieldStatus'
        ));
    }
}

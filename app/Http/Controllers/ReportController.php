<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Research;

class ReportController extends Controller
{
    public function statistics()
    {
        $perField = Field::withCount('researches')->orderBy('nama')->get();
        $perYear = Research::selectRaw('tahun, COUNT(*) as total')
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get();

        return view('reports.statistics', compact('perField', 'perYear'));
    }
}

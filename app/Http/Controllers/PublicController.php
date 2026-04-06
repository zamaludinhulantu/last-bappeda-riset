<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Research;
use App\Models\News;
use App\Models\ContactInfo;

class PublicController extends Controller
{
    public function about()
    {
        $contactInfo = ContactInfo::current();
        return view('public.about', compact('contactInfo'));
    }

    public function contact()
    {
        $contactInfo = ContactInfo::current();
        return view('public.contact', compact('contactInfo'));
    }

    public function statistics()
    {
        $perField = Field::withCount(['researches' => function($q){
            $q->where('status','approved');
        }])->orderBy('nama')->get();

        $perYear = Research::selectRaw('tahun, COUNT(*) as total')
            ->where('status', 'approved')
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get();

        return view('public.statistics', compact('perField', 'perYear'));
    }

    public function announcements()
    {
        $researches = Research::with(['field:id,nama', 'institution:id,nama'])
            ->where('status', 'approved')
            ->orderByDesc('disetujui_pada')
            ->orderByDesc('id')
            ->paginate(12);

        return view('public.announcements', compact('researches'));
    }

    public function news()
    {
        $items = News::published()
            ->latest('dipublikasikan_pada')
            ->latest()
            ->paginate(9);

        return view('public.news', compact('items'));
    }

    public function newsShow(News $news)
    {
        if (!$news->dipublikasikan_pada || ($news->status && $news->status !== 'published')) {
            abort(404);
        }

        return view('public.news_show', compact('news'));
    }
}

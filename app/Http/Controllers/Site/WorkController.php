<?php

namespace App\Http\Controllers\Site;

use App\Enums\PublishStatus;
use App\Http\Controllers\Controller;
use App\Models\Work;
use Illuminate\View\View;

class WorkController extends Controller
{
    /**
     * Display a listing of the published works.
     */
    public function index(): View
    {
        $works = Work::published()->with('tags')->latest('published_at')->latest('id')->get();

        return view('site.works.index', compact('works'));
    }

    /**
     * Display the work with its gallery. Drafts are not found.
     */
    public function show(Work $work): View
    {
        abort_unless($work->status === PublishStatus::Published, 404);

        return view('site.works.show', ['work' => $work->load(['category', 'tags', 'images'])]);
    }
}

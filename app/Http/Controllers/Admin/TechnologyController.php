<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TechnologyCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TechnologyRequest;
use App\Models\Technology;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TechnologyController extends Controller
{
    /**
     * Display a listing of the technologies.
     */
    public function index(): View
    {
        $technologies = Technology::withCount('works')->ordered()->get();

        return view('admin.technologies.index', compact('technologies'));
    }

    /**
     * Show the form for creating a new technology.
     */
    public function create(): View
    {
        return view('admin.technologies.create', ['categories' => TechnologyCategory::cases()]);
    }

    /**
     * Store a newly created technology.
     */
    public function store(TechnologyRequest $request): RedirectResponse
    {
        Technology::create($request->validated());

        return redirect()->route('admin.technologies.index')->with('status', '使用技術を登録しました。');
    }

    /**
     * Show the form for editing the technology.
     */
    public function edit(Technology $technology): View
    {
        return view('admin.technologies.edit', ['technology' => $technology, 'categories' => TechnologyCategory::cases()]);
    }

    /**
     * Update the technology.
     */
    public function update(TechnologyRequest $request, Technology $technology): RedirectResponse
    {
        $technology->update($request->validated());

        return redirect()->route('admin.technologies.index')->with('status', '使用技術を更新しました。');
    }

    /**
     * Remove the technology. It is detached from its works.
     */
    public function destroy(Technology $technology): RedirectResponse
    {
        $technology->delete();

        return redirect()->route('admin.technologies.index')->with('status', '使用技術を削除しました。');
    }
}

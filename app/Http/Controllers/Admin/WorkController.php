<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PublishStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WorkRequest;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Work;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WorkController extends Controller
{
    /**
     * Display a listing of the works.
     */
    public function index(): View
    {
        $works = Work::with('category')->withCount('images')->latest('id')->paginate(20);

        return view('admin.works.index', compact('works'));
    }

    /**
     * Show the form for creating a new work.
     */
    public function create(): View
    {
        return view('admin.works.create', $this->formOptions());
    }

    /**
     * Store a newly created work with its gallery images.
     */
    public function store(WorkRequest $request): RedirectResponse
    {
        $attributes = $request->safe()->except(['thumbnail', 'remove_thumbnail', 'images', 'delete_images', 'tags']);
        $attributes['thumbnail_path'] = $request->file('thumbnail')?->store('works', 'public');
        $newImages = $this->storeImages($request->file('images', []), 0);

        DB::transaction(function () use ($request, $attributes, $newImages) {
            $work = $request->user()->works()->create($attributes);
            $work->tags()->sync($request->validated('tags', []));
            $work->images()->createMany($newImages);
        });

        return redirect()->route('admin.works.index')->with('status', '制作アプリを登録しました。');
    }

    /**
     * Show the form for editing the work.
     */
    public function edit(Work $work): View
    {
        return view('admin.works.edit', ['work' => $work->load(['tags', 'images']), ...$this->formOptions()]);
    }

    /**
     * Update the work. Replaced, removed and deleted image files are deleted.
     */
    public function update(WorkRequest $request, Work $work): RedirectResponse
    {
        $attributes = $request->safe()->except(['thumbnail', 'remove_thumbnail', 'images', 'delete_images', 'tags']);
        $oldThumbnailPath = $work->thumbnail_path;

        if ($request->hasFile('thumbnail')) {
            $attributes['thumbnail_path'] = $request->file('thumbnail')->store('works', 'public');
        } elseif ($request->boolean('remove_thumbnail')) {
            $attributes['thumbnail_path'] = null;
        }

        // Only images of this work can be deleted, whatever IDs are posted.
        $deletedImages = $work->images()->whereKey($request->validated('delete_images', []))->get();
        $newImages = $this->storeImages($request->file('images', []), (int) $work->images()->max('sort_order') + 1);

        DB::transaction(function () use ($request, $work, $attributes, $deletedImages, $newImages) {
            $work->update($attributes);
            $work->tags()->sync($request->validated('tags', []));
            $work->images()->whereKey($deletedImages->modelKeys())->delete();
            $work->images()->createMany($newImages);
        });

        $unusedPaths = $deletedImages->pluck('file_path');

        if ($oldThumbnailPath && $oldThumbnailPath !== $work->thumbnail_path) {
            $unusedPaths->push($oldThumbnailPath);
        }

        Storage::disk('public')->delete($unusedPaths->all());

        return redirect()->route('admin.works.index')->with('status', '制作アプリを更新しました。');
    }

    /**
     * Remove the work with its thumbnail and gallery image files.
     */
    public function destroy(Work $work): RedirectResponse
    {
        $paths = $work->images()->pluck('file_path')->push($work->thumbnail_path)->filter();

        $work->delete();

        Storage::disk('public')->delete($paths->all());

        return redirect()->route('admin.works.index')->with('status', '制作アプリを削除しました。');
    }

    /**
     * Save uploaded gallery images and get their rows, numbered from the given sort order.
     *
     * @param  array<int, UploadedFile>  $files
     * @return array<int, array{file_path: string, sort_order: int}>
     */
    private function storeImages(array $files, int $firstSortOrder): array
    {
        return collect($files)->values()->map(fn (UploadedFile $file, int $index) => [
            'file_path' => $file->store('works', 'public'),
            'sort_order' => $firstSortOrder + $index,
        ])->all();
    }

    /**
     * Get the choices shown in the work form.
     *
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'categories' => Category::orderBy('id')->get(),
            'tags' => Tag::orderBy('id')->get(),
            'statuses' => PublishStatus::cases(),
        ];
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PublishStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Display a listing of the posts.
     */
    public function index(): View
    {
        $posts = Post::with('category')->latest('published_at')->latest('id')->paginate(20);

        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create(): View
    {
        return view('admin.posts.create', $this->formOptions());
    }

    /**
     * Store a newly created post.
     */
    public function store(PostRequest $request): RedirectResponse
    {
        $attributes = $request->safe()->except(['thumbnail', 'remove_thumbnail', 'tags']);
        $attributes['thumbnail_path'] = $request->file('thumbnail')?->store('posts', 'public');

        DB::transaction(function () use ($request, $attributes) {
            $post = $request->user()->posts()->create($attributes);
            $post->tags()->sync($request->validated('tags', []));
        });

        return redirect()->route('admin.posts.index')->with('status', '記事を登録しました。');
    }

    /**
     * Show the form for editing the post.
     */
    public function edit(Post $post): View
    {
        return view('admin.posts.edit', ['post' => $post->load('tags'), ...$this->formOptions()]);
    }

    /**
     * Update the post. A replaced or removed thumbnail file is deleted.
     */
    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        $attributes = $request->safe()->except(['thumbnail', 'remove_thumbnail', 'tags']);
        $oldThumbnailPath = $post->thumbnail_path;

        if ($request->hasFile('thumbnail')) {
            $attributes['thumbnail_path'] = $request->file('thumbnail')->store('posts', 'public');
        } elseif ($request->boolean('remove_thumbnail')) {
            $attributes['thumbnail_path'] = null;
        }

        DB::transaction(function () use ($request, $post, $attributes) {
            $post->update($attributes);
            $post->tags()->sync($request->validated('tags', []));
        });

        if ($oldThumbnailPath && $oldThumbnailPath !== $post->thumbnail_path) {
            Storage::disk('public')->delete($oldThumbnailPath);
        }

        return redirect()->route('admin.posts.index')->with('status', '記事を更新しました。');
    }

    /**
     * Remove the post and its thumbnail file.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();

        if ($post->thumbnail_path) {
            Storage::disk('public')->delete($post->thumbnail_path);
        }

        return redirect()->route('admin.posts.index')->with('status', '記事を削除しました。');
    }

    /**
     * Get the choices shown in the post form.
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

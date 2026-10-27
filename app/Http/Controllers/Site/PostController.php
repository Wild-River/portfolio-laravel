<?php

namespace App\Http\Controllers\Site;

use App\Enums\PublishStatus;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Display a listing of the published posts.
     */
    public function index(): View
    {
        $posts = Post::published()->with('tags')->latest()->get();

        return view('site.posts.index', compact('posts'));
    }

    /**
     * Display the post. Drafts are not found.
     */
    public function show(Post $post): View
    {
        abort_unless($post->status === PublishStatus::Published, 404);

        return view('site.posts.show', ['post' => $post->load(['category', 'tags'])]);
    }
}

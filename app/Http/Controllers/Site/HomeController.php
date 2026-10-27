<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Work;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the top page with the latest posts and works.
     */
    public function __invoke(): View
    {
        return view('site.home', [
            'posts' => Post::published()->with('category')->latest()->limit(3)->get(),
            'works' => Work::published()->with('category')->latest()->limit(6)->get(),
        ]);
    }
}

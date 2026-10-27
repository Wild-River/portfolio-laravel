<x-site-layout :title="$post->title">
    <x-slot name="header">
        @include('site.partials.banner', ['title' => 'blog', 'tag' => 'p'])
    </x-slot>

    <p class="breadcrumb"><a href="{{ route('home') }}">TOP</a> / <a href="{{ route('posts.index') }}">記事一覧</a> / {{ $post->title }}</p>

    <article class="article">
        <header class="article-header">
            <h1 class="article-title">{{ $post->title }}</h1>
            <div class="article-meta">
                @if ($post->category)
                    <span class="post-list-item__badge">{{ $post->category->name }}</span>
                @endif
                <span class="article-meta__date">
                    <i class="fa-regular fa-clock"></i>
                    {{ $post->published_at->format('Y.m.d') }}
                </span>
            </div>
        </header>

        @if ($post->thumbnail_url)
            <img class="article-hero" src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}">
        @endif

        <div class="article-body">{{ App\Support\AutoLink::html($post->body) }}</div>

        @if ($post->tags->isNotEmpty())
            <div class="article-tags">
                @foreach ($post->tags as $tag)
                    <span class="tag-chip">#{{ $tag->name }}</span>
                @endforeach
            </div>
        @endif

        <div>
            <a href="{{ route('posts.index') }}" class="article-back-link"><i class="fa-solid fa-arrow-left"></i> 戻る</a>
        </div>
    </article>
</x-site-layout>

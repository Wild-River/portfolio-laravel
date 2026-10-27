<x-site-layout title="記事一覧">
    <x-slot name="header">
        @include('site.partials.banner', ['title' => 'blog'])
    </x-slot>

    <p class="breadcrumb"><a href="{{ route('home') }}">TOP</a> / 記事一覧</p>

    <div class="blog-list">
        @foreach ($posts as $post)
            <a class="blog-card" href="{{ route('posts.show', $post) }}">
                @if ($post->thumbnail_url)
                    <span class="thumb-frame">
                        <img class="blog-card__thumb" src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}">
                    </span>
                @endif
                <div class="blog-card__body">
                    <h2 class="blog-card__title">{{ $post->title }}</h2>
                    @if ($post->tags->isNotEmpty())
                        <div class="blog-card__tags">
                            @foreach ($post->tags as $tag)
                                <span class="tag-chip">#{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endif
                    <span class="blog-card__meta">
                        <i class="fa-regular fa-clock"></i>
                        {{ $post->created_at->format('Y.m.d') }}
                    </span>
                </div>
            </a>
        @endforeach
    </div>
</x-site-layout>

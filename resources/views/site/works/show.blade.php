<x-site-layout :title="$work->title">
    <x-slot name="header">
        @include('site.partials.banner', ['title' => 'works', 'tag' => 'p'])
    </x-slot>

    <p class="breadcrumb"><a href="{{ route('home') }}">TOP</a> / <a href="{{ route('works.index') }}">制作アプリ一覧</a> / {{ $work->title }}</p>

    <article class="article">
        <header class="article-header">
            <h1 class="article-title">{{ $work->title }}</h1>
            <div class="article-meta">
                @if ($work->category)
                    <span class="post-list-item__badge">{{ $work->category->name }}</span>
                @endif
                <span class="article-meta__date">
                    <i class="fa-regular fa-clock"></i>
                    {{ $work->published_at->format('Y.m.d') }}
                </span>
            </div>
        </header>

        @if ($work->thumbnail_url)
            <img class="article-hero" src="{{ $work->thumbnail_url }}" alt="{{ $work->title }}">
        @endif

        <div class="article-body">{{ App\Support\AutoLink::html($work->description) }}</div>

        @if ($work->images->isNotEmpty())
            <div class="article-gallery">
                @foreach ($work->images as $image)
                    <span class="article-gallery__frame">
                        <img src="{{ $image->url }}" alt="">
                    </span>
                @endforeach
            </div>

            <div class="lightbox" id="lightbox" hidden>
                <button type="button" class="lightbox__close" aria-label="閉じる">&times;</button>
                <img class="lightbox__img" src="" alt="">
            </div>
        @endif

        @if ($work->tags->isNotEmpty())
            <div class="article-tags">
                @foreach ($work->tags as $tag)
                    <span class="tag-chip">#{{ $tag->name }}</span>
                @endforeach
            </div>
        @endif

        <div>
            <a href="{{ route('works.index') }}" class="article-back-link"><i class="fa-solid fa-arrow-left"></i> 戻る</a>
        </div>
    </article>
</x-site-layout>

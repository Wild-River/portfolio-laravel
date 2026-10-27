<x-site-layout>
    <x-slot name="header">
        <section class="hero">
            @include('site.partials.nav')

            <h1 class="hero__title">Rumi Arakawa</h1>
            <p class="hero__subtitle">2026.5月〜 G'sでの学習ログと制作アプリ</p>
        </section>
    </x-slot>

    <div class="hero-overlap">
        <h2>新着記事</h2>
        <div class="post-list">
            @foreach ($posts as $post)
                <a class="post-list-item" href="{{ route('posts.show', $post) }}">
                    <span class="post-list-item__date">{{ $post->created_at->format('Y.m.d') }}</span>
                    @if ($post->category)
                        <span class="post-list-item__badge">{{ $post->category->name }}</span>
                    @endif
                    <span class="post-list-item__title">{{ $post->title }}</span>
                    <i class="fa-solid fa-chevron-right post-list-item__arrow"></i>
                </a>
            @endforeach
        </div>
    </div>

    <h2>制作アプリ</h2>
    <div class="portfolio-grid portfolio-grid--home">
        @foreach ($works as $work)
            <a class="portfolio-card" href="{{ route('works.show', $work) }}">
                @if ($work->thumbnail_url)
                    <span class="thumb-frame">
                        <img class="portfolio-card__thumb" src="{{ $work->thumbnail_url }}" alt="{{ $work->title }}">
                    </span>
                @endif
                <div class="portfolio-card__body">
                    <p class="portfolio-card__title">{{ $work->title }}</p>
                    <p class="portfolio-card__category">{{ $work->category?->name }}</p>
                </div>
            </a>
        @endforeach
    </div>

    <div class="section-more">
        <a class="btn-primary" href="{{ route('works.index') }}">制作アプリ一覧を見る</a>
    </div>
</x-site-layout>

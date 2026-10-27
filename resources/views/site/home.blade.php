<x-site-layout>
    <x-slot name="header">
        <section class="hero">
            @include('site.partials.nav')

            <h1 class="hero__title">Rumi Arakawa</h1>
            <p class="hero__subtitle">2026.5月〜 プログラミングの学習ログと制作アプリ</p>
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

    <div class="github-langs-section">
        <h2>GitHubリポジトリでの使用言語</h2>
        @if ($languages->isNotEmpty())
            @php
                $colors = ['#67b5b0', '#549691', '#8a9a95', '#c9c2a8', '#b4d9d6', '#3d5c58', '#d9d4c2'];
            @endphp
            <div class="github-langs">
                <div class="github-langs__bar">
                    @foreach ($languages as $language)
                        <span class="github-langs__segment" style="width: {{ $language['percent'] }}%; background: {{ $colors[$loop->index % count($colors)] }};"></span>
                    @endforeach
                </div>

                <ul class="github-langs__list">
                    @foreach ($languages as $language)
                        <li class="github-langs__item">
                            <span class="github-langs__swatch" style="background: {{ $colors[$loop->index % count($colors)] }};"></span>
                            <span class="github-langs__name">{{ $language['language'] }}</span>
                            <span class="github-langs__percent">{{ round($language['percent'], 1) }}%</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</x-site-layout>

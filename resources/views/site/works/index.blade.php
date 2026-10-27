<x-site-layout title="制作アプリ一覧">
    <x-slot name="header">
        @include('site.partials.banner', ['title' => 'works'])
    </x-slot>

    <p class="breadcrumb"><a href="{{ route('home') }}">TOP</a> / 制作アプリ一覧</p>

    <div class="portfolio-grid">
        @foreach ($works as $work)
            <a class="portfolio-card" href="{{ route('works.show', $work) }}">
                @if ($work->thumbnail_url)
                    <span class="thumb-frame">
                        <img class="portfolio-card__thumb" src="{{ $work->thumbnail_url }}" alt="{{ $work->title }}">
                    </span>
                @endif
                <div class="portfolio-card__body">
                    <div class="portfolio-card__heading">
                        <h2 class="portfolio-card__title">{{ $work->title }}</h2>
                        @if ($work->tags->isNotEmpty())
                            <div class="portfolio-card__tags">
                                @foreach ($work->tags as $tag)
                                    <span class="tag-chip">#{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <p class="portfolio-card__category">{{ $work->category?->name }}</p>
                </div>
            </a>
        @endforeach
    </div>
</x-site-layout>

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

        @include('site.partials.work-links')

        @if ($work->thumbnail_url)
            <img class="article-hero" src="{{ $work->thumbnail_url }}" alt="{{ $work->title }}">
        @endif

        <div class="article-body">{{ App\Support\ArticleBody::html($work->description) }}</div>

        @if ($work->technologies->isNotEmpty())
            <section class="article-tech">
                <h2 class="article-tech__title">使用技術</h2>
                @foreach ($work->technologies->groupBy(fn ($technology) => $technology->category->label()) as $label => $group)
                    <div class="article-tech__group">
                        <h3 class="article-tech__label">{{ $label }}</h3>
                        <ul class="tech-grid">
                            @foreach ($group as $technology)
                                <li class="tech-tile">
                                    <span class="tech-tile__frame">
                                        @if ($technology->has_icon_file)
                                            <img class="tech-tile__image" src="{{ $technology->icon_url }}" alt="">
                                        @elseif ($technology->icon_url)
                                            <span class="tech-tile__icon" style="--icon: url('{{ $technology->icon_url }}'); --icon-color: {{ $technology->color ?? 'currentColor' }}"></span>
                                        @else
                                            <span class="tech-tile__initial">{{ mb_substr($technology->name, 0, 1) }}</span>
                                        @endif
                                    </span>
                                    <span class="tech-tile__name">{{ $technology->name }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </section>
        @endif

        @if ($work->images->isNotEmpty())
            <div class="article-gallery">
                @foreach ($work->images as $image)
                    <span class="article-gallery__frame">
                        <img src="{{ $image->url }}" alt="">
                    </span>
                @endforeach
            </div>

            <div @class(['lightbox', 'lightbox--slidable' => $work->images->count() > 1]) id="lightbox" hidden>
                <button type="button" class="lightbox__close" aria-label="閉じる">&times;</button>
                @if ($work->images->count() > 1)
                    <button type="button" class="lightbox__nav lightbox__nav--prev" aria-label="前の画像"><i class="fa-solid fa-chevron-left"></i></button>
                    <button type="button" class="lightbox__nav lightbox__nav--next" aria-label="次の画像"><i class="fa-solid fa-chevron-right"></i></button>
                    <p class="lightbox__counter"></p>
                @endif
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

{{-- 制作アプリのカード（トップと制作アプリ一覧で共通） --}}
<a class="portfolio-card" href="{{ route('works.show', $work) }}">
    @if ($work->thumbnail_url)
        <span class="thumb-frame">
            <img class="portfolio-card__thumb" src="{{ $work->thumbnail_url }}" alt="{{ $work->title }}">
        </span>
    @endif
    <div class="portfolio-card__body">
        <div @class(['portfolio-card__heading', 'portfolio-card__heading--stacked' => $showDate ?? false])>
            <h2 class="portfolio-card__title">{{ $work->title }}</h2>
            @if ($showDate ?? false)
                <span class="portfolio-card__meta">
                    <i class="fa-regular fa-clock"></i>
                    {{ $work->published_at->format('Y.m.d') }}
                </span>
            @endif
            @if ($work->tags->isNotEmpty())
                <div class="portfolio-card__tags">
                    @foreach ($work->tags as $tag)
                        <span class="tag-chip">#{{ $tag->name }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</a>

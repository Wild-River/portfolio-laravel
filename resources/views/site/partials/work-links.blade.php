{{-- 制作アプリの確認用URL・GitHub へのリンク（詳細ページのアイコン付きボタン） --}}
@if ($work->demo_url || $work->github_url)
    <div class="work-links">
        @if ($work->demo_url)
            <a class="work-link work-link--demo" href="{{ $work->demo_url }}" target="_blank" rel="noopener noreferrer">
                <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                <span>アプリを見る</span>
            </a>
        @endif
        @if ($work->github_url)
            <a class="work-link work-link--github" href="{{ $work->github_url }}" target="_blank" rel="noopener noreferrer">
                <i class="fa-brands fa-github" aria-hidden="true"></i>
                <span>コードを見る</span>
            </a>
        @endif
    </div>
@endif

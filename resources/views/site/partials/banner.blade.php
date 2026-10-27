{{-- 一覧ページは h1、詳細ページは記事タイトルを h1 にするため p で出す --}}
<div class="page-banner">
    @include('site.partials.nav')

    <{{ $tag ?? 'h1' }} class="page-banner__title">{{ $title }}</{{ $tag ?? 'h1' }}>
</div>

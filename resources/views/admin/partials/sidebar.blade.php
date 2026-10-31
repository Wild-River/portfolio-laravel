@php
    // [一覧のルート名, 一覧の表示名, 登録の表示名, アイコン]
    // 編集ページには直接のリンクが無いので、一覧側のリンクをアクティブにする
    $sidebarGroups = [
        ['admin.works', '制作アプリ管理', '制作アプリ登録', 'fa-briefcase'],
        ['admin.posts', '記事管理', '記事登録', 'fa-newspaper'],
        ['admin.categories', 'カテゴリ管理', 'カテゴリ登録', 'fa-folder'],
        ['admin.tags', 'タグ管理', 'タグ登録', 'fa-tag'],
        ['admin.technologies', '使用技術管理', '使用技術登録', 'fa-code'],
    ];
@endphp

<button type="button" class="menu-toggle" aria-label="メニューを開く" aria-expanded="false" aria-controls="sidebar">
    <span class="menu-toggle__bar"></span>
    <span class="menu-toggle__bar"></span>
    <span class="menu-toggle__bar"></span>
</button>

<aside class="sidebar" id="sidebar">
    <div class="sidebar__brand">
        <a href="{{ route('dashboard') }}">ブログ管理システム</a>
    </div>
    <a href="{{ route('home') }}" class="sidebar__link" target="_blank" rel="noopener noreferrer">
        <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
        <span>サイトを表示</span>
    </a>

    <nav class="sidebar__nav">
        @foreach ($sidebarGroups as [$route, $indexLabel, $createLabel, $icon])
            <div class="sidebar__group">
                <a href="{{ route("{$route}.index") }}" @class(['sidebar__link', 'is-active' => request()->routeIs("{$route}.index", "{$route}.edit")])>
                    <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>
                    <span>{{ $indexLabel }}</span>
                </a>
                <a href="{{ route("{$route}.create") }}" @class(['sidebar__link', 'sidebar__link--sub', 'is-active' => request()->routeIs("{$route}.create")])>
                    <i class="fa-solid fa-plus" aria-hidden="true"></i>
                    <span>{{ $createLabel }}</span>
                </a>
            </div>
        @endforeach
    </nav>

    <div class="sidebar__footer">
        <a href="{{ route('profile.edit') }}" @class(['sidebar__link', 'is-active' => request()->routeIs('profile.edit')])>
            <i class="fa-solid fa-user" aria-hidden="true"></i>
            <span>プロフィール</span>
        </a>
        <form method="post" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar__link">
                <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                <span>ログアウト</span>
            </button>
        </form>
    </div>
</aside>
<div class="sidebar-overlay"></div>

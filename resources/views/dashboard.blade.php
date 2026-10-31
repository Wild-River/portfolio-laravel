<x-admin-layout title="ダッシュボード">
    <h1 class="page-title">ダッシュボード</h1>

    <div class="card-grid">
        <a href="{{ route('admin.works.index') }}" class="menu-card">
            <i class="fa-solid fa-briefcase" aria-hidden="true"></i>
            <span>制作アプリ管理</span>
        </a>
        <a href="{{ route('admin.posts.index') }}" class="menu-card">
            <i class="fa-solid fa-newspaper" aria-hidden="true"></i>
            <span>記事管理</span>
        </a>
        <a href="{{ route('admin.categories.index') }}" class="menu-card">
            <i class="fa-solid fa-folder" aria-hidden="true"></i>
            <span>カテゴリ管理</span>
        </a>
        <a href="{{ route('admin.tags.index') }}" class="menu-card">
            <i class="fa-solid fa-tag" aria-hidden="true"></i>
            <span>タグ管理</span>
        </a>
        <a href="{{ route('admin.technologies.index') }}" class="menu-card">
            <i class="fa-solid fa-code" aria-hidden="true"></i>
            <span>使用技術管理</span>
        </a>
    </div>
</x-admin-layout>

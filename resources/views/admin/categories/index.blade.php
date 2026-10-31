<x-admin-layout title="カテゴリ一覧">
    <div class="page-head">
        <h1>カテゴリ一覧</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn-primary">+ 新規登録</a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>カテゴリ名</th>
                    <th>スラッグ</th>
                    <th>記事数</th>
                    <th>制作アプリ数</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr class="row-link" data-href="{{ route('admin.categories.edit', $category) }}">
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->slug }}</td>
                        <td>{{ $category->posts_count }}</td>
                        <td>{{ $category->works_count }}</td>
                        <td class="actions-cell">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="icon-btn" title="編集" aria-label="「{{ $category->name }}」を編集">
                                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="table-empty">カテゴリはまだありません。</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>

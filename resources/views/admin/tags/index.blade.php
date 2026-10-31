<x-admin-layout title="タグ一覧">
    <div class="page-head">
        <h1>タグ一覧</h1>
        <a href="{{ route('admin.tags.create') }}" class="btn-primary">+ 新規登録</a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>タグ名</th>
                    <th>スラッグ</th>
                    <th>記事数</th>
                    <th>制作アプリ数</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tags as $tag)
                    <tr class="row-link" data-href="{{ route('admin.tags.edit', $tag) }}">
                        <td>{{ $tag->id }}</td>
                        <td>{{ $tag->name }}</td>
                        <td>{{ $tag->slug }}</td>
                        <td>{{ $tag->posts_count }}</td>
                        <td>{{ $tag->works_count }}</td>
                        <td class="actions-cell">
                            <a href="{{ route('admin.tags.edit', $tag) }}" class="icon-btn" title="編集" aria-label="「{{ $tag->name }}」を編集">
                                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="table-empty">タグはまだありません。</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>

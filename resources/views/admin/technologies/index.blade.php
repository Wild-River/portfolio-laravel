<x-admin-layout title="使用技術一覧">
    <div class="page-head">
        <h1>使用技術一覧</h1>
        <a href="{{ route('admin.technologies.create') }}" class="btn-primary">+ 新規登録</a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>アイコン</th>
                    <th>技術名</th>
                    <th>分類</th>
                    <th>表示順</th>
                    <th>制作アプリ数</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($technologies as $technology)
                    <tr class="row-link" data-href="{{ route('admin.technologies.edit', $technology) }}">
                        <td>
                            @if ($technology->icon_url)
                                <img class="table-icon" src="{{ $technology->icon_url }}" alt="">
                            @else
                                <span class="table-excerpt">なし</span>
                            @endif
                        </td>
                        <td>{{ $technology->name }}</td>
                        <td>{{ $technology->category->label() }}</td>
                        <td>{{ $technology->sort_order }}</td>
                        <td>{{ $technology->works_count }}</td>
                        <td class="actions-cell">
                            <a href="{{ route('admin.technologies.edit', $technology) }}" class="icon-btn" title="編集" aria-label="「{{ $technology->name }}」を編集">
                                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="table-empty">使用技術はまだありません。</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>

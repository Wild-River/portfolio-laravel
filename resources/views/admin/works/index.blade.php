<x-admin-layout title="制作アプリ一覧">
    <div class="page-head">
        <h1>制作アプリ一覧</h1>
        <a href="{{ route('admin.works.create') }}" class="btn-primary">+ 新規登録</a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>サムネイル</th>
                    <th>ID</th>
                    <th>タイトル</th>
                    <th>説明文</th>
                    <th>スラッグ</th>
                    <th>ステータス</th>
                    <th>公開日</th>
                    <th>カテゴリ</th>
                    <th>ギャラリー</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($works as $work)
                    <tr class="row-link" data-href="{{ route('admin.works.edit', $work) }}">
                        <td>
                            @if ($work->thumbnail_url)
                                <img class="table-thumb" src="{{ $work->thumbnail_url }}" alt="">
                            @endif
                        </td>
                        <td>{{ $work->id }}</td>
                        <td>{{ $work->title }}</td>
                        <td class="table-excerpt">{{ Str::limit($work->description, 20) }}</td>
                        <td>{{ $work->slug }}</td>
                        <td>{{ $work->status->label() }}</td>
                        <td class="table-nowrap">{{ $work->published_at->format('Y.m.d') }}</td>
                        <td>{{ $work->category?->name }}</td>
                        <td class="table-nowrap">{{ $work->images_count }}枚</td>
                        <td class="actions-cell">
                            <a href="{{ route('admin.works.edit', $work) }}" class="icon-btn" title="編集" aria-label="「{{ $work->title }}」を編集">
                                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="table-empty">制作アプリはまだありません。</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $works->links('pagination::bootstrap-3') }}
</x-admin-layout>

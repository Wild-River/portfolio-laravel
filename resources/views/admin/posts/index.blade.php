<x-admin-layout title="記事一覧">
    <div class="page-head">
        <h1>記事一覧</h1>
        <a href="{{ route('admin.posts.create') }}" class="btn-primary">+ 新規登録</a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>サムネイル</th>
                    <th>ID</th>
                    <th>タイトル</th>
                    <th>本文</th>
                    <th>スラッグ</th>
                    <th>ステータス</th>
                    <th>公開日</th>
                    <th>カテゴリ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $post)
                    <tr class="row-link" data-href="{{ route('admin.posts.edit', $post) }}">
                        <td>
                            @if ($post->thumbnail_url)
                                <img class="table-thumb" src="{{ $post->thumbnail_url }}" alt="">
                            @endif
                        </td>
                        <td>{{ $post->id }}</td>
                        <td>{{ $post->title }}</td>
                        <td class="table-excerpt">{{ Str::limit($post->body, 20) }}</td>
                        <td>{{ $post->slug }}</td>
                        <td>{{ $post->status->label() }}</td>
                        <td class="table-nowrap">{{ $post->published_at->format('Y.m.d') }}</td>
                        <td>{{ $post->category?->name }}</td>
                        <td class="actions-cell">
                            <a href="{{ route('admin.posts.edit', $post) }}" class="icon-btn" title="編集" aria-label="「{{ $post->title }}」を編集">
                                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="table-empty">記事はまだありません。</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $posts->links('pagination::bootstrap-3') }}
</x-admin-layout>

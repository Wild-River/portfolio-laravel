<x-admin-layout title="記事編集">
    <h1 class="page-title">記事編集</h1>

    <div class="card">
        <form method="post" action="{{ route('admin.posts.update', $post) }}" enctype="multipart/form-data" id="edit-form">
            @csrf
            @method('put')

            @include('admin.posts.partials.form')
        </form>

        {{-- フォームは入れ子にできないので、更新ボタンは form 属性で上のフォームに結びつける --}}
        <div class="form-actions">
            <button type="submit" form="edit-form" class="submit-btn">更新する</button>
            <form method="post" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('この記事を削除しますか？');">
                @csrf
                @method('delete')

                <button type="submit" class="delete-btn">削除する</button>
            </form>
            @if ($post->status === \App\Enums\PublishStatus::Published)
                <a href="{{ route('posts.show', $post) }}" class="back-btn" target="_blank" rel="noopener noreferrer">ページを見る</a>
            @endif
            <a href="{{ route('admin.posts.index') }}" class="back-btn">戻る</a>
        </div>
    </div>
</x-admin-layout>

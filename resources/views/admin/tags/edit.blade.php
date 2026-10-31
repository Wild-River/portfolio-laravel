<x-admin-layout title="タグ編集">
    <h1 class="page-title">タグ編集</h1>

    <div class="card">
        <form method="post" action="{{ route('admin.tags.update', $tag) }}" id="edit-form">
            @csrf
            @method('put')

            @include('admin.tags.partials.form')
        </form>

        {{-- フォームは入れ子にできないので、更新ボタンは form 属性で上のフォームに結びつける --}}
        <div class="form-actions">
            <button type="submit" form="edit-form" class="submit-btn">更新する</button>
            <form method="post" action="{{ route('admin.tags.destroy', $tag) }}" onsubmit="return confirm('このタグを削除しますか？\n記事と制作アプリからも外れます。');">
                @csrf
                @method('delete')

                <button type="submit" class="delete-btn">削除する</button>
            </form>
            <a href="{{ route('admin.tags.index') }}" class="back-btn">戻る</a>
        </div>
    </div>
</x-admin-layout>

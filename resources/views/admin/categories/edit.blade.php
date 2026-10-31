<x-admin-layout title="カテゴリ編集">
    <h1 class="page-title">カテゴリ編集</h1>

    <div class="card">
        <form method="post" action="{{ route('admin.categories.update', $category) }}" id="edit-form">
            @csrf
            @method('put')

            @include('admin.categories.partials.form')
        </form>

        {{-- フォームは入れ子にできないので、更新ボタンは form 属性で上のフォームに結びつける --}}
        <div class="form-actions">
            <button type="submit" form="edit-form" class="submit-btn">更新する</button>
            <form method="post" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('このカテゴリを削除しますか？\n記事と制作アプリは「カテゴリなし」になります。');">
                @csrf
                @method('delete')

                <button type="submit" class="delete-btn">削除する</button>
            </form>
            <a href="{{ route('admin.categories.index') }}" class="back-btn">戻る</a>
        </div>
    </div>
</x-admin-layout>

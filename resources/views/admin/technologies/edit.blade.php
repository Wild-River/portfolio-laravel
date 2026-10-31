<x-admin-layout title="使用技術編集">
    <h1 class="page-title">使用技術編集</h1>

    <div class="card">
        <form method="post" action="{{ route('admin.technologies.update', $technology) }}" id="edit-form">
            @csrf
            @method('put')

            @include('admin.technologies.partials.form')
        </form>

        {{-- フォームは入れ子にできないので、更新ボタンは form 属性で上のフォームに結びつける --}}
        <div class="form-actions">
            <button type="submit" form="edit-form" class="submit-btn">更新する</button>
            <form method="post" action="{{ route('admin.technologies.destroy', $technology) }}" onsubmit="return confirm('この使用技術を削除しますか？\n制作アプリからも外れます。');">
                @csrf
                @method('delete')

                <button type="submit" class="delete-btn">削除する</button>
            </form>
            <a href="{{ route('admin.technologies.index') }}" class="back-btn">戻る</a>
        </div>
    </div>
</x-admin-layout>

<x-admin-layout title="制作アプリ編集">
    <h1 class="page-title">制作アプリ編集</h1>

    <div class="card">
        <form method="post" action="{{ route('admin.works.update', $work) }}" enctype="multipart/form-data" id="edit-form">
            @csrf
            @method('put')

            @include('admin.works.partials.form')
        </form>

        {{-- フォームは入れ子にできないので、更新ボタンは form 属性で上のフォームに結びつける --}}
        <div class="form-actions">
            <button type="submit" form="edit-form" class="submit-btn">更新する</button>
            <form method="post" action="{{ route('admin.works.destroy', $work) }}" onsubmit="return confirm('この制作アプリを削除しますか？');">
                @csrf
                @method('delete')

                <button type="submit" class="delete-btn">削除する</button>
            </form>
            @if ($work->status === \App\Enums\PublishStatus::Published)
                <a href="{{ route('works.show', $work) }}" class="back-btn" target="_blank" rel="noopener noreferrer">ページを見る</a>
            @endif
            <a href="{{ route('admin.works.index') }}" class="back-btn">戻る</a>
        </div>
    </div>
</x-admin-layout>

<x-admin-layout title="タグ登録">
    <h1 class="page-title">タグ登録</h1>

    <div class="card">
        <form method="post" action="{{ route('admin.tags.store') }}">
            @csrf

            @include('admin.tags.partials.form', ['tag' => null])

            <div class="form-actions">
                <button type="submit" class="submit-btn">登録する</button>
                <a href="{{ route('admin.tags.index') }}" class="back-btn">戻る</a>
            </div>
        </form>
    </div>
</x-admin-layout>

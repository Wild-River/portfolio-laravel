<x-admin-layout title="カテゴリ登録">
    <h1 class="page-title">カテゴリ登録</h1>

    <div class="card">
        <form method="post" action="{{ route('admin.categories.store') }}">
            @csrf

            @include('admin.categories.partials.form', ['category' => null])

            <div class="form-actions">
                <button type="submit" class="submit-btn">登録する</button>
                <a href="{{ route('admin.categories.index') }}" class="back-btn">戻る</a>
            </div>
        </form>
    </div>
</x-admin-layout>

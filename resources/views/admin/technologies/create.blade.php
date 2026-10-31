<x-admin-layout title="使用技術登録">
    <h1 class="page-title">使用技術登録</h1>

    <div class="card">
        <form method="post" action="{{ route('admin.technologies.store') }}">
            @csrf

            @include('admin.technologies.partials.form', ['technology' => null])

            <div class="form-actions">
                <button type="submit" class="submit-btn">登録する</button>
                <a href="{{ route('admin.technologies.index') }}" class="back-btn">戻る</a>
            </div>
        </form>
    </div>
</x-admin-layout>

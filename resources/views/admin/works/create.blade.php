<x-admin-layout title="制作アプリ登録">
    <h1 class="page-title">制作アプリ登録</h1>

    <div class="card">
        <form method="post" action="{{ route('admin.works.store') }}" enctype="multipart/form-data">
            @csrf

            @include('admin.works.partials.form', ['work' => null])

            <div class="form-actions">
                <button type="submit" class="submit-btn">登録する</button>
                <a href="{{ route('admin.works.index') }}" class="back-btn">戻る</a>
            </div>
        </form>
    </div>
</x-admin-layout>

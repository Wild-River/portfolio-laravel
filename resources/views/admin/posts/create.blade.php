<x-admin-layout title="記事登録">
    <h1 class="page-title">記事登録</h1>

    <div class="card">
        <form method="post" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data">
            @csrf

            @include('admin.posts.partials.form', ['post' => null])

            <div class="form-actions">
                <button type="submit" class="submit-btn">登録する</button>
                <a href="{{ route('admin.posts.index') }}" class="back-btn">戻る</a>
            </div>
        </form>
    </div>
</x-admin-layout>

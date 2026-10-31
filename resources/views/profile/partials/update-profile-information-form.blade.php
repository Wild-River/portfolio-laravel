<section>
    <h2 class="card-title">プロフィール情報</h2>
    <p class="form-hint">名前とメールアドレスを変更できます。</p>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="form-group">
            <label for="name" class="form-label">名前</label>
            <input id="name" name="name" type="text" class="form-input form-input--narrow" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            <x-admin.form-error :messages="$errors->get('name')" />
        </div>

        <div class="form-group">
            <label for="email" class="form-label">メールアドレス</label>
            <input id="email" name="email" type="email" class="form-input form-input--narrow" value="{{ old('email', $user->email) }}" required autocomplete="username">
            <x-admin.form-error :messages="$errors->get('email')" />
        </div>

        <div class="form-actions">
            <button type="submit" class="submit-btn">保存する</button>
        </div>
    </form>
</section>

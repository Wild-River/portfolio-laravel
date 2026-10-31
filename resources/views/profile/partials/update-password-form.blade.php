<section>
    <h2 class="card-title">パスワードの変更</h2>
    <p class="form-hint">推測されにくい、長いパスワードにしてください。</p>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="form-group">
            <label for="update_password_current_password" class="form-label">現在のパスワード</label>
            <input id="update_password_current_password" name="current_password" type="password" class="form-input form-input--narrow" autocomplete="current-password">
            <x-admin.form-error :messages="$errors->updatePassword->get('current_password')" />
        </div>

        <div class="form-group">
            <label for="update_password_password" class="form-label">新しいパスワード</label>
            <input id="update_password_password" name="password" type="password" class="form-input form-input--narrow" autocomplete="new-password">
            <x-admin.form-error :messages="$errors->updatePassword->get('password')" />
        </div>

        <div class="form-group">
            <label for="update_password_password_confirmation" class="form-label">新しいパスワード（確認用）</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-input form-input--narrow" autocomplete="new-password">
            <x-admin.form-error :messages="$errors->updatePassword->get('password_confirmation')" />
        </div>

        <div class="form-actions">
            <button type="submit" class="submit-btn">変更する</button>
        </div>
    </form>
</section>

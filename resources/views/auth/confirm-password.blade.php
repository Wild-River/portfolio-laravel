<x-guest-layout title="パスワードの確認">
    <h1>パスワードの確認</h1>

    <form method="POST" action="{{ route('password.confirm') }}" class="login-form">
        @csrf

        <p class="form-hint">この先は保護されたページです。続けるにはパスワードを入力してください。</p>

        <div class="form-group">
            <label for="password" class="form-label">パスワード</label>
            <input id="password" type="password" name="password" class="form-input" required autocomplete="current-password">
            <x-admin.form-error :messages="$errors->get('password')" />
        </div>

        <button type="submit" class="submit-btn">確認する</button>
    </form>
</x-guest-layout>

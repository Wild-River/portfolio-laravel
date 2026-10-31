<x-guest-layout title="ログイン">
    <h1>ブログ管理システム</h1>

    <form method="POST" action="{{ route('login') }}" class="login-form">
        @csrf

        @if (session('status'))
            <p class="flash">{{ session('status') }}</p>
        @endif

        <div class="form-group">
            <label for="email" class="form-label">メールアドレス</label>
            <input id="email" type="email" name="email" class="form-input" value="{{ old('email') }}" required autofocus autocomplete="username">
            <x-admin.form-error :messages="$errors->get('email')" />
        </div>

        <div class="form-group">
            <label for="password" class="form-label">パスワード</label>
            <input id="password" type="password" name="password" class="form-input" required autocomplete="current-password">
            <x-admin.form-error :messages="$errors->get('password')" />
        </div>

        <label for="remember_me" class="form-check">
            <input id="remember_me" type="checkbox" name="remember">
            ログイン状態を保持する
        </label>

        <button type="submit" class="submit-btn">ログイン</button>
    </form>
</x-guest-layout>

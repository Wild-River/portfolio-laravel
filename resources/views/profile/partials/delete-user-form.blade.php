<section>
    <h2 class="card-title">アカウントの削除</h2>
    <p class="form-hint">削除すると元に戻せません。管理者は1人だけなので、削除すると管理画面にログインできなくなります。</p>

    <form method="post" action="{{ route('profile.destroy') }}" onsubmit="return confirm('アカウントを削除しますか？\nこの操作は元に戻せません。');">
        @csrf
        @method('delete')

        <div class="form-group">
            <label for="delete_user_password" class="form-label">パスワード</label>
            <input id="delete_user_password" name="password" type="password" class="form-input form-input--narrow" autocomplete="current-password">
            <p class="form-hint">確認のため、パスワードを入力してください。</p>
            <x-admin.form-error :messages="$errors->userDeletion->get('password')" />
        </div>

        <div class="form-actions">
            <button type="submit" class="delete-btn">アカウントを削除する</button>
        </div>
    </form>
</section>

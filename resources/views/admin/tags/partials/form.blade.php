<div class="form-group">
    <label for="name" class="form-label">タグ名</label>
    <input id="name" name="name" type="text" class="form-input" value="{{ old('name', $tag?->name) }}" required autofocus>
    <x-admin.form-error :messages="$errors->get('name')" />
</div>

<div class="form-group">
    <label for="slug" class="form-label">スラッグ</label>
    <input id="slug" name="slug" type="text" class="form-input" value="{{ old('slug', $tag?->slug) }}" required>
    <p class="form-hint">URL に使います。英小文字・数字・ハイフンで入力してください（例：my-first-post）。</p>
    <x-admin.form-error :messages="$errors->get('slug')" />
</div>

@php
    $selectedTagIds = array_map('intval', old('tags', $post?->tags->pluck('id')->all() ?? []));
@endphp

<div class="form-group">
    <label for="title" class="form-label">タイトル</label>
    <input id="title" name="title" type="text" class="form-input" value="{{ old('title', $post?->title) }}" required autofocus>
    <x-admin.form-error :messages="$errors->get('title')" />
</div>

<div class="form-group">
    <label for="body" class="form-label">本文</label>
    <textarea id="body" name="body" rows="12" class="form-input" required>{{ old('body', $post?->body) }}</textarea>
    <x-admin.form-error :messages="$errors->get('body')" />
</div>

<div class="form-group">
    <label for="thumbnail" class="form-label">サムネイル画像</label>
    @if ($post?->thumbnail_url)
        <img src="{{ $post->thumbnail_url }}" alt="現在のサムネイル" class="form-preview">
        <label class="form-check">
            <input type="checkbox" name="remove_thumbnail" value="1" @checked(old('remove_thumbnail'))>
            サムネイルを削除する
        </label>
    @endif
    <input id="thumbnail" name="thumbnail" type="file" accept=".jpg,.jpeg,.png,.gif,.webp" class="form-input">
    <p class="form-hint">jpg・png・gif・webp、2MB まで。{{ $post?->thumbnail_url ? '選ぶと現在の画像と差し替えます。' : '' }}</p>
    <x-admin.form-error :messages="$errors->get('thumbnail')" />
</div>

<div class="form-group">
    <label for="category_id" class="form-label">カテゴリ</label>
    <select id="category_id" name="category_id" class="form-input form-input--narrow">
        <option value="">カテゴリなし</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected((string) old('category_id', $post?->category_id) === (string) $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
    <x-admin.form-error :messages="$errors->get('category_id')" />
</div>

<fieldset class="form-group">
    <legend class="form-label">タグ</legend>
    <div class="tag-list">
        @forelse ($tags as $tag)
            <label>
                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" @checked(in_array($tag->id, $selectedTagIds, true))>
                {{ $tag->name }}
            </label>
        @empty
            <p class="form-hint">タグはまだありません。</p>
        @endforelse
    </div>
    <x-admin.form-error :messages="Arr::flatten([$errors->get('tags'), $errors->get('tags.*')])" />
</fieldset>

<div class="form-group">
    <label for="slug" class="form-label">スラッグ</label>
    <input id="slug" name="slug" type="text" class="form-input form-input--narrow" value="{{ old('slug', $post?->slug) }}" required>
    <p class="form-hint">URL に使います。英小文字・数字・ハイフンで入力してください（例：my-first-post）。</p>
    <x-admin.form-error :messages="$errors->get('slug')" />
</div>

<div class="form-group">
    <label for="status" class="form-label">ステータス</label>
    <select id="status" name="status" class="form-input form-input--narrow">
        @foreach ($statuses as $status)
            <option value="{{ $status->value }}" @selected(old('status', $post?->status->value ?? 'draft') === $status->value)>{{ $status->label() }}</option>
        @endforeach
    </select>
    <x-admin.form-error :messages="$errors->get('status')" />
</div>

<div class="form-group">
    <label for="published_at" class="form-label">公開日</label>
    <input id="published_at" name="published_at" type="date" class="form-input form-input--narrow" value="{{ old('published_at', $post?->published_at?->format('Y-m-d') ?? today()->format('Y-m-d')) }}" required>
    <p class="form-hint">公開ページに表示する日付です。並び順にも使います。</p>
    <x-admin.form-error :messages="$errors->get('published_at')" />
</div>

@php
    $selectedTagIds = array_map('intval', old('tags', $work?->tags->pluck('id')->all() ?? []));
    $selectedTechnologyIds = array_map('intval', old('technologies', $work?->technologies->pluck('id')->all() ?? []));
@endphp

<div class="form-group">
    <label for="title" class="form-label">タイトル</label>
    <input id="title" name="title" type="text" class="form-input" value="{{ old('title', $work?->title) }}" required autofocus>
    <x-admin.form-error :messages="$errors->get('title')" />
</div>

<div class="form-group">
    <label for="description" class="form-label">説明文</label>
    <textarea id="description" name="description" rows="12" class="form-input" required>{{ old('description', $work?->description) }}</textarea>
    <x-admin.form-error :messages="$errors->get('description')" />
</div>

<div class="form-group">
    <label for="demo_url" class="form-label">確認用URL</label>
    <input id="demo_url" name="demo_url" type="url" class="form-input" value="{{ old('demo_url', $work?->demo_url) }}" placeholder="https://" required>
    <p class="form-hint">実際にアプリを動かして確認できる URL です（公開ページに「アプリを見る」として表示）。</p>
    <x-admin.form-error :messages="$errors->get('demo_url')" />
</div>

<div class="form-group">
    <label for="github_url" class="form-label">GitHub URL（任意）</label>
    <input id="github_url" name="github_url" type="url" class="form-input" value="{{ old('github_url', $work?->github_url) }}" placeholder="https://github.com/">
    <p class="form-hint">コードのリポジトリの URL です（公開ページに「コードを見る」として表示）。リポジトリが非公開なら空のままにしてください。</p>
    <x-admin.form-error :messages="$errors->get('github_url')" />
</div>

<div class="form-group">
    <label for="thumbnail" class="form-label">サムネイル画像</label>
    @if ($work?->thumbnail_url)
        <img src="{{ $work->thumbnail_url }}" alt="現在のサムネイル" class="form-preview">
        <label class="form-check">
            <input type="checkbox" name="remove_thumbnail" value="1" @checked(old('remove_thumbnail'))>
            サムネイルを削除する
        </label>
    @endif
    <input id="thumbnail" name="thumbnail" type="file" accept=".jpg,.jpeg,.png,.gif,.webp" class="form-input">
    <p class="form-hint">jpg・png・gif・webp、2MB まで。{{ $work?->thumbnail_url ? '選ぶと現在の画像と差し替えます。' : '' }}</p>
    <x-admin.form-error :messages="$errors->get('thumbnail')" />
</div>

<fieldset class="form-group">
    <legend class="form-label">ギャラリー画像</legend>
    @if ($work?->images->isNotEmpty())
        <div class="gallery-grid">
            @foreach ($work->images as $image)
                <label>
                    <img src="{{ $image->url }}" alt="">
                    <span>
                        <input type="checkbox" name="delete_images[]" value="{{ $image->id }}" @checked(in_array((string) $image->id, old('delete_images', []), true))>
                        この画像を削除
                    </span>
                </label>
            @endforeach
        </div>
    @endif
    <label for="images" class="form-label">画像を追加</label>
    <input id="images" name="images[]" type="file" accept=".jpg,.jpeg,.png,.gif,.webp" multiple class="form-input">
    <p class="form-hint">複数選択できます（1回に10枚まで）。jpg・png・gif・webp、1枚 2MB まで。</p>
    <x-admin.form-error :messages="Arr::flatten([$errors->get('images'), $errors->get('images.*')])" />
</fieldset>

<div class="form-group">
    <label for="category_id" class="form-label">カテゴリ</label>
    <select id="category_id" name="category_id" class="form-input form-input--narrow">
        <option value="">カテゴリなし</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected((string) old('category_id', $work?->category_id) === (string) $category->id)>{{ $category->name }}</option>
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

<fieldset class="form-group">
    <legend class="form-label">使用技術</legend>
    @forelse ($technologies as $label => $group)
        <p class="tag-list__heading">{{ $label }}</p>
        <div class="tag-list">
            @foreach ($group as $technology)
                <label>
                    <input type="checkbox" name="technologies[]" value="{{ $technology->id }}" @checked(in_array($technology->id, $selectedTechnologyIds, true))>
                    {{ $technology->name }}
                </label>
            @endforeach
        </div>
    @empty
        <p class="form-hint">使用技術はまだありません。<a href="{{ route('admin.technologies.create') }}">使用技術を登録</a>してください。</p>
    @endforelse
    <x-admin.form-error :messages="Arr::flatten([$errors->get('technologies'), $errors->get('technologies.*')])" />
</fieldset>

<div class="form-group">
    <label for="slug" class="form-label">スラッグ</label>
    <input id="slug" name="slug" type="text" class="form-input form-input--narrow" value="{{ old('slug', $work?->slug) }}" required>
    <p class="form-hint">URL に使います。英小文字・数字・ハイフンで入力してください（例：my-first-post）。</p>
    <x-admin.form-error :messages="$errors->get('slug')" />
</div>

<div class="form-group">
    <label for="status" class="form-label">ステータス</label>
    <select id="status" name="status" class="form-input form-input--narrow">
        @foreach ($statuses as $status)
            <option value="{{ $status->value }}" @selected(old('status', $work?->status->value ?? 'draft') === $status->value)>{{ $status->label() }}</option>
        @endforeach
    </select>
    <x-admin.form-error :messages="$errors->get('status')" />
</div>

<div class="form-group">
    <label for="published_at" class="form-label">公開日</label>
    <input id="published_at" name="published_at" type="date" class="form-input form-input--narrow" value="{{ old('published_at', $work?->published_at?->format('Y-m-d') ?? today()->format('Y-m-d')) }}" required>
    <p class="form-hint">公開ページに表示する日付です。並び順にも使います。</p>
    <x-admin.form-error :messages="$errors->get('published_at')" />
</div>

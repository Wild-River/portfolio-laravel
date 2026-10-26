@php
    $selectedTagIds = array_map('intval', old('tags', $post?->tags->pluck('id')->all() ?? []));
@endphp

<div>
    <x-input-label for="title" value="タイトル" />
    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $post?->title)" required autofocus />
    <x-input-error class="mt-2" :messages="$errors->get('title')" />
</div>

<div>
    <x-input-label for="body" value="本文" />
    <textarea id="body" name="body" rows="12" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('body', $post?->body) }}</textarea>
    <x-input-error class="mt-2" :messages="$errors->get('body')" />
</div>

<div>
    <x-input-label for="thumbnail" value="サムネイル画像" />
    @if ($post?->thumbnail_url)
        <img src="{{ $post->thumbnail_url }}" alt="現在のサムネイル" class="mt-2 max-w-sm rounded">
        <label class="mt-2 inline-flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" name="remove_thumbnail" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('remove_thumbnail'))>
            サムネイルを削除する
        </label>
    @endif
    <input id="thumbnail" name="thumbnail" type="file" accept=".jpg,.jpeg,.png,.gif,.webp" class="mt-2 block w-full text-sm text-gray-600">
    <p class="mt-1 text-sm text-gray-500">jpg・png・gif・webp、2MB まで。{{ $post?->thumbnail_url ? '選ぶと現在の画像と差し替えます。' : '' }}</p>
    <x-input-error class="mt-2" :messages="$errors->get('thumbnail')" />
</div>

<div>
    <x-input-label for="category_id" value="カテゴリ" />
    <select id="category_id" name="category_id" class="mt-1 block w-full sm:w-64 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        <option value="">カテゴリなし</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected((string) old('category_id', $post?->category_id) === (string) $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
    <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
</div>

<fieldset>
    <legend class="block font-medium text-sm text-gray-700">タグ</legend>
    <div class="mt-2 flex flex-wrap gap-x-6 gap-y-2">
        @forelse ($tags as $tag)
            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(in_array($tag->id, $selectedTagIds, true))>
                {{ $tag->name }}
            </label>
        @empty
            <p class="text-sm text-gray-500">タグはまだありません。</p>
        @endforelse
    </div>
    <x-input-error class="mt-2" :messages="[...$errors->get('tags'), ...$errors->get('tags.*')]" />
</fieldset>

<div>
    <x-input-label for="slug" value="スラッグ" />
    <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full sm:w-64" :value="old('slug', $post?->slug)" required />
    <p class="mt-1 text-sm text-gray-500">URL に使います。半角英数字・ハイフン・アンダースコアで入力してください。</p>
    <x-input-error class="mt-2" :messages="$errors->get('slug')" />
</div>

<div>
    <x-input-label for="status" value="ステータス" />
    <select id="status" name="status" class="mt-1 block w-full sm:w-64 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        @foreach ($statuses as $status)
            <option value="{{ $status->value }}" @selected(old('status', $post?->status->value ?? 'draft') === $status->value)>{{ $status->label() }}</option>
        @endforeach
    </select>
    <x-input-error class="mt-2" :messages="$errors->get('status')" />
</div>

<div>
    <x-input-label for="name" value="カテゴリ名" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $category?->name)" required autofocus />
    <x-input-error class="mt-2" :messages="$errors->get('name')" />
</div>

<div>
    <x-input-label for="slug" value="スラッグ" />
    <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" :value="old('slug', $category?->slug)" required />
    <p class="mt-1 text-sm text-gray-500">URL に使います。英小文字・数字・ハイフンで入力してください（例：my-first-post）。</p>
    <x-input-error class="mt-2" :messages="$errors->get('slug')" />
</div>

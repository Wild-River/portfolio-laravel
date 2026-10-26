<div>
    <x-input-label for="name" value="タグ名" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $tag?->name)" required autofocus />
    <x-input-error class="mt-2" :messages="$errors->get('name')" />
</div>

<div>
    <x-input-label for="slug" value="スラッグ" />
    <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" :value="old('slug', $tag?->slug)" required />
    <p class="mt-1 text-sm text-gray-500">URL に使います。半角英数字・ハイフン・アンダースコアで入力してください。</p>
    <x-input-error class="mt-2" :messages="$errors->get('slug')" />
</div>

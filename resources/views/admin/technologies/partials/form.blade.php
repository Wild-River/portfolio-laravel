<div>
    <x-input-label for="name" value="技術名" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $technology?->name)" required autofocus />
    <x-input-error class="mt-2" :messages="$errors->get('name')" />
</div>

<div>
    <x-input-label for="category" value="分類" />
    <select id="category" name="category" class="mt-1 block w-full sm:w-64 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        @foreach ($categories as $category)
            <option value="{{ $category->value }}" @selected(old('category', $technology?->category->value) === $category->value)>{{ $category->label() }}</option>
        @endforeach
    </select>
    <p class="mt-1 text-sm text-gray-500">公開ページでは、この分類ごとにまとめて表示します。</p>
    <x-input-error class="mt-2" :messages="$errors->get('category')" />
</div>

<div>
    <x-input-label for="icon" value="アイコン名（任意）" />
    <x-text-input id="icon" name="icon" type="text" class="mt-1 block w-full sm:w-64" :value="old('icon', $technology?->icon)" placeholder="react" />
    <p class="mt-1 text-sm text-gray-500"><a href="https://simpleicons.org/" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:underline">Simple Icons</a> のスラッグです（例：Next.js は nextdotjs）。Simple Icons にないものは、画像を public/images/technologies に置いてファイル名を入力すると、元の色のまま表示します（例：sakura-internet.svg）。空のままにすると、頭文字を表示します。</p>
    <x-input-error class="mt-2" :messages="$errors->get('icon')" />
</div>

<div>
    <x-input-label for="color" value="アイコンの色（任意）" />
    <x-text-input id="color" name="color" type="text" class="mt-1 block w-full sm:w-64" :value="old('color', $technology?->color)" placeholder="#61DAFB" />
    <p class="mt-1 text-sm text-gray-500">#から始まる6桁のカラーコードです。Simple Icons のページに載っているブランドカラーを入れてください。空のままにすると、文字色で表示します。画像ファイルのアイコンには使いません。</p>
    <x-input-error class="mt-2" :messages="$errors->get('color')" />
</div>

<div>
    <x-input-label for="sort_order" value="表示順" />
    <x-text-input id="sort_order" name="sort_order" type="number" min="0" max="9999" class="mt-1 block w-full sm:w-32" :value="old('sort_order', $technology?->sort_order ?? 0)" required />
    <p class="mt-1 text-sm text-gray-500">同じ分類の中で、小さい数字から順に並びます。</p>
    <x-input-error class="mt-2" :messages="$errors->get('sort_order')" />
</div>

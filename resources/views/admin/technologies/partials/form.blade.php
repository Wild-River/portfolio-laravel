<div class="form-group">
    <label for="name" class="form-label">技術名</label>
    <input id="name" name="name" type="text" class="form-input" value="{{ old('name', $technology?->name) }}" required autofocus>
    <x-admin.form-error :messages="$errors->get('name')" />
</div>

<div class="form-group">
    <label for="category" class="form-label">分類</label>
    <select id="category" name="category" class="form-input form-input--narrow">
        @foreach ($categories as $category)
            <option value="{{ $category->value }}" @selected(old('category', $technology?->category->value) === $category->value)>{{ $category->label() }}</option>
        @endforeach
    </select>
    <p class="form-hint">公開ページでは、この分類ごとにまとめて表示します。</p>
    <x-admin.form-error :messages="$errors->get('category')" />
</div>

<div class="form-group">
    <label for="icon" class="form-label">アイコン名（任意）</label>
    <input id="icon" name="icon" type="text" class="form-input form-input--narrow" value="{{ old('icon', $technology?->icon) }}" placeholder="react">
    <p class="form-hint"><a href="https://simpleicons.org/" target="_blank" rel="noopener noreferrer">Simple Icons</a> のスラッグです（例：Next.js は nextdotjs）。Simple Icons にないものは、画像を public/images/technologies に置いてファイル名を入力すると、元の色のまま表示します（例：sakura-internet.svg）。空のままにすると、頭文字を表示します。</p>
    <x-admin.form-error :messages="$errors->get('icon')" />
</div>

<div class="form-group">
    <label for="color" class="form-label">アイコンの色（任意）</label>
    <input id="color" name="color" type="text" class="form-input form-input--narrow" value="{{ old('color', $technology?->color) }}" placeholder="#61DAFB">
    <p class="form-hint">#から始まる6桁のカラーコードです。Simple Icons のページに載っているブランドカラーを入れてください。空のままにすると、文字色で表示します。画像ファイルのアイコンには使いません。</p>
    <x-admin.form-error :messages="$errors->get('color')" />
</div>

<div class="form-group">
    <label for="sort_order" class="form-label">表示順</label>
    <input id="sort_order" name="sort_order" type="number" min="0" max="9999" class="form-input form-input--short" value="{{ old('sort_order', $technology?->sort_order ?? 0) }}" required>
    <p class="form-hint">同じ分類の中で、小さい数字から順に並びます。</p>
    <x-admin.form-error :messages="$errors->get('sort_order')" />
</div>

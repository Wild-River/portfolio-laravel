<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                カテゴリ一覧
            </h2>
            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-gray-700">
                + 新規登録
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-auth-session-status :status="session('status')" />

            <div class="bg-white overflow-x-auto shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-6 py-3">ID</th>
                            <th class="px-6 py-3">カテゴリ名</th>
                            <th class="px-6 py-3">スラッグ</th>
                            <th class="px-6 py-3">記事数</th>
                            <th class="px-6 py-3">制作アプリ数</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-900">
                        @forelse ($categories as $category)
                            <tr>
                                <td class="px-6 py-3">{{ $category->id }}</td>
                                <td class="px-6 py-3">{{ $category->name }}</td>
                                <td class="px-6 py-3">{{ $category->slug }}</td>
                                <td class="px-6 py-3">{{ $category->posts_count }}</td>
                                <td class="px-6 py-3">{{ $category->works_count }}</td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-indigo-600 hover:underline">編集</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-6 text-center text-gray-500">カテゴリはまだありません。</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

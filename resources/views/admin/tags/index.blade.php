<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                タグ一覧
            </h2>
            <a href="{{ route('admin.tags.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-gray-700">
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
                            <th class="px-6 py-3">タグ名</th>
                            <th class="px-6 py-3">スラッグ</th>
                            <th class="px-6 py-3">記事数</th>
                            <th class="px-6 py-3">制作アプリ数</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-900">
                        @forelse ($tags as $tag)
                            <tr>
                                <td class="px-6 py-3">{{ $tag->id }}</td>
                                <td class="px-6 py-3">{{ $tag->name }}</td>
                                <td class="px-6 py-3">{{ $tag->slug }}</td>
                                <td class="px-6 py-3">{{ $tag->posts_count }}</td>
                                <td class="px-6 py-3">{{ $tag->works_count }}</td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('admin.tags.edit', $tag) }}" class="text-indigo-600 hover:underline">編集</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-6 text-center text-gray-500">タグはまだありません。</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

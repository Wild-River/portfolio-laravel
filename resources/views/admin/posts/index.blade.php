<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                記事一覧
            </h2>
            <a href="{{ route('admin.posts.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-gray-700">
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
                            <th class="px-6 py-3">サムネイル</th>
                            <th class="px-6 py-3">ID</th>
                            <th class="px-6 py-3">タイトル</th>
                            <th class="px-6 py-3">本文</th>
                            <th class="px-6 py-3">スラッグ</th>
                            <th class="px-6 py-3">ステータス</th>
                            <th class="px-6 py-3">カテゴリ</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-900">
                        @forelse ($posts as $post)
                            <tr>
                                <td class="px-6 py-3">
                                    @if ($post->thumbnail_url)
                                        <img src="{{ $post->thumbnail_url }}" alt="" class="h-12 w-20 object-cover rounded">
                                    @endif
                                </td>
                                <td class="px-6 py-3">{{ $post->id }}</td>
                                <td class="px-6 py-3">{{ $post->title }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ Str::limit($post->body, 20) }}</td>
                                <td class="px-6 py-3">{{ $post->slug }}</td>
                                <td class="px-6 py-3">{{ $post->status->label() }}</td>
                                <td class="px-6 py-3">{{ $post->category?->name }}</td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="text-indigo-600 hover:underline">編集</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-6 text-center text-gray-500">記事はまだありません。</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $posts->links() }}
        </div>
    </div>
</x-app-layout>

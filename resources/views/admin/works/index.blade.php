<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                制作アプリ一覧
            </h2>
            <a href="{{ route('admin.works.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-gray-700">
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
                            <th class="px-6 py-3">説明文</th>
                            <th class="px-6 py-3">スラッグ</th>
                            <th class="px-6 py-3">ステータス</th>
                            <th class="px-6 py-3">カテゴリ</th>
                            <th class="px-6 py-3">ギャラリー</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tdescription class="divide-y divide-gray-100 text-gray-900">
                        @forelse ($works as $work)
                            <tr>
                                <td class="px-6 py-3">
                                    @if ($work->thumbnail_url)
                                        <img src="{{ $work->thumbnail_url }}" alt="" class="h-12 w-20 object-cover rounded">
                                    @endif
                                </td>
                                <td class="px-6 py-3">{{ $work->id }}</td>
                                <td class="px-6 py-3">{{ $work->title }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ Str::limit($work->description, 20) }}</td>
                                <td class="px-6 py-3">{{ $work->slug }}</td>
                                <td class="px-6 py-3">{{ $work->status->label() }}</td>
                                <td class="px-6 py-3">{{ $work->category?->name }}</td>
                                <td class="px-6 py-3">{{ $work->images_count }}枚</td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('admin.works.edit', $work) }}" class="text-indigo-600 hover:underline">編集</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-6 text-center text-gray-500">制作アプリはまだありません。</td>
                            </tr>
                        @endforelse
                    </tdescription>
                </table>
            </div>

            {{ $works->links() }}
        </div>
    </div>
</x-app-layout>

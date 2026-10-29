<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            使用技術編集
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg space-y-6">
                <form method="post" action="{{ route('admin.technologies.update', $technology) }}" class="max-w-xl space-y-6">
                    @csrf
                    @method('put')

                    @include('admin.technologies.partials.form')

                    <div class="flex items-center gap-4">
                        <x-primary-button>更新する</x-primary-button>
                        <a href="{{ route('admin.technologies.index') }}" class="text-sm text-gray-600 hover:underline">戻る</a>
                    </div>
                </form>

                <form method="post" action="{{ route('admin.technologies.destroy', $technology) }}" onsubmit="return confirm('この使用技術を削除しますか？\n制作アプリからも外れます。');">
                    @csrf
                    @method('delete')

                    <x-danger-button>削除する</x-danger-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

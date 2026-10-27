<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            制作アプリ登録
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <form method="post" action="{{ route('admin.works.store') }}" enctype="multipart/form-data" class="max-w-3xl space-y-6">
                    @csrf

                    @include('admin.works.partials.form', ['work' => null])

                    <div class="flex items-center gap-4">
                        <x-primary-button>登録する</x-primary-button>
                        <a href="{{ route('admin.works.index') }}" class="text-sm text-gray-600 hover:underline">戻る</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

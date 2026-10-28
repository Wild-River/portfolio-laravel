<x-site-layout title="制作アプリ一覧" :wide="true">
    <x-slot name="header">
        @include('site.partials.banner', ['title' => 'works'])
    </x-slot>

    <p class="breadcrumb"><a href="{{ route('home') }}">TOP</a> / 制作アプリ一覧</p>

    <div class="portfolio-grid">
        @foreach ($works as $work)
            @include('site.partials.work-card', ['showDate' => true])
        @endforeach
    </div>
</x-site-layout>

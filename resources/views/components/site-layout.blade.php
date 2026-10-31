@props(['title' => null, 'wide' => false, 'description' => null, 'image' => null, 'type' => 'website'])

@php
    $siteName = 'Rumi Arakawa -portfolio site-';
    $pageTitle = $title ? "{$title} | {$siteName}" : $siteName;
    $description = Str::limit(Str::squish($description ?? '2026年5月からのプログラミングの学習ログと制作アプリ'), 120);
    // SNS のクローラーは相対 URL を読めないので、画像は絶対 URL にする
    $image = url($image ?? '/images/ogp.jpg');
@endphp

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $description }}">
    <meta property="og:title" content="{{ $title ?? $siteName }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:type" content="{{ $type }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $image }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="ja_JP">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    @vite(['resources/css/site.css', 'resources/js/site.js'])
</head>

<body>
    {{ $header }}

    <div @class(['container', 'container--wide' => $wide])>
        {{ $slot }}
    </div>

    @include('site.partials.footer')
</body>

</html>

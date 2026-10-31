@props(['title' => null])

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ? "{$title} | " : '' }}ブログ管理システム</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>

<body>
    @include('admin.partials.sidebar')

    <div class="container">
        @if (session('status'))
            <p class="flash">{{ session('status') }}</p>
        @endif

        {{ $slot }}
    </div>
</body>

</html>

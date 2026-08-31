<!DOCTYPE html>
<html lang="en" class="bg-black">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Mavnus')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-black flex flex-col w-full min-h-screen">
    <div class="flex flex-col justify-center items-center min-h-screen">
        @yield('content')
    </div>
</body>

</html>

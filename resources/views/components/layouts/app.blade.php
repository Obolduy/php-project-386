<!DOCTYPE html>
<html lang="ru" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full bg-neutral-50 text-neutral-900">
        <main class="mx-auto flex min-h-full max-w-xl flex-col justify-center px-6 py-16">
            {{ $slot }}
        </main>
    </body>
</html>

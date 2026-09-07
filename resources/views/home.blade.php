<x-layouts.app>
    <h1 class="text-3xl font-semibold tracking-tight">{{ config('app.name') }}</h1>

    <p class="mt-3 text-neutral-600">
        Выберите свободное время в календаре и запишитесь на разговор с владельцем календаря.
    </p>

    <a
        href="{{ route('booking') }}"
        class="mt-8 flex items-center justify-between rounded-xl border border-neutral-200 bg-white px-5 py-4 transition hover:border-neutral-400"
    >
        <span class="font-medium">Встреча</span>
        <span class="rounded-full bg-neutral-100 px-3 py-1 text-sm text-neutral-600">{{ $durationMinutes }} мин</span>
    </a>
</x-layouts.app>

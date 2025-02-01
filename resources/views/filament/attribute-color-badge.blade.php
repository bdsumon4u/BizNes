@props([
    'key',
    'value' => null,
])

<span
    class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-700 rounded-full gap-x-2 ring-1 ring-inset ring-gray-200 dark:text-gray-300 dark:ring-white/10"
>
    <svg class="size-5" style="color: {{ $key }}" aria-hidden="true" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 5.09V18.92A6.92 6.92 0 0 0 18.919 12c0-3.816-3.103-6.91-6.919-6.91Z" />
        <path
            d="M12 .938C5.888.938.937 5.888.937 12c0 6.113 4.95 11.063 11.063 11.063S23.063 18.113 23.063 12C23.063 5.888 18.113.937 12 .937Zm0 19.359A8.294 8.294 0 0 1 3.703 12 8.294 8.294 0 0 1 12 3.703 8.294 8.294 0 0 1 20.297 12 8.294 8.294 0 0 1 12 20.297Z"
        />
    </svg>
    @if ($value)
        <kb>{{ $value }}</kb>
    @endif
</span>

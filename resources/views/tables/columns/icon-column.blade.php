<div class="px-4 py-3 filament-icon-picker-icon-column">
    @if ($icon = $getState())
        <x-icon class="text-gray-500 size-6 dark:text-gray-400" :name="$icon" aria-hidden="true" />
    @endif
</div>

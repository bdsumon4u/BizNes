<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php
        $id = $getId();
        $isConcealed = $isConcealed();
        $isDisabled = $isDisabled();
        $statePath = $getStatePath();
    @endphp

    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">
        <!-- Interact with the `state` property in Alpine.js -->
        <x-filament::input.wrapper>
            <x-filament::input
                x-model="state"
            />
        </x-filament::input.wrapper>
    </div>
</x-dynamic-component>

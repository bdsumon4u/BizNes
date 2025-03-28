<x-filament-panels::page
    @class([
        'fi-resource-edit-record-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'fi-resource-record-' . $record->getKey(),
    ])
>
    <div
        class="pb-10"
        x-data="{
            activeTab: @entangle('activeTab')
        }"
    >
        <div x-bind:class="{'resource-edit-tab': $store.sidebar.isOpen}" class="sticky z-30 -mx-4 -mt-4 md:-mx-6 lg:mx-0 bg-white/75 backdrop-blur-sm dark:bg-gray-900/80 top-16">
            <x-filament::tabs :contained="true">
                <x-filament::tabs.item
                    alpine-active="activeTab === 'detail'"
                    x-on:click="activeTab = 'detail'"
                    icon="untitledui-file-02"
                >
                    {{ __('Overview') }}
                </x-filament::tabs.item>

                <x-filament::tabs.item
                    alpine-active="activeTab === 'media'"
                    x-on:click="activeTab = 'media'"
                    icon="untitledui-image"
                >
                    {{ __('Media') }}
                </x-filament::tabs.item>

                <x-filament::tabs.item
                    alpine-active="activeTab === 'price'"
                    x-on:click="activeTab = 'price'"
                    icon="untitledui-coins-stacked-02"
                >
                    {{ __('Pricing') }}
                </x-filament::tabs.item>

                <x-filament::tabs.item
                    alpine-active="activeTab === 'files'"
                    x-on:click="activeTab = 'files'"
                    icon="untitledui-paperclip"
                >
                    {{ __('Files') }}
                </x-filament::tabs.item>

                @if ($record->isStandard())
                <x-filament::tabs.item
                    alpine-active="activeTab === 'inventory'"
                    x-on:click="activeTab = 'inventory'"
                    icon="untitledui-package"
                >
                    {{ __('Inventory') }}
                </x-filament::tabs.item>

                <x-filament::tabs.item
                    alpine-active="activeTab === 'shipping'"
                    x-on:click="activeTab = 'shipping'"
                    icon="untitledui-plane"
                >
                    {{ __('Shipping') }}
                </x-filament::tabs.item>
                @endif

                <x-filament::tabs.item
                    alpine-active="activeTab === 'options'"
                    x-on:click="activeTab = 'options'"
                    icon="untitledui-puzzle-piece"
                >
                    {{ __('Options') }}
                </x-filament::tabs.item>
            </x-filament::tabs>
        </div>

        <div class="mt-4 resource-edit-tab-content">
            <div x-show="activeTab === 'detail'">
                @capture($form)
                    <x-filament-panels::form
                        id="form"
                        :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
                        wire:submit="save"
                    >
                        {{ $this->form }}

                        <x-filament-panels::form.actions
                            :actions="$this->getCachedFormActions()"
                            :full-width="$this->hasFullWidthFormActions()"
                        />
                    </x-filament-panels::form>
                @endcapture

                {{ $form() }}
            </div>

            <div x-show="activeTab === 'media'">
                @livewire(\App\Filament\Resources\VariantResource\Pages\ManageMedia::class, ['record' => $record->getKey()])
            </div>

            <div x-show="activeTab === 'price'">
                @livewire(\App\Filament\Resources\VariantResource\Pages\ManagePricing::class, ['record' => $record->getKey()])
            </div>

            <div x-cloak x-show="activeTab === 'files'">
                Files
            </div>

            @if ($record->isStandard())
            <div x-cloak x-show="activeTab === 'inventory'">
                Inventory
            </div>

            <div x-cloak x-show="activeTab === 'shipping'">
                @livewire(\App\Filament\Resources\VariantResource\Pages\ManageShipping::class, ['record' => $record->getKey()])
            </div>
            @endif

            <div x-cloak x-show="activeTab === 'options'">
                @livewire(\App\Filament\Resources\VariantResource\Pages\ManageOptions::class, ['record' => $record->getKey()])
            </div>
        </div>
    </div>

    <x-filament-panels::page.unsaved-data-changes-alert />
</x-filament-panels::page>

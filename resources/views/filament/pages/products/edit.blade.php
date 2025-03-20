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
            options: [
                'detail',
                'media',
                'price',
                'files',
                'attributes',
                'variants',
                'inventory',
                'seo',
                'shipping',
                'related'
            ],
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

                @if (true || $record->isVirtual())
                    <x-filament::tabs.item
                        alpine-active="activeTab === 'files'"
                        x-on:click="activeTab = 'files'"
                        icon="untitledui-paperclip"
                    >
                        {{ __('Files') }}
                    </x-filament::tabs.item>
                @endif

                @if (true || $record->canUseAttributes())
                    <x-filament::tabs.item
                        alpine-active="activeTab === 'attributes'"
                        x-on:click="activeTab = 'attributes'"
                        icon="untitledui-puzzle-piece"
                    >
                        {{ __('Attributes') }}
                    </x-filament::tabs.item>
                @endif

                @if (true || $record->canUseVariants())
                    <x-filament::tabs.item
                        alpine-active="activeTab === 'variants'"
                        x-on:click="activeTab = 'variants'"
                        icon="untitledui-book-open"
                    >
                        {{ __('Variants') }}
                    </x-filament::tabs.item>
                @endif

                <x-filament::tabs.item
                    alpine-active="activeTab === 'inventory'"
                    x-on:click="activeTab = 'inventory'"
                    icon="untitledui-package"
                >
                    {{ __('Inventory') }}
                </x-filament::tabs.item>

                @if (true || $record->canUseShipping())
                    <x-filament::tabs.item
                        alpine-active="activeTab === 'shipping'"
                        x-on:click="activeTab = 'shipping'"
                        icon="untitledui-plane"
                    >
                        {{ __('Shipping') }}
                    </x-filament::tabs.item>
                @endif

                <x-filament::tabs.item
                    alpine-active="activeTab === 'seo'"
                    x-on:click="activeTab = 'seo'"
                    icon="untitledui-monitor-02"
                >
                    {{ __('SEO') }}
                </x-filament::tabs.item>

                <x-filament::tabs.item
                    alpine-active="activeTab === 'related'"
                    x-on:click="activeTab = 'related'"
                    icon="untitledui-dataflow-04"
                >
                    {{ __('Related Products') }}
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
                @livewire(\App\Filament\Resources\ProductResource\Pages\ManageMedia::class, ['record' => $record->getKey()])
            </div>
            <div x-show="activeTab === 'price'">
                @livewire(\App\Filament\Resources\ProductResource\Pages\ManagePricing::class, ['record' => $record->getKey()])
            </div>

            @if (true || $record->isVirtual())
                <div x-cloak x-show="activeTab === 'files'">
                    Files
                </div>
            @endif

            @if (true || $record->canUseAttributes())
                <div x-cloak x-show="activeTab === 'attributes'">
                    @livewire(\App\Filament\Resources\ProductResource\Pages\ManageAttributes::class, ['record' => $record->getKey()])
                </div>
            @endif

            @if (true || $record->canUseVariants())
                <div x-cloak x-show="activeTab === 'variants'">
                    @livewire(\App\Filament\Resources\ProductResource\Pages\ManageVariants::class, ['record' => $record->getKey()])
                </div>
            @endif

            <div x-cloak x-show="activeTab === 'inventory'">
                Inventory
            </div>
            <div x-cloak x-show="activeTab === 'seo'">
                @livewire(\App\Filament\Resources\ProductResource\Pages\ManageSEO::class, ['record' => $record->getKey()])
            </div>

            @if (true || $record->canUseShipping())
                <div x-cloak x-show="activeTab === 'shipping'">
                    @livewire(\App\Filament\Resources\ProductResource\Pages\ManageShipping::class, ['record' => $record->getKey()])
                </div>
            @endif

            <div x-cloak x-show="activeTab === 'related'">
                Related
            </div>
        </div>
    </div>

    <x-filament-panels::page.unsaved-data-changes-alert />
</x-filament-panels::page>

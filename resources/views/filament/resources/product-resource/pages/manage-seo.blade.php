<x-filament-panels::page
    @class([
        'fi-resource-edit-record-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'fi-resource-record-' . $record->getKey(),
    ])
>
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
    <div x-data="{meta_title: null}" class="grid gap-6 lg:grid-cols-2 lg:gap-x-10">
        {{ $form() }}

        <div class="max-w-xl">
            <h4 class="text-sm leading-5 text-gray-600 dark:text-gray-400">
                {{ __('Here is a preview of what an search engine can display, play with it!') }}
            </h4>
            <div
                class="flex flex-col h-auto p-1 mt-5 overflow-hidden bg-gray-100 shadow-sm rounded-xl ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-white/10"
            >
                <div class="flex w-full items-center justify-between p-1.5">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-red-500 border border-red-400 rounded-full"></div>
                        <div class="w-3 h-3 bg-yellow-500 border border-yellow-400 rounded-full"></div>
                        <div class="w-3 h-3 bg-green-500 border border-green-400 rounded-full"></div>
                    </div>
                    <x-untitledui-google-chrome class="text-gray-500 size-5 dark:text-gray-300" />
                </div>
                <div class="w-full h-full p-4 mt-2 overflow-auto bg-white rounded-lg dark:bg-gray-950 sm:p-6">
                    <div class="flex flex-col">
                        <h3 class="font-medium leading-6 text-primary-600 dark:text-primary-500" x-text="meta_title"></h3>
                        <span class="mt-1 text-sm leading-5 text-green-600 truncate dark:text-green-400">
                            {{ config('app.url') }}/{your-custom-prefix}/{{ $data['slug'] }}
                        </span>
                        <p class="mt-1 text-sm leading-5 text-gray-500 text-whitespace-no-wrap dark:text-gray-400">
                            {{ $data['meta_description'] }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>

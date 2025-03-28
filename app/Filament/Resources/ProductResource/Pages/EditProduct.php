<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Url;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected static string $view = 'filament.pages.products.edit';

    #[Url(as: 'tab')]
    public string $activeTab = 'detail';

    public function getTitle(): string|Htmlable
    {
        $template = '<h2
            class="text-2xl font-bold leading-6 text-gray-900 font-heading dark:text-white sm:truncate sm:text-3xl sm:leading-9"
        >
            {{ $record->name }}
        </h2>';

        if (static::class === EditProduct::class) {
            $template = '<x-filament::badge
                :color="$record->type->getColor()"
                :icon="$record->type->getIcon()"
                class="inline-flex"
            >
                {{ $record->type->getLabel() }}
            </x-filament::badge>'.$template;
        }

        return new HtmlString(Blade::render($template, ['record' => $this->getRecord()]));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

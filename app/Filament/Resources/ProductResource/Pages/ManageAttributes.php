<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Enum\FieldType;
use App\Filament\Resources\AttributeResource;
use App\Filament\Resources\ProductResource;
use App\Models\Attribute;
use App\Models\AttributeProduct;
use App\Tables\Columns\IconColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconSize;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use JaOcero\RadioDeck\Forms\Components\RadioDeck;
use Livewire\Attributes\Lazy;

#[Lazy()]
class ManageAttributes extends ManageRelatedRecords
{
    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'attributes';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Attributes';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AttributeProduct::with(['attribute', 'option', 'option.attribute'])
                    ->where('product_id', $this->getRecord()->getKey())
            )
            ->recordTitleAttribute('name')
            ->columns([
                IconColumn::make('attribute.icon')
                    ->label(__('Icon')),
                Tables\Columns\TextColumn::make('attribute.name'),
                Tables\Columns\TextColumn::make('option.value'),
                Tables\Columns\TextColumn::make('value')
                    ->html()
                    ->limit(150),
            ])
            ->defaultGroup(
                Group::make('attribute_id')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false)
                    ->getTitleFromRecordUsing(fn ($record): string => $record->attribute->name),
            )
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                // Tables\Actions\AssociateAction::make(),
                Tables\Actions\Action::make('choose')
                    ->label(__('Choose attributes'))
                    ->steps([
                        Forms\Components\Wizard\Step::make('attributes')
                            ->icon('untitledui-puzzle-piece')
                            ->schema([
                                RadioDeck::make('attributes')
                                    ->options(
                                        AttributeResource::getEloquentQuery()
                                            ->scopes('enabled')
                                            ->select('id', 'name')
                                            ->pluck('name', 'id')
                                    )
                                    ->descriptions(
                                        AttributeResource::getEloquentQuery()
                                            ->scopes('enabled')
                                            ->select('id', 'description')
                                            ->pluck('description', 'id')
                                            ->toArray()
                                    )
                                    ->icons(
                                        AttributeResource::getEloquentQuery()
                                            ->scopes('enabled')
                                            ->select('id', 'icon')
                                            ->pluck('icon', 'id')
                                            ->toArray()
                                    )
                                    ->alignment(Alignment::Start)
                                    ->iconSize(IconSize::Small)
                                    ->color('primary')
                                    ->columns(2)
                                    ->live()
                                    ->afterStateUpdated(
                                        fn (RadioDeck $component) => $component->getContainer()
                                            ->getParentComponent()
                                            ->getContainer()
                                            ->getComponent('options')
                                            ->getChildComponentContainer()
                                            ->fill()
                                    )
                                    ->multiple()
                                    ->required(),
                            ]),
                        Forms\Components\Wizard\Step::make('options')
                            ->icon('untitledui-dotpoints')
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->schema(function (Forms\Get $get): array {
                                        $selectSchema = [];
                                        $textSchema = [];

                                        $attributes = Attribute::with('options')
                                            ->select('id', 'name', 'type', 'slug')
                                            ->whereIn('id', $get('attributes'))
                                            ->get();

                                        $selectedAttributes = AttributeProduct::query()
                                            ->where('product_id', $this->getRecord()->getKey())
                                            ->whereIn('attribute_id', $get('attributes'))
                                            ->get()
                                            ->mapToGroups(fn (AttributeProduct $attributeProduct) => [
                                                $attributeProduct->attribute_id => $attributeProduct->option_id,
                                            ]);

                                        foreach ($attributes as $attribute) {
                                            /** @var Attribute $attribute */
                                            if ($attribute->hasMultipleOptions() || $attribute->hasSingleOption()) {
                                                $selectSchema[] = Forms\Components\Select::make("options.{$attribute->id}")
                                                    ->key($attribute->slug)
                                                    ->label($attribute->name)
                                                    ->required()
                                                    ->options($attribute->options->pluck('value', 'id'))
                                                    ->disableOptionWhen(
                                                        fn (string $value): bool => in_array(
                                                            $value,
                                                            $selectedAttributes->get($attribute->id)?->toArray() ?? []
                                                        )
                                                    )
                                                    ->multiple($attribute->hasMultipleOptions())
                                                    ->preload()
                                                    ->optionsLimit(10)
                                                    ->native(false);
                                            }

                                            if ($attribute->hasTextOption()) {
                                                $field = match ($attribute->type) {
                                                    FieldType::RichText => Forms\Components\RichEditor::make("options.value.{$attribute->id}")
                                                        ->label($attribute->name)
                                                        ->key($attribute->slug)
                                                        ->disabled($selectedAttributes->get($attribute->id) !== null)
                                                        ->columnSpanFull(),
                                                    FieldType::DatePicker => Forms\Components\DatePicker::make("options.value.{$attribute->id}")
                                                        ->label($attribute->name)
                                                        ->key($attribute->slug)
                                                        ->disabled($selectedAttributes->get($attribute->id) !== null)
                                                        ->native(false),
                                                    default => Forms\Components\TextInput::make("options.value.{$attribute->id}")
                                                        ->key($attribute->slug)
                                                        ->disabled($selectedAttributes->get($attribute->id) !== null)
                                                        ->label($attribute->name),
                                                };

                                                $textSchema[] = $field;
                                            }
                                        }

                                        return array_merge(
                                            $selectSchema,
                                            count($textSchema) ? [/* SEPARATOR */] : [],
                                            $textSchema
                                        );
                                    })
                                    ->key('options'),
                            ]),
                    ])
                    ->action(function (array $data) {
                        $options = data_get($data, 'options');

                        $product = $this->getRecord();
                        foreach (Arr::except($options, 'value') as $attributeId => $options) {
                            Arr::map(
                                array: Arr::wrap($options),
                                callback: fn ($value) => $product->attributes()
                                    ->attach($attributeId, ['option_id' => $value])
                            );
                        }

                        if (count($customValues = Arr::get($options, 'value', [])) > 0) {
                            foreach ($customValues as $attributeId => $value) {
                                $product->attributes()->attach([
                                    $attributeId => ['value' => $value],
                                ]);
                            }
                        }

                        Notification::make()
                            ->title(__('You have successfully added attributes to this product!'))
                            ->success()
                            ->send();

                        // $this->redirect(
                        //     route('shopper.products.edit', ['product' => $this->productId, 'tab' => 'attributes']),
                        //     navigate: true
                        // );
                    })
                    ->slideOver()
                    ->modalWidth('xl'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DissociateAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DissociateBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

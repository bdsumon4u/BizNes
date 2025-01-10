<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $tenantOwnershipRelationshipName = 'businesses';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn (string $state) => Hash::make($state))
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),
                Forms\Components\Toggle::make('is_partner')
                    ->label(__('Partner'))
                    ->onIcon('heroicon-o-exclamation-triangle')
                    ->hintIcon('heroicon-o-exclamation-circle')
                    ->hint(__('Also an owner of this business.'))
                    ->dehydrated(false)
                    ->formatStateUsing(fn (?Model $record) => $record?->isOwner(Filament::getTenant()))
                    ->afterStateUpdated(fn (bool $state, Forms\Set $set) => $state && $set('roles', [
                        Utils::getRoleModel()::whereBelongsTo(Filament::getTenant())
                            ->where('name', Utils::getSuperAdminName())
                            ->firstOrFail()
                            ->getKey(),
                    ]))
                    ->live(),
                Forms\Components\Select::make('roles')
                    ->multiple()
                    ->relationship('roles', 'name', fn ($query) => $query->whereBelongsTo(Filament::getTenant()))
                    ->pivotData([Utils::getTenantModelForeignKey() => Filament::getTenant()->getKey()])
                    ->preload()
                    ->searchable()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->saveRelationshipsWhenHidden(fn (Forms\Get $get) => $get('is_partner'))
                    ->hidden(function (?Model $record, Forms\Get $get) {
                        if ($get('is_partner') || $record?->is(Filament::auth()->user())) {
                            return true;
                        }

                        if ($record?->isOwner(Filament::getTenant())) {
                            return ! optional(Filament::auth()->user())->isOwner(Filament::getTenant());
                        }

                        return false;
                    })
                    // ->dehydrated(false) // Must be after the `disabled` method call ***IMPORTANT***
                    ->hint(fn (?Model $record) => $record?->is(Filament::auth()->user()) ? __('You cannot change your own roles.') : null),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->color(fn (Model $record) => $record->is(Filament::auth()->user()) ? Color::Green : null)
                    ->weight(fn (Model $record) => $record->is(Filament::auth()->user()) ? FontWeight::SemiBold : null)
                    ->icon(fn (Model $record) => $record->isOwner(Filament::getTenant()) ? 'heroicon-o-sparkles' : null)
                    ->iconPosition(IconPosition::After),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->icon(fn (User $record) => $record->hasVerifiedEmail() ? 'heroicon-o-check-badge' : null)
                    ->iconColor(fn (User $record) => $record->hasVerifiedEmail() ? Color::Green : null),
                Tables\Columns\TextColumn::make('businesses_count')
                    ->label(__('Businesses'))
                    ->counts('businesses')
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Utils::getSuperAdminName() => 'success',
                        default => 'primary',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver()
                    ->modalWidth('md'),
                Tables\Actions\Action::make('partner')
                    ->databaseTransaction()
                    ->action(function (Model $record) {
                        if (! $isOwner = $record->isOwner(Filament::getTenant())) {
                            $record->assignRole(
                                Utils::getRoleModel()::whereBelongsTo(Filament::getTenant())
                                    ->where('name', Utils::getSuperAdminName())
                                    ->firstOrFail()
                            );
                        }
                        $record->businesses()->updateExistingPivot(Filament::getTenant(), [
                            'is_owner' => ! $isOwner,
                        ]);
                    })
                    ->icon(fn (Model $record) => $record->isOwner(Filament::getTenant()) ? 'heroicon-o-x-circle' : 'heroicon-o-exclamation-triangle')
                    ->color(fn (Model $record) => $record->isOwner(Filament::getTenant()) ? 'danger' : 'warning')
                    ->hidden(fn (Model $record) => $record->is(Filament::auth()->user()))
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            // 'create' => Pages\CreateUser::route('/create'),
            // 'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return Utils::isResourceNavigationGroupEnabled()
            ? __('filament-shield::filament-shield.nav.group')
            : '';
    }
}

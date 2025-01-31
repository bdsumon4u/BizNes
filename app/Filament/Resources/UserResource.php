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
use Illuminate\Support\HtmlString;

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
                    ->maxLength(255)
                    ->readOnly(fn (Forms\Get $get) => $get('is_user_exists'))
                    ->helperText(fn (Forms\Get $get) => $get('is_user_exists') ? __('This found user will be used instead of creating a new one.') : null),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255)
                    ->afterStateUpdated(function (?string $state, Forms\Get $get, Forms\Set $set) {
                        $set('is_user_exists', false);
                        if (! $typedName = $get('typed_name')) {
                            $set('typed_name', $get('name'));
                        }
                        if ($state && $user = static::getModel()::query()->firstWhere('email', $state)) {
                            $set('is_user_exists', true);
                            $set('name', $user->name);
                        } elseif ($typedName) {
                            $set('name', $typedName);
                        }
                    })
                    ->helperText(fn (Forms\Get $get) => $get('is_user_exists') ? new HtmlString(__('This email address belongs to the user <strong style="color: red;">:name</strong>.', ['name' => $get('name')])) : null)
                    ->lazy(),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn (string $state) => Hash::make($state))
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->hidden(fn (Forms\Get $get) => $get('is_user_exists'))
                    ->required(fn (string $context): bool => $context === 'create'),
                Forms\Components\Toggle::make('is_partner')
                    ->saveRelationshipsUsing(function (Model $record, Forms\Get $get, Forms\Set $set) {
                        $record->businesses()->updateExistingPivot(Filament::getTenant(), [
                            'is_owner' => $get('is_partner') ?? false,
                        ]);
                    })
                    ->label(__('Partner'))
                    ->onIcon('heroicon-o-exclamation-triangle')
                    ->hintIcon('heroicon-o-exclamation-circle')
                    ->hint(__('Also an owner of this business.'))
                    ->formatStateUsing(fn (?Model $record) => $record?->isOwner(Filament::getTenant()))
                    ->afterStateUpdated(fn (bool $state, Forms\Set $set) => $set('roles', $state ? [
                        Utils::getRoleModel()::whereBelongsTo(Filament::getTenant())
                            ->where('name', Utils::getSuperAdminName())
                            ->firstOrFail()
                            ->getKey(),
                    ] : []))
                    ->dehydrated(false)
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
                    ->weight(fn (Model $record) => $record->is(Filament::auth()->user()) ? FontWeight::SemiBold : null)
                    ->icon(fn (Model $record) => $record->is(Filament::auth()->user()) ? 'heroicon-o-user-circle' : null)
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
                Tables\Columns\TextColumn::make('type')
                    ->label(__('Type'))
                    ->getStateUsing(fn (Model $record): string => $record->isOwner(Filament::getTenant()) ? __('Owner') : __('Employee'))
                    ->color(fn (Model $record): string => $record->isOwner(Filament::getTenant()) ? 'success' : 'primary')
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

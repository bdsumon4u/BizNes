<?php

namespace App\Filament\Resources\PurchaseResource\Pages;

use App\Filament\Resources\PurchaseResource;
use App\Models\Product;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class CreatePurchase extends CreateRecord
{
    protected static string $resource = PurchaseResource::class;

    protected static string $view = 'filament.pages.purchases.create';

    public ?string $search = '';

    public array $searchResults = [];

    public array $purchaseItems = [];

    public float $globalDiscount = -5;

    public string $globalDiscountType = 'fixed';

    public float $final_amount = 0;

    public $errorMessages = [];

    public function getHeading(): string
    {
        return '';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return '';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function create(bool $another = false): void
    {
        $this->authorizeAccess();

        try {
            $this->beginDatabaseTransaction();

            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeCreate($data);

            $this->callHook('beforeCreate');

            $this->record = $this->handleRecordCreation($data);

            $this->form->model($this->getRecord())->saveRelationships();

            $this->callHook('afterCreate');

            $this->commitDatabaseTransaction();
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $this->rollBackDatabaseTransaction() :
                $this->commitDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        $this->rememberData();

        $this->getCreatedNotification()?->send();

        if ($another) {
            // Ensure that the form record is anonymized so that relationships aren't loaded.
            $this->form->model($this->getRecord()::class);
            $this->record = null;

            $this->fillForm();

            return;
        }

        $redirectUrl = $this->getRedirectUrl();

        $this->redirect($redirectUrl);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validate the purchase items
        $validator = Validator::make([
            'purchaseItems' => $this->purchaseItems,
            'globalDiscount' => $this->globalDiscount,
            'globalDiscountType' => $this->globalDiscountType,
            'final_amount' => $this->final_amount,
        ], [
            'purchaseItems' => ['required', 'array', 'min:1'],
            'purchaseItems.*.id' => ['required', 'exists:products,id'],
            'purchaseItems.*.quantity' => ['required', 'numeric', 'min:1'],
            'purchaseItems.*.price' => ['required', 'numeric', 'min:0'],
            'purchaseItems.*.discount' => ['required', 'numeric', 'min:0'],
            'purchaseItems.*.discount_type' => ['required', 'in:fixed,percent'],
            'purchaseItems.*.expiry_date' => ['date', 'before:today'],
            'globalDiscount' => ['required', 'numeric', 'min:0'],
            'globalDiscountType' => ['required', 'in:fixed,percent'],
            'final_amount' => ['required', 'numeric', 'min:0'],
        ], [
            'purchaseItems.required' => 'Please add at least one product to the purchase',
            'purchaseItems.min' => 'Please add at least one product to the purchase',
            'purchaseItems.*.id.required' => 'Product ID is required',
            'purchaseItems.*.id.exists' => 'Selected product does not exist',
            'purchaseItems.*.quantity.required' => 'Quantity is required',
            'purchaseItems.*.quantity.min' => 'Quantity must be at least 1',
            'purchaseItems.*.price.required' => 'Price is required',
            'purchaseItems.*.price.min' => 'Price must be at least 0',
            'purchaseItems.*.discount.min' => 'Discount cannot be negative',
            'purchaseItems.*.expiry_date.date' => 'Expiry date must be a valid date',
            'purchaseItems.*.expiry_date.before' => 'Expiry date must be a past date',
        ]);

        if ($validator->fails()) {
            $this->errorMessages = $validator->errors()->toArray();

            // Create a notification for the main error
            Notification::make()
                ->title('Validation Error')
                ->body('Please fix the errors in the form.')
                ->danger()
                ->send();

            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        // Include the global discount and final amount in the purchase record
        $data['globalDiscount'] = $this->globalDiscount;
        $data['globalDiscountType'] = $this->globalDiscountType;
        $data['amount'] = $this->final_amount;

        return $data;
    }

    protected function afterCreate(): void
    {
        parent::afterCreate();

        // Create purchase items
        foreach ($this->purchaseItems as $item) {
            $this->record->items()->create([
                'product_id' => $item['id'],
                'purchasable_type' => Product::class,
                'purchasable_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'discount' => $item['discount'],
                'discount_type' => $item['discount_type'],
                'expiry_date' => $item['expiry_date'] ?? null,
            ]);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                ...$form->getComponents(),
                Forms\Components\TextInput::make('search')
                    ->hiddenLabel()
                    ->placeholder('Search products...')
                    ->live(debounce: 300)
                    ->extraAlpineAttributes(['autofocus' => true])
                    ->afterStateUpdated(function ($state) {
                        if (! $state || strlen($state) < 2) {
                            $this->searchResults = [];

                            return;
                        }

                        $this->searchResults = Product::search($state)
                            ->query(function ($query) {
                                $query->where('business_id', Filament::getTenant()->getKey())
                                    ->with('variants.parent');
                            })
                            ->get(['id', 'parent_id', 'name', 'price'])
                            ->flatMap(function (Product $product) {
                                if ($product->variants->isEmpty()) {
                                    return [[
                                        'id' => $product->id,
                                        'name' => $product->name,
                                        'image' => $product->getFirstMediaUrl('thumbnail'),
                                        'price' => 12,
                                    ]];
                                }

                                return $product->variants->map(function (Product $product) {
                                    return [
                                        'id' => $product->id,
                                        'name' => $product->title,
                                        'image' => $product->getFirstMediaUrl('thumbnail'),
                                        'price' => 23,
                                    ];
                                });
                            })
                            ->toArray();
                    })
                    ->columnSpanFull(),
            ]);
    }
}

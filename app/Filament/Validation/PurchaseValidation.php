<?php

namespace App\Filament\Validation;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PurchaseValidation
{
    public static function validate(array $data): array
    {
        $validator = Validator::make($data, [
            'purchaseItems' => ['required', 'array', 'min:1'],
            'purchaseItems.*.id' => ['required', 'exists:products,id'],
            'purchaseItems.*.quantity' => ['required', 'numeric', 'min:1'],
            'purchaseItems.*.price' => ['required', 'numeric', 'min:0'],
            'purchaseItems.*.discount' => ['required', 'numeric', 'min:0'],
            'purchaseItems.*.discount_type' => ['required', 'in:fixed,percent'],
            'purchaseItems.*.expiry_date' => ['nullable', 'date', 'after:today'],
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
            'purchaseItems.*.price.min' => 'Price cannot be negative',
            'purchaseItems.*.discount.required' => 'Discount is required',
            'purchaseItems.*.discount.min' => 'Discount cannot be negative',
            'purchaseItems.*.expiry_date.after' => 'Expiry date must be a future date',
            'globalDiscount.min' => 'Discount cannot be negative',
            'final_amount.min' => 'Final amount cannot be negative',
        ]);

        // Add custom validation rules
        $validator->after(function ($validator) use ($data) {
            // Validate global discount limits
            if ($data['globalDiscountType'] === 'percent' && $data['globalDiscount'] > 100) {
                $validator->errors()->add('globalDiscount', 'Discount percentage cannot exceed 100%');
            } elseif ($data['globalDiscountType'] === 'fixed') {
                $subtotal = collect($data['purchaseItems'])->reduce(function ($total, $item) {
                    return $total + ($item['price'] * $item['quantity']);
                }, 0);

                if ($data['globalDiscount'] > $subtotal) {
                    $validator->errors()->add('globalDiscount', 'Discount amount cannot exceed subtotal');
                }
            }

            // Validate individual product discount limits
            foreach ($data['purchaseItems'] as $index => $item) {
                if ($item['discount_type'] === 'percent' && $item['discount'] > 100) {
                    $validator->errors()->add("purchaseItems.{$index}.discount", 'Discount percentage cannot exceed 100%');
                } elseif ($item['discount_type'] === 'fixed' && $item['discount'] > $item['price']) {
                    $validator->errors()->add("purchaseItems.{$index}.discount", 'Discount amount cannot exceed price');
                }
            }
        });

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        return $data;
    }
}

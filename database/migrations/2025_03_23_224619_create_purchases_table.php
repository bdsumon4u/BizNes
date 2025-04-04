<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained();
            $table->foreignId('location_id')->constrained();
            $table->foreignId('supplier_id')->constrained();
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('discount')->default(0);
            $table->string('discount_type')->nullable();
            $table->unsignedInteger('additional_cost')->default(0);
            $table->text('additional_cost_note')->nullable();
            $table->unsignedInteger('total');
            $table->date('date');
            $table->timestamps();
        });

        Schema::create('product_purchase', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('purchase_id')->constrained();
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('price')->default(0);
            $table->unsignedInteger('discount')->default(0);
            $table->string('discount_type')->nullable();
            $table->date('expiry_date')->nullable()->index();
            $table->timestamps();

            $table->unique(['product_id', 'purchase_id', 'expiry_date']);
        });

        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->unsignedInteger('price')->default(0);
            $table->unsignedInteger('quantity')->default(0);
            $table->date('expiry_date')->nullable()->index();
            $table->timestamps();

            $table->unique(['location_id', 'product_id', 'expiry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
        Schema::dropIfExists('product_purchase');
        Schema::dropIfExists('purchases');
    }
};

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
            $table->unsignedInteger('amount');
            $table->date('date');
            $table->timestamps();
        });

        Schema::create('product_purchase', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('purchase_id')->constrained();
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('price');
            $table->unsignedInteger('discount')->default(0);
            $table->string('discount_type')->nullable();
            $table->date('expiry_date')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->unsignedInteger('price');
            $table->unsignedInteger('quantity');
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
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
    }
};

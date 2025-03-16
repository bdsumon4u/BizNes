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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();
            $table->foreignId('brand_id')->nullable()->constrained();
            $this->addSomeFields($table);
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_visible')->default(false);
            $table->string('type')->nullable();

            $table->shipping_v1($table);
            $table->seo_v1($table);

            $table->timestamp('published_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $this->addSomeFields($table);
            $table->unsignedInteger('position')->default(0);

            $table->shipping_v1($table);
            $table->json('metadata')->nullable();

            $table->timestamps();
        });

        Schema::create('product_has_relations', function (Blueprint $table): void {
            $table->foreignId('product_id')->constrained();
            $table->morphs('productable');
        });

        Schema::create('attribute_product', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('attribute_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('option_id')->nullable()->constrained();
            $table->text('value')->nullable();
        });

        Schema::create('option_variant', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('option_id')->constrained();
            $table->foreignId('variant_id')->constrained();
        });

        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_group_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->integer('quantity');
            $table->integer('price');

            $table->unique(['customer_group_id', 'product_id',  'quantity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_prices');
        Schema::dropIfExists('option_variant');
        Schema::dropIfExists('attribute_product');
        Schema::dropIfExists('product_has_relations');
        Schema::dropIfExists('variants');
        Schema::dropIfExists('products');
    }

    private function addSomeFields(Blueprint $table): void
    {
        $table->foreignId('business_id')->constrained();
        $table->string('name');
        $table->string('slug');
        $table->string('sku')->nullable();
        $table->string('ean')->nullable();
        $table->string('upc')->nullable();
        $table->string('barcode')->nullable();
        $table->integer('security_stock')->default(1);
        $table->boolean('allow_backorder')->default(false);

        foreach (['slug', 'sku', 'barcode', 'ean', 'upc'] as $column) {
            $table->unique(['business_id', $column]);
        }
    }
};

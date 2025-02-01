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
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained();
            $table->string('name');
            $table->string('slug');
            $table->string('type');
            $table->string('icon')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->boolean('is_searchable')->default(false);
            $table->boolean('is_filterable')->default(false);
            $table->timestamps();

            $table->unique(['business_id', 'slug']);
        });

        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained();
            $table->string('key');
            $table->string('value', 50);
            $table->unsignedSmallInteger('position')->default(0);

            $table->unique(['attribute_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};

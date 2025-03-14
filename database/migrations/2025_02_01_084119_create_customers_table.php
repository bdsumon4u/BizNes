<?php

use App\Models\CustomerGroup;
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
        Schema::create('customer_groups', function (Blueprint $table) {
            $table->id();
            // Use application level validation for one null row at most.
            $table->foreignId('business_id')->nullable()->constrained();
            $table->string('name');
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['business_id', 'name']);
        });

        CustomerGroup::create(['name' => 'Default']);

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained();
            $table->foreignId('customer_group_id')->constrained();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
        Schema::dropIfExists('customer_groups');
    }
};

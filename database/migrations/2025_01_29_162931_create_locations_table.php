<?php

use App\Models\Business;
use App\Models\Location;
use App\Models\User;
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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Business::class)->constrained();
            $table->string('name');
            $table->string('type');
            $table->text('description')->nullable();
            $table->string('street');
            $table->string('district');
            $table->string('city');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_main')->default(false);
            $table->timestamps();

            $table->unique([(new Business)->getForeignKey(), 'name']);
        });

        Schema::create('location_user', function (Blueprint $table) {
            $table->foreignIdFor(Location::class)->constrained();
            $table->foreignIdFor(User::class)->constrained();
            $table->timestamps();

            $table->unique([(new Location)->getForeignKey(), (new User)->getForeignKey()]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_user');
        Schema::dropIfExists('locations');
    }
};

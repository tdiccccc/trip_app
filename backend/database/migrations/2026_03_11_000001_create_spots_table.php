<?php

declare(strict_types=1);

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
        Schema::create('spots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address');
            $table->float('latitude')->nullable();
            $table->float('longitude')->nullable();
            $table->string('business_hours')->nullable();
            $table->string('price_info')->nullable();
            $table->string('google_maps_url')->nullable();
            $table->string('image_url')->nullable();
            $table->string('category')->default('sightseeing');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('category');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spots');
    }
};

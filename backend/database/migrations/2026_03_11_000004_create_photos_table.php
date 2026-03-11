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
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('spot_id')->nullable()->constrained('spots')->nullOnDelete();
            $table->string('storage_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('original_filename');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->text('caption')->nullable();
            $table->dateTime('taken_at')->nullable();
            $table->timestamps();

            $table->index('taken_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};

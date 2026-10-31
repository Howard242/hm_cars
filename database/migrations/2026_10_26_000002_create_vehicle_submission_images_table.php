<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_submission_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_submission_id')
                ->constrained('vehicle_submissions')
                ->cascadeOnDelete();
            $table->string('image_path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['vehicle_submission_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_submission_images');
    }
};
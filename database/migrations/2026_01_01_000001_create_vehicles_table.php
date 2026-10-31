<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('make', 80);
            $table->string('model', 100);
            $table->unsignedSmallInteger('year');
            $table->decimal('price', 12, 2);
            $table->string('location', 120);
            $table->string('transmission', 40)->nullable();
            $table->string('fuel_type', 40)->nullable();
            $table->unsignedInteger('mileage_km')->nullable();
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->string('status', 30)->default('pending')->index();

            // PRIVATE seller details: never render these fields publicly.
            $table->string('seller_name', 120)->nullable();
            $table->string('seller_phone', 40)->nullable();

            $table->timestamps();
            $table->index(['status', 'make', 'price']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};

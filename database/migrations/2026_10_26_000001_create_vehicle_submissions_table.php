<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_submissions', function (Blueprint $table) {
            $table->id();
            $table->char('tracking_token_hash', 64)->unique();
            $table->char('request_key_hash', 64)->unique();
            $table->string('status', 32)->default('pending_review')->index();
            $table->string('seller_name', 120);
            $table->string('seller_phone', 40);
            $table->string('seller_email', 190)->nullable();
            $table->string('make', 80);
            $table->string('model', 100);
            $table->unsignedSmallInteger('year');
            $table->decimal('price', 12, 2);
            $table->string('location', 120);
            $table->string('transmission', 40)->nullable();
            $table->string('fuel_type', 40)->nullable();
            $table->unsignedInteger('mileage_km')->nullable();
            $table->text('description')->nullable();
            $table->text('admin_feedback')->nullable();
            $table->text('seller_response')->nullable();
            $table->foreignId('approved_vehicle_id')
                ->nullable()
                ->constrained('vehicles')
                ->nullOnDelete();
            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_submissions');
    }
};
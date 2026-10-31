<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_submissions', function (Blueprint $table) {
            $table->timestamp('marketing_authorized_at')->nullable();
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->timestamp('marketing_authorized_at')->nullable();
        });

        Schema::create('vehicle_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('platform', 20);
            $table->timestamps();
            $table->index(['vehicle_id', 'platform', 'created_at']);
        });

        Schema::create('vehicle_promotion_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_promotion_id')
                ->constrained('vehicle_promotions')
                ->cascadeOnDelete();
            $table->timestamps();
            $table->index(['vehicle_promotion_id', 'created_at']);
        });

        Schema::table('inquiries', function (Blueprint $table) {
            $table->foreignId('vehicle_promotion_id')
                ->nullable()
                ->constrained('vehicle_promotions')
                ->nullOnDelete();
            $table->string('utm_source', 20)->nullable();
            $table->string('utm_medium', 20)->nullable();
            $table->string('utm_campaign', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('vehicle_promotion_id');
            $table->dropColumn(['utm_source', 'utm_medium', 'utm_campaign']);
        });

        Schema::dropIfExists('vehicle_promotion_visits');
        Schema::dropIfExists('vehicle_promotions');

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('marketing_authorized_at');
        });

        Schema::table('vehicle_submissions', function (Blueprint $table) {
            $table->dropColumn('marketing_authorized_at');
        });
    }
};

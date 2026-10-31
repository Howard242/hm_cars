<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_hero_slides', function (Blueprint $table) {
            $table->unsignedInteger('autoplay_seconds')->default(6)->after('sort_order');
            $table->string('transition_style', 20)->default('fade')->after('autoplay_seconds');
            $table->string('offer_badge', 80)->nullable()->after('transition_style');
            $table->foreignId('featured_vehicle_id')->nullable()->after('offer_badge')->constrained('vehicles')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('home_hero_slides', function (Blueprint $table) {
            $table->dropConstrainedForeignId('featured_vehicle_id');
            $table->dropColumn(['autoplay_seconds', 'transition_style', 'offer_badge']);
        });
    }
};

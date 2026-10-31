<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow', 120);
            $table->string('title', 180);
            $table->string('highlight', 180)->nullable();
            $table->string('description', 500);
            $table->string('primary_label', 80)->default('Explore cars');
            $table->string('primary_url', 255)->nullable();
            $table->string('secondary_label', 80)->nullable();
            $table->string('secondary_url', 255)->nullable();
            $table->string('image_path', 255)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('home_hero_slides')->insert([
            [
                'eyebrow' => 'YOUR CAR. YOUR NEXT CHAPTER.', 'title' => 'Find the right car.', 'highlight' => "We'll help you connect.",
                'description' => 'Explore quality listings across Kenya and let our team guide you from first inquiry to viewing.',
                'primary_label' => 'Explore cars', 'primary_url' => '/vehicles', 'secondary_label' => 'How it works', 'secondary_url' => '/#how-it-works',
                'sort_order' => 1, 'is_published' => true, 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'eyebrow' => 'DRIVE MORE. WORRY LESS.', 'title' => 'Find a car that fits your life.', 'highlight' => 'We coordinate every step.',
                'description' => 'Choose your budget, choose your dream, and get personal help checking availability, arranging viewings and making the next move.',
                'primary_label' => 'Browse available cars', 'primary_url' => '/vehicles', 'secondary_label' => 'Tell us what you need', 'secondary_url' => '/#inquiry-form',
                'sort_order' => 2, 'is_published' => true, 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'eyebrow' => 'SELL SMARTER WITH HM CARS', 'title' => 'Ready to sell your vehicle?', 'highlight' => 'We help it reach the right buyer.',
                'description' => 'Submit your car for review, receive guided support, and only share private seller details through the HM CARS team.',
                'primary_label' => 'Sell your car', 'primary_url' => '/sell-your-car', 'secondary_label' => 'How we sell for you', 'secondary_url' => '/sell-your-car',
                'sort_order' => 3, 'is_published' => true, 'created_at' => $now, 'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('home_hero_slides');
    }
};

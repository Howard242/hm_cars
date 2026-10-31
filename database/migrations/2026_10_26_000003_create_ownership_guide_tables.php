<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ownership_guides', function (Blueprint $table) {
            $table->id();
            $table->string('title', 180);
            $table->text('introduction');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('ownership_guide_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ownership_guide_id')
                ->constrained('ownership_guides')
                ->cascadeOnDelete();
            $table->string('title', 180);
            $table->text('body');
            $table->string('image_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->index(['ownership_guide_id', 'sort_order']);
        });

        $now = now();
        $guideId = DB::table('ownership_guides')->insertGetId([
            'title' => 'How to Own a Car in Kenya',
            'introduction' => 'Buying a car is a major decision. Use this guide to plan your purchase, check the vehicle and understand the steps to take before driving away. Requirements can change, so confirm current procedures with the relevant Kenyan authorities and your insurer.',
            'is_published' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $steps = [
            [
                'title' => 'Plan your full budget',
                'body' => 'Set a purchase budget and allow for costs beyond the sale price, such as an independent inspection, insurance, transfer-related charges, fuel and maintenance. Ask for a written breakdown before committing.',
            ],
            [
                'title' => 'Check the vehicle and seller',
                'body' => 'Compare the vehicle with its records, including registration and identifying details. Confirm that the seller is the registered owner or is properly authorised to sell. Check available ownership and vehicle information using current official NTSA or eCitizen services.',
            ],
            [
                'title' => 'Arrange an independent inspection',
                'body' => 'Have a qualified mechanic inspect the vehicle before you pay. Where possible, view it in person, verify its condition and history, and take a careful test drive with the owner’s permission.',
            ],
            [
                'title' => 'Agree the sale in writing',
                'body' => 'Record the buyer and seller details, vehicle identifiers, agreed price, condition, payment terms and handover date in a signed sale agreement. Keep proof of payment and avoid sending money until you have verified the vehicle and seller.',
            ],
            [
                'title' => 'Complete the official ownership transfer',
                'body' => 'Follow the current NTSA/eCitizen vehicle-transfer process with the registered owner. Confirm the required documents, steps and charges through official channels, and keep the transfer confirmation and updated ownership records.',
            ],
            [
                'title' => 'Arrange insurance before use',
                'body' => 'Speak with a licensed insurer or intermediary about cover appropriate for the vehicle and your needs. Confirm that the cover is active and understand its terms before using the car on public roads.',
            ],
            [
                'title' => 'Keep records and stay up to date',
                'body' => 'Keep copies of the sale agreement, payment receipt, ownership records, insurance documents, inspection reports and service history. Check official guidance for any renewals or other requirements that apply to your vehicle.',
            ],
        ];

        foreach ($steps as $index => $step) {
            DB::table('ownership_guide_steps')->insert([
                'ownership_guide_id' => $guideId,
                'title' => $step['title'],
                'body' => $step['body'],
                'sort_order' => $index + 1,
                'is_published' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ownership_guide_steps');
        Schema::dropIfExists('ownership_guides');
    }
};

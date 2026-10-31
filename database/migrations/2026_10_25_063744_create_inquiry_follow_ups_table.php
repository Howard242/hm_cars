
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiry_follow_ups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inquiry_id')
                ->constrained('inquiries')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('contact_method', 30);
            $table->text('notes');

            $table->dateTime('next_follow_up_at')
                ->nullable();

            $table->timestamps();

            $table->index('next_follow_up_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiry_follow_ups');
    }
};

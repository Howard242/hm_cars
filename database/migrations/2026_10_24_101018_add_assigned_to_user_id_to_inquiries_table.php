<?php

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
    Schema::table('inquiries', function (Blueprint $table) {
        $table->foreignId('assigned_to_user_id')
            ->nullable()
            ->after('vehicle_id')
            ->constrained('users')
            ->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('inquiries', function (Blueprint $table) {
        $table->dropForeign(['assigned_to_user_id']);
        $table->dropColumn('assigned_to_user_id');
    });
}
};

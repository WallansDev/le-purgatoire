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
        Schema::table('interventions', function (Blueprint $table) {
            $table->boolean('is_completed')->default(false)->after('finished_at');
            $table->text('non_completion_reason')->nullable()->after('is_completed');
            $table->text('notes')->nullable()->after('non_completion_reason');
            $table->text('client_comments')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interventions', function (Blueprint $table) {
            $table->dropColumn(['is_completed', 'non_completion_reason', 'notes', 'client_comments']);
        });
    }
};

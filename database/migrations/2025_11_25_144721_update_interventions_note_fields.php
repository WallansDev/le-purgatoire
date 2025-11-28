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
            if (Schema::hasColumn('interventions', 'rating')) {
                $table->renameColumn('rating', 'note');
            }

            if (Schema::hasColumn('interventions', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interventions', function (Blueprint $table) {
            if (! Schema::hasColumn('interventions', 'notes')) {
                $table->text('notes')->nullable();
            }

            if (Schema::hasColumn('interventions', 'note')) {
                $table->renameColumn('note', 'rating');
            }
        });
    }
};

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
            // Rendre le technicien optionnel
            $table->dropForeign(['technician_id']);
            $table->foreignId('technician_id')->nullable()->change();
            $table->foreign('technician_id')->references('id')->on('technicians')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interventions', function (Blueprint $table) {
            $table->dropForeign(['technician_id']);
            $table->foreignId('technician_id')->nullable(false)->change();
            $table->foreign('technician_id')->references('id')->on('technicians')->cascadeOnDelete();
        });
    }
};


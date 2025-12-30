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
        Schema::table('groups', function (Blueprint $table) {
            // Supprimer toutes les colonnes de permissions
            $table->dropColumn([
                'companies_read',
                'companies_write',
                'companies_delete',
                'technicians_read',
                'technicians_write',
                'technicians_delete',
                'interventions_read',
                'interventions_write',
                'interventions_delete',
                'organizations_read',
                'organizations_write',
                'organizations_delete',
                'groups_read',
                'groups_write',
                'groups_delete',
                'can_invite',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            // Recréer toutes les colonnes de permissions
            $table->boolean('companies_read')->default(false);
            $table->boolean('companies_write')->default(false);
            $table->boolean('companies_delete')->default(false);
            $table->boolean('technicians_read')->default(false);
            $table->boolean('technicians_write')->default(false);
            $table->boolean('technicians_delete')->default(false);
            $table->boolean('interventions_read')->default(false);
            $table->boolean('interventions_write')->default(false);
            $table->boolean('interventions_delete')->default(false);
            $table->boolean('organizations_read')->default(false);
            $table->boolean('organizations_write')->default(false);
            $table->boolean('organizations_delete')->default(false);
            $table->boolean('groups_read')->default(false);
            $table->boolean('groups_write')->default(false);
            $table->boolean('groups_delete')->default(false);
            $table->boolean('can_invite')->default(false);
        });
    }
};

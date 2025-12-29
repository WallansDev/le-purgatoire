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
            // Supprimer les anciennes permissions globales
            $table->dropColumn(['can_read', 'can_write', 'can_delete']);
            
            // Permissions pour Companies
            $table->boolean('companies_read')->default(false)->after('is_default');
            $table->boolean('companies_write')->default(false)->after('companies_read');
            $table->boolean('companies_delete')->default(false)->after('companies_write');
            
            // Permissions pour Technicians
            $table->boolean('technicians_read')->default(false)->after('companies_delete');
            $table->boolean('technicians_write')->default(false)->after('technicians_read');
            $table->boolean('technicians_delete')->default(false)->after('technicians_write');
            
            // Permissions pour Interventions
            $table->boolean('interventions_read')->default(false)->after('technicians_delete');
            $table->boolean('interventions_write')->default(false)->after('interventions_read');
            $table->boolean('interventions_delete')->default(false)->after('interventions_write');
            
            // Permissions pour Organizations
            $table->boolean('organizations_read')->default(false)->after('interventions_delete');
            $table->boolean('organizations_write')->default(false)->after('organizations_read');
            $table->boolean('organizations_delete')->default(false)->after('organizations_write');
            
            // Permissions pour Groups
            $table->boolean('groups_read')->default(false)->after('organizations_delete');
            $table->boolean('groups_write')->default(false)->after('groups_read');
            $table->boolean('groups_delete')->default(false)->after('groups_write');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            // Supprimer les nouvelles permissions granulaires
            $table->dropColumn([
                'companies_read', 'companies_write', 'companies_delete',
                'technicians_read', 'technicians_write', 'technicians_delete',
                'interventions_read', 'interventions_write', 'interventions_delete',
                'organizations_read', 'organizations_write', 'organizations_delete',
                'groups_read', 'groups_write', 'groups_delete',
            ]);
            
            // Restaurer les anciennes permissions globales
            $table->boolean('can_read')->default(false)->after('is_default');
            $table->boolean('can_write')->default(false)->after('can_read');
            $table->boolean('can_delete')->default(false)->after('can_write');
        });
    }
};

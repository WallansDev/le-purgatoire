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
        Schema::table('tags', function (Blueprint $table) {
            // Supprimer l'index unique sur name
            $table->dropUnique(['name']);
            
            // Ajouter la colonne organization_id
            $table->foreignId('organization_id')
                ->nullable()
                ->after('id')
                ->constrained('organizations')
                ->cascadeOnDelete();
            
            // Créer un index unique sur la combinaison name + organization_id
            $table->unique(['name', 'organization_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            // Supprimer l'index unique sur name + organization_id
            $table->dropUnique(['name', 'organization_id']);
            
            // Supprimer la colonne organization_id
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
            
            // Restaurer l'index unique sur name
            $table->unique('name');
        });
    }
};

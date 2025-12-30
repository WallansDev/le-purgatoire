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
        Schema::create('group_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->enum('scope', ['global', 'organization', 'group'])->default('organization');
            // scope définit la portée de la permission :
            // - global : permission sur toutes les ressources du type
            // - organization : permission sur les ressources de l'organisation du groupe
            // - group : permission limitée au groupe lui-même (pour certaines actions spécifiques)
            $table->timestamps();

            $table->unique(['group_id', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_permissions');
    }
};

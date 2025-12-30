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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ex: 'companies.read', 'groups.write', 'organizations.delete'
            $table->string('display_name'); // ex: 'Lire les entreprises', 'Modifier les groupes'
            $table->string('resource'); // 'companies', 'technicians', 'interventions', 'organizations', 'groups', 'users'
            $table->string('action'); // 'read', 'write', 'delete', 'invite'
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['resource', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};

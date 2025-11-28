<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('phone')->nullable()->after('email');
            $table->boolean('is_admin')->default(false)->after('email_verified_at');
            $table->boolean('must_change_password')->default(false)->after('is_admin');
        });

        DB::table('users')->select('id', 'name')->orderBy('id')->lazyById()->each(function ($user) {
            [$first, $last] = array_pad(explode(' ', $user->name, 2), 2, null);

            DB::table('users')->where('id', $user->id)->update([
                'first_name' => $first,
                'last_name' => $last,
            ]);
        });

        $firstUser = DB::table('users')->orderBy('id')->first();

        if ($firstUser) {
            DB::table('users')->where('id', $firstUser->id)->update([
                'is_admin' => true,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'phone', 'is_admin', 'must_change_password']);
        });
    }
};

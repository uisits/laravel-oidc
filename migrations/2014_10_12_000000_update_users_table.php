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
        Schema::table('users', function (Blueprint $table) {
            $table->string('netid')->after('id')->unique();
            $table->string('first_name')->after('name');
            $table->string('last_name')->after('first_name');
            $table->string('uin', 9)->unique()->after('last_name');
            $table->string('token', 600)->nullable();
            $table->string('remember_token', 600)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(array_merge([
                'netid',
                'first_name',
                'last_name',
                'uin',
                'token',
            ], []));
        });
    }
};

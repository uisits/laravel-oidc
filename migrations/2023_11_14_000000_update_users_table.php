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
            if(!Schema::hasColumn('users', 'netid')) {
                $table->string('netid')->after('id');
            }

            if(!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->after('name');
            }
            if(!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->after('first_name');
            }
            if(!Schema::hasColumn('users', 'uin')) {
                $table->string('uin', 9)->after('netid')->unique();
            }

            $table->longText('access_token')->nullable()->after('password');
            $table->longText('id_token')->nullable()->after('access_token');
            $table->longText('refresh_token')->nullable()->after('id_token');
            $table->dropColumn('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('refresh_token', 'remember_token');
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

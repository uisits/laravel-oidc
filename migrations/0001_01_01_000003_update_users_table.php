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
                $table->string('netid', 20)->after('id')->unique();
            }
            if(!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name', 100)->after('name');
            }
            if(!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name', 100)->after('first_name');
            }
            if(!Schema::hasColumn('users', 'preferred_first_name')) {
                $table->string('preferred_first_name', 100)->nullable()->after('last_name');
            }
            if(!Schema::hasColumn('users', 'uin')) {
                $table->string('uin', 9)->unique()->after('preferred_first_name');
            }
            if(!Schema::hasColumn('users', 'access_token')) {
                $table->longText('access_token')->nullable()->after('password');
            }
            if(!Schema::hasColumn('users', 'id_token')) {
                $table->longText('id_token')->nullable()->after('access_token');
            }
            if(!Schema::hasColumn('users', 'refresh_token')) {
                $table->longText('refresh_token')->nullable()->after('id_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'netid',
                'first_name',
                'last_name',
                'uin',
                'access_token',
                'id_token',
                'refresh_token',];
            if(Schema::hasColumns('users', $columns)) {
                $table->dropColumn(array_merge($columns, []));
            }
        });
    }
};

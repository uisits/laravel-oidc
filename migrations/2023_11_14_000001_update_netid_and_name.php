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
            $this->updateNetid();
            $this->updateName();
            $table->unique('netid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_netid_unique');
        });
    }

    /**
     * @return bool
     */
    protected function updateNetid(): bool
    {
        return DB::connection()->statement('UPDATE `users` SET `netid` = `name`');
    }

    /**
     * @return bool
     */
    protected function updateName(): bool
    {
        return DB::connection()->statement('UPDATE `users` SET `name` = CONCAT(`first_name`, " ", `last_name`)');
    }
};

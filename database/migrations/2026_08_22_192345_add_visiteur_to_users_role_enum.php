<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddVisiteurToUsersRoleEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','vendeur','visiteur') NOT NULL DEFAULT 'visiteur'");
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','vendeur') NOT NULL DEFAULT 'vendeur'");
    }
}

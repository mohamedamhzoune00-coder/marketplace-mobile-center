<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeHorairesHeuresNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE horaires_boutique MODIFY heure_ouverture TIME NULL");
        DB::statement("ALTER TABLE horaires_boutique MODIFY heure_fermeture TIME NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE horaires_boutique MODIFY heure_ouverture TIME NOT NULL");
        DB::statement("ALTER TABLE horaires_boutique MODIFY heure_fermeture TIME NOT NULL");
    }
}

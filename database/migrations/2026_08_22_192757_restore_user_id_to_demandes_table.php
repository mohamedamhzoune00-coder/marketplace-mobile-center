<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RestoreUserIdToDemandesTable extends Migration
{
    public function up()
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->dropColumn('token');
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->uuid('token')->unique()->nullable()->after('id');
        });
    }
}

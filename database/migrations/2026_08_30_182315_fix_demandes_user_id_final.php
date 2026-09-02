<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixDemandesUserIdFinal extends Migration
{
    public function up()
    {
        // n7ido token ghi ila mazal kayn
        if (Schema::hasColumn('demandes', 'token')) {
            Schema::table('demandes', function (Blueprint $table) {
                $table->dropColumn('token');
            });
        }

        // nzido user_id ghi ila mazal machi kayn
        if (!Schema::hasColumn('demandes', 'user_id')) {
            Schema::table('demandes', function (Blueprint $table) {
                $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        // ma khasnach n rollback had l fix - khaliha khawya
    }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToDemandesTable extends Migration
{
    public function up()
    {
        Schema::table('demandes', function (Blueprint $table) {
            // ghi ila l3amoud machi kayn, khal9o kaml (b constraint)
            if (!Schema::hasColumn('demandes', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        // khaliha khawya - migrations lokhrin howa li kaydiro l fix
    }
}
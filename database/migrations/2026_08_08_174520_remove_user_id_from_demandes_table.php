<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUserIdFromDemandesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
{
    Schema::table('demandes', function (Blueprint $table) {
        // نحيدو أولاً الـ Foreign Key (خاص تتحيد قبل العمود نفسو)
        $table->dropForeign(['user_id']);
        // منبعد نحيدو العمود
        $table->dropColumn('user_id');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
   public function down()
{
    Schema::table('demandes', function (Blueprint $table) {
        $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
    });
}
}

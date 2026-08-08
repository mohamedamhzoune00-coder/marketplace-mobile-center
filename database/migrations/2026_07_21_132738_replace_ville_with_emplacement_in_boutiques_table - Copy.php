<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReplaceVilleWithEmplacementInBoutiquesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boutiques', function (Blueprint $table) {

            // إضافة عنوان البوتيك داخل المركز التجاري
            $table->string('emplacement')->after('adresse');

            // حذف عمود المدينة لأنه المشروع كامل داخل مكناس
            $table->dropColumn('ville');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boutiques', function (Blueprint $table) {

            // نرجعو عمود المدينة إذا درنا rollback
            $table->string('ville')->after('adresse');

            // نحذف emplacement
            $table->dropColumn('emplacement');

        });
    }
}
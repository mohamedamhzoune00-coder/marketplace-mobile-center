<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatistiquesVues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
   {
    Schema::create('statistiques_vues', function (Blueprint $table) {

        // المفتاح الأساسي
        $table->id();

        // المنتج الذي تخصه الإحصائية
        $table->foreignId('produit_id')
              ->constrained('produits')
              ->onDelete('cascade');

        // تاريخ الإحصائية
        $table->date('date');

        // عدد المشاهدات في هذا اليوم
        $table->unsignedInteger('nombre_vues')->default(0);

        // تاريخ الإنشاء وآخر تعديل
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statistiques_vues');
    }
}

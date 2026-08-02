<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesProduitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
   {
    Schema::create('images_produits', function (Blueprint $table) {

        // المفتاح الأساسي
        $table->id();

        // المنتج الذي تنتمي إليه الصورة
        $table->foreignId('produit_id')
              ->constrained('produits')
              ->onDelete('cascade');

        // مسار الصورة
        $table->string('chemin');

        // هل هي الصورة الرئيسية؟
        $table->boolean('principale')->default(false);

        // ترتيب عرض الصور
        $table->integer('ordre')->default(0);

        // تاريخ الإنشاء والتعديل
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
        Schema::dropIfExists('images_produits');
    }
}

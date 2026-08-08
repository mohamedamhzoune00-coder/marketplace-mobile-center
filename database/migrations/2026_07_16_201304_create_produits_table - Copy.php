<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProduitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
 {
    Schema::create('produits', function (Blueprint $table) {

        // المفتاح الأساسي
        $table->id();

        // المتجر الذي يملك المنتج
        $table->foreignId('boutique_id')
              ->constrained()
              ->onDelete('cascade');

        // الفئة التي ينتمي إليها المنتج
        $table->foreignId('category_id')
              ->constrained('categories')
              ->onDelete('cascade');

        // اسم المنتج
        $table->string('nom');

        // وصف المنتج
        $table->text('description')->nullable();

        // السعر
        $table->decimal('prix', 10, 2);

        // الكمية المتوفرة
        $table->integer('stock')->default(0);

        // العلامة التجارية
        $table->string('marque')->nullable();

        // رقم الموديل
        $table->string('modele')->nullable();

        // هل المنتج متوفر؟
        $table->boolean('disponible')->default(true);

        // عدد المشاهدات
        $table->unsignedInteger('vues')->default(0);

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
        Schema::dropIfExists('produits');
    }
}

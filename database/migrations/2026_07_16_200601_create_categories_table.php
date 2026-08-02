<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
   {
    Schema::create('categories', function (Blueprint $table) {

        // المفتاح الأساسي
        $table->id();

        // اسم الفئة
        $table->string('nom');

        // وصف الفئة
        $table->text('description')->nullable();

        // الفئة الأب (إذا كانت هذه فئة فرعية)
        $table->foreignId('parent_id')
              ->nullable()
              ->constrained('categories')
              ->onDelete('cascade');

        // ترتيب عرض الفئات
        $table->integer('ordre')->default(0);

        // هل الفئة مفعلة؟
        $table->boolean('actif')->default(true);

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
        Schema::dropIfExists('categories');
    }
}

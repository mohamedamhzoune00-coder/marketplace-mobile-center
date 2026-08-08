<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoutiquesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    Schema::create('boutiques', function (Blueprint $table) {

        // المفتاح الأساسي
        $table->id();

        // البائع صاحب المتجر
        $table->foreignId('user_id')
              ->constrained()
              ->onDelete('cascade');

        // اسم المتجر
        $table->string('nom');

        // وصف المتجر
        $table->text('description')->nullable();

        // رقم الهاتف
        $table->string('telephone', 20);

        // البريد الإلكتروني
        $table->string('email')->nullable();

        // العنوان
        $table->string('adresse');

        // المدينة
        $table->string('ville');

        // شعار المتجر
        $table->string('logo')->nullable();

        // صورة الغلاف
        $table->string('couverture')->nullable();

        // هل المتجر نشط؟
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
        Schema::dropIfExists('boutiques');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJournauxAuditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
   {
    Schema::create('journaux_audit', function (Blueprint $table) {

        // المفتاح الأساسي
        $table->id();

        // المستخدم الذي قام بالعملية
        $table->foreignId('user_id')
              ->nullable()
              ->constrained()
              ->nullOnDelete();

        // اسم العملية
        $table->string('action');

        // اسم الجدول المتأثر
        $table->string('table_concernee');

        // معرف السجل المتأثر
        $table->unsignedBigInteger('record_id')->nullable();

        // تفاصيل إضافية
        $table->text('details')->nullable();

        // عنوان IP
        $table->string('ip_address', 45)->nullable();

        // معلومات المتصفح
        $table->text('user_agent')->nullable();

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
        Schema::dropIfExists('journaux_audit');
    }
}

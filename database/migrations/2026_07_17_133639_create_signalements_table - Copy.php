<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSignalementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    Schema::create('signalements', function (Blueprint $table) {

        // المفتاح الأساسي
        $table->id();

        // المستخدم الذي قام بالإبلاغ
        $table->foreignId('user_id')
              ->constrained()
              ->onDelete('cascade');

        // المنتج المُبلغ عنه
        $table->foreignId('produit_id')
              ->constrained('produits')
              ->onDelete('cascade');

        // سبب البلاغ
        $table->text('raison');

        // حالة البلاغ
        $table->enum('statut', ['en_attente', 'accepte', 'refuse'])
              ->default('en_attente');

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
        Schema::dropIfExists('signalements');
    }
}

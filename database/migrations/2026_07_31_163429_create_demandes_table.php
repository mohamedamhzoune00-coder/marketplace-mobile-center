<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemandesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demandes', function (Blueprint $table) {

            // المفتاح الأساسي
            $table->id();

            // المنتج المطلوب
            $table->foreignId('produit_id')
                  ->constrained('produits')
                  ->onDelete('cascade');

            // البوتيك ديال المنتج
            $table->foreignId('boutique_id')
                  ->constrained('boutiques')
                  ->onDelete('cascade');

            // معلومات الزبون
            $table->string('nom_client');

            $table->string('telephone',20);

            $table->string('email')->nullable();

            // الكمية
            $table->integer('quantite')->default(1);

            // رسالة اختيارية
            $table->text('message')->nullable();

            // حالة الطلب
            $table->enum('statut',[
                'en_attente',
                'acceptee',
                'refusee'
            ])->default('en_attente');

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
        Schema::dropIfExists('demandes');
    }
}
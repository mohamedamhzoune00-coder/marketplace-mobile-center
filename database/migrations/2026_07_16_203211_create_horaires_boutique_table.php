<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHorairesBoutiqueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
  {
    Schema::create('horaires_boutique', function (Blueprint $table) {

        // المفتاح الأساسي
        $table->id();

        // المتجر
        $table->foreignId('boutique_id')
              ->constrained('boutiques')
              ->onDelete('cascade');

        // اليوم
        $table->string('jour');

        // وقت الفتح
        $table->time('heure_ouverture');

        // وقت الإغلاق
        $table->time('heure_fermeture');

        // هل المتجر مغلق في هذا اليوم؟
        $table->boolean('ferme')->default(false);

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
        Schema::dropIfExists('horaires_boutique');
    }
}

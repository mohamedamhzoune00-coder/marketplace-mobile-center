<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
    Schema::table('users', function (Blueprint $table) {

        // دور المستخدم
        $table->enum('role', ['super_admin', 'vendeur'])
              ->default('vendeur')
              ->after('email');

        // رقم الهاتف
        $table->string('telephone', 20)
              ->nullable()
              ->after('role');

        // هل الحساب مفعل؟
        $table->boolean('actif')
              ->default(true)
              ->after('telephone');

    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    Schema::table('users', function (Blueprint $table) {

        $table->dropColumn([
            'role',
            'telephone',
            'actif'
        ]);

    });
}
}

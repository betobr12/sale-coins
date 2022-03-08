<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table)
        {
            $table->string('cpf_cnpj',14)->nullable()->default(null);
            $table->integer('access_level')->nullable()->default(null);
            $table->string('uuid',255)->nullable()->default(null);
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
            $table->dropColumn('cpf_cnpj');
            $table->dropColumn('access_level');
            $table->dropColumn('uuid');
        });
    }
};

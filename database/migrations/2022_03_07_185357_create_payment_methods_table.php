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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid',255)->nullable()->default(null);
            $table->string('description',255)->nullable()->default(null);
            $table->unsignedBigInteger('tax_id')->nullable()->default(null);
            $table->foreign('tax_id')->references('id')->on('taxes')->onDelete('set null');
            $table->timestamps();
            $table->datetime('deleted_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
};

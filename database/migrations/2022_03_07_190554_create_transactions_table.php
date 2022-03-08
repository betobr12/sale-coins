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
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid',255)->nullable()->default(null);
            $table->unsignedBigInteger('user_id')->nullable()->default(null);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('status_id')->nullable()->default(null);
            $table->foreign('status_id')->references('id')->on('status')->onDelete('set null');
            $table->unsignedBigInteger('payment_method_id')->nullable()->default(null);
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('set null');
            $table->unsignedBigInteger('currency_id')->nullable()->default(null);
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');
            $table->float('value')->nullable()->default(null);
            $table->float('net_value')->nullable()->default(null);
            $table->float('tax_payment')->nullable()->default(null);
            $table->float('tax_conversion')->nullable()->default(null);
            $table->float('currency_amount')->nullable()->default(null);
            $table->datetime('confirmad_date_at')->nullable()->default(null);
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
        Schema::dropIfExists('transactions');
    }
};

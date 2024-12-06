<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('session_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->integer('qty');
            $table->foreign('client_id')->references('id')->on('distributors');
            $table->foreign('event_id')->references('id')->on('events');
            $table->foreign('session_id')->references('id')->on('sessions');
            $table->foreign('product_id')->references('id')->on('products');
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
        Schema::dropIfExists('charts');
    }
}

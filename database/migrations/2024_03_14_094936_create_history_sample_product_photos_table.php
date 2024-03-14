<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistorySampleProductPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_sample_product_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_product_id');
            $table->unsignedBigInteger('history_sample_product_id')->nullable();
            $table->string('status');
            $table->integer('sequence')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();

            $table->foreign('sample_product_id')->references('id')->on('sample_products');
            $table->foreign('history_sample_product_id')->references('id')->on('history_sample_products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history_sample_product_photos');
    }
}

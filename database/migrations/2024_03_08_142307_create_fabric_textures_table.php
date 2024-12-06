<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFabricTexturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fabric_textures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_product_id');
            $table->integer('sequence');
            $table->string('description');
            $table->string('photo');
            $table->timestamps();

            $table->foreign('sample_product_id')->references('id')->on('sample_products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fabric_textures');
    }
}

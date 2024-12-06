<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryFabricTexturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_fabric_textures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_product_id');
            $table->unsignedBigInteger('fabric_texture_id');
            $table->unsignedBigInteger('history_sample_product_id')->nullable();
            $table->string('status');
            $table->integer('sequence');
            $table->string('description');
            $table->string('photo');
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
        Schema::dropIfExists('history_fabric_textures');
    }
}

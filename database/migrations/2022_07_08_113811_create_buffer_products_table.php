<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBufferProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buffer_products', function (Blueprint $table) {
            $table->id();
            $table->integer('clothes_id')->constrained('clothes');
            $table->integer('size_id')->constrained('sizes');
            $table->integer('qty_avaliable')->nullable();
            $table->integer('qty_process')->default(0);
            $table->integer('qty_buffer')->default(0);
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
        Schema::dropIfExists('buffer_products');
    }
}

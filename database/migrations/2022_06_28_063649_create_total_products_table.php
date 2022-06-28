<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTotalProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('total_products', function (Blueprint $table) {
            $table->id();
            $table->integer('clothes_id')->constrained('clothes');
            $table->boolean('veil')->default(0);
            $table->integer('size_s')->default(0);
            $table->integer('size_m')->default(0);
            $table->integer('size_l')->default(0);
            $table->integer('size_xl')->default(0);
            $table->integer('size_xxl')->default(0);
            $table->integer('size_xxxl')->default(0);
            $table->integer('size_2')->default(0);
            $table->integer('size_4')->default(0);
            $table->integer('size_6')->default(0);
            $table->integer('size_8')->default(0);
            $table->integer('size_10')->default(0);
            $table->integer('size_12')->default(0);
            $table->integer('total')->default(0);
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
        Schema::dropIfExists('total_products');
    }
}

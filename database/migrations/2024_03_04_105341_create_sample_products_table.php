<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSampleProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sample_products', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('article_name', 100);
            $table->string('material', 60);
            $table->string('size', 70);
            $table->string('accessories', 80);
            $table->unsignedBigInteger('designer_id')->nullable();
            $table->boolean('designer_signature')->nullable();
            $table->unsignedBigInteger('md_id')->nullable();
            $table->boolean('md_signature')->nullable();
            $table->unsignedBigInteger('leader_designer_id')->nullable();
            $table->boolean('leader_designer_signature')->nullable();
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
        Schema::dropIfExists('sample_products');
    }
}

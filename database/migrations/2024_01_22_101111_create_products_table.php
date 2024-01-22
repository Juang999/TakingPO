<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('entity_name');
            $table->string('article_name');
            $table->string('color');
            $table->string('material');
            $table->text('combo')->nullable();
            $table->string('special_feature');
            $table->string('keyword');
            $table->text('description')->nullable();
            $table->string('slug');
            $table->integer('group_article')->nullable();
            $table->string('category')->nullable();
            $table->unsignedBigInteger('type_id')->nullable()->constrained('types');
            $table->boolean('is_active')->default(true);
            $table->foreign('type_id')->references('id')->on('types');
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
        Schema::dropIfExists('products');
    }
}

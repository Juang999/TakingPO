<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClothesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clothes', function (Blueprint $table) {
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
            $table->integer('type_id')->nullable()->constrained('types');
            $table->boolean('is_active')->default(true);
            $table->integer('size_s')->nullable()->default(0);
            $table->integer('size_m')->nullable()->default(0);
            $table->integer('size_l')->nullable()->default(0);
            $table->integer('size_xl')->nullable()->default(0);
            $table->integer('size_xxl')->nullable()->default(0);
            $table->integer('size_xxxl')->nullable()->default(0);
            $table->string('size_2')->nullable()->default(0);
            $table->string('size_4')->nullable()->default(0);
            $table->string('size_6')->nullable()->default(0);
            $table->string('size_8')->nullable()->default(0);
            $table->string('size_10')->nullable()->default(0);
            $table->string('size_12')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clothes');
    }
}

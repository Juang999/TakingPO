<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FourthUpdateChartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('charts', function (Blueprint $table) {
            $table->integer('size_14')->after('size_12')->nullable();
            $table->integer('size_16')->after('size_14')->nullable();
            $table->integer('size_XXS')->after('product_id')->nullable();
            $table->integer('size_XS')->after('size_XXS')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdditionColumnHistorySampleProductPhotoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('history_sample_product_photos', function (Blueprint $table) {
            $table->unsignedBigInteger('hs_photo_id')->after('history_sample_product_id');

            $table->foreign('hs_photo_id')->on('sample_product_photos')->references('id');
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

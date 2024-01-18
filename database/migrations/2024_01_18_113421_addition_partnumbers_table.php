<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdditionPartnumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partnumbers', function (Blueprint $table) {
            $table->unsignedBigInteger('clothes_id')->unique()->change();
            $table->foreign('clothes_id')->references('id')->on('clothes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('partnumbers');
    }
}

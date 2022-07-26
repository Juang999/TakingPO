<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalClothesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clothes', function (Blueprint $table) {
            $table->string('size_27')->default(0);
            $table->string('size_28')->default(0);
            $table->string('size_29')->default(0);
            $table->string('size_30')->default(0);
            $table->string('size_31')->default(0);
            $table->string('size_32')->default(0);
            $table->string('size_33')->default(0);
            $table->string('size_34')->default(0);
            $table->string('size_35')->default(0);
            $table->string('size_36')->default(0);
            $table->string('size_37')->default(0);
            $table->string('size_38')->default(0);
            $table->string('size_39')->default(0);
            $table->string('size_40')->default(0);
            $table->string('size_41')->default(0);
            $table->string('size_42')->default(0);
            $table->string('other')->default(0);
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

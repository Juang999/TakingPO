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
            $table->string('size_27')->nullable()->default(0);
            $table->string('size_28')->nullable()->default(0);
            $table->string('size_29')->nullable()->default(0);
            $table->string('size_30')->nullable()->default(0);
            $table->string('size_31')->nullable()->default(0);
            $table->string('size_32')->nullable()->default(0);
            $table->string('size_33')->nullable()->default(0);
            $table->string('size_34')->nullable()->default(0);
            $table->string('size_35')->nullable()->default(0);
            $table->string('size_36')->nullable()->default(0);
            $table->string('size_37')->nullable()->default(0);
            $table->string('size_38')->nullable()->default(0);
            $table->string('size_39')->nullable()->default(0);
            $table->string('size_40')->nullable()->default(0);
            $table->string('size_41')->nullable()->default(0);
            $table->string('size_42')->nullable()->default(0);
            $table->string('other')->nullable()->default(0);
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

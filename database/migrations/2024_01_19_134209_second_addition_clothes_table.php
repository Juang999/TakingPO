<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SecondAdditionClothesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clothes', function (Blueprint $table) {
            $table->unsignedBigInteger('price')->nullable()->after('is_active');
            $table->dropColumn(['size_27','size_28','size_29','size_30','size_31','size_32','size_33','size_34','size_35','size_36','size_37','size_38','size_39','size_40','size_41','size_42','other','size_s','size_m','size_l','size_xl','size_xxl','size_xxxl','size_2','size_4','size_6','size_8','size_10','size_12']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('clothes');
    }
}

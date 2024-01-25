<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnsChartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('charts', function (Blueprint $table) {
            $table->dropColumn(['S','M','L','XL','XXL','XXXL','2','4','6','8','10','12','27','28','29','30','31','32','33','34','35','36','37','38','39','40','41','42']);
            $table->integer('size_S')->after('product_id');
            $table->integer('size_M')->after('size_S');
            $table->integer('size_L')->after('size_M');
            $table->integer('size_XL')->after('size_L');
            $table->integer('size_XXL')->after('size_XL');
            $table->integer('size_XXXL')->after('size_XXL');
            $table->integer('size_2')->after('size_XXXL');
            $table->integer('size_4')->after('size_2');
            $table->integer('size_6')->after('size_4');
            $table->integer('size_8')->after('size_6');
            $table->integer('size_10')->after('size_8');
            $table->integer('size_12')->after('size_10');
            $table->integer('size_27')->after('size_12');
            $table->integer('size_28')->after('size_27');
            $table->integer('size_29')->after('size_28');
            $table->integer('size_30')->after('size_29');
            $table->integer('size_31')->after('size_30');
            $table->integer('size_32')->after('size_31');
            $table->integer('size_33')->after('size_32');
            $table->integer('size_34')->after('size_33');
            $table->integer('size_35')->after('size_34');
            $table->integer('size_36')->after('size_35');
            $table->integer('size_37')->after('size_36');
            $table->integer('size_38')->after('size_37');
            $table->integer('size_39')->after('size_38');
            $table->integer('size_40')->after('size_39');
            $table->integer('size_41')->after('size_40');
            $table->integer('size_42')->after('size_41');
            $table->integer('size_other')->after('size_42');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('charts');
    }
}

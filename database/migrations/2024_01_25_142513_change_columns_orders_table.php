<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnsOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['S','M','L','XL','XXL','XXXL','2','4','6','8','10','12','27','28','29','30','31','32','33','34','35','36','37','38','39','40','41','42']);
            $table->integer('size_S')->default(0)->after('product_id');
            $table->integer('size_M')->default(0)->after('size_S');
            $table->integer('size_L')->default(0)->after('size_M');
            $table->integer('size_XL')->default(0)->after('size_L');
            $table->integer('size_XXL')->default(0)->after('size_XL');
            $table->integer('size_XXXL')->default(0)->after('size_XXL');
            $table->integer('size_2')->default(0)->after('size_XXXL');
            $table->integer('size_4')->default(0)->after('size_2');
            $table->integer('size_6')->default(0)->after('size_4');
            $table->integer('size_8')->default(0)->after('size_6');
            $table->integer('size_10')->default(0)->after('size_8');
            $table->integer('size_12')->default(0)->after('size_10');
            $table->integer('size_27')->default(0)->after('size_12');
            $table->integer('size_28')->default(0)->after('size_27');
            $table->integer('size_29')->default(0)->after('size_28');
            $table->integer('size_30')->default(0)->after('size_29');
            $table->integer('size_31')->default(0)->after('size_30');
            $table->integer('size_32')->default(0)->after('size_31');
            $table->integer('size_33')->default(0)->after('size_32');
            $table->integer('size_34')->default(0)->after('size_33');
            $table->integer('size_35')->default(0)->after('size_34');
            $table->integer('size_36')->default(0)->after('size_35');
            $table->integer('size_37')->default(0)->after('size_36');
            $table->integer('size_38')->default(0)->after('size_37');
            $table->integer('size_39')->default(0)->after('size_38');
            $table->integer('size_40')->default(0)->after('size_39');
            $table->integer('size_41')->default(0)->after('size_40');
            $table->integer('size_42')->default(0)->after('size_41');
            $table->integer('size_other')->default(0)->after('size_42');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}

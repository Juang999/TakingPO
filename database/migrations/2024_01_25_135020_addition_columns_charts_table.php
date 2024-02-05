<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdditionColumnsChartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('charts', function (Blueprint $table) {
            $table->dropColumn('qty');
            $table->integer('S')->after('product_id');
            $table->integer('M')->after('S');
            $table->integer('L')->after('M');
            $table->integer('XL')->after('L');
            $table->integer('XXL')->after('XL');
            $table->integer('XXXL')->after('XXL');
            $table->integer('2')->after('XXXL');
            $table->integer('4')->after('2');
            $table->integer('6')->after('4');
            $table->integer('8')->after('6');
            $table->integer('10')->after('8');
            $table->integer('12')->after('10');
            $table->integer('27')->after('12');
            $table->integer('28')->after('27');
            $table->integer('29')->after('28');
            $table->integer('30')->after('29');
            $table->integer('31')->after('30');
            $table->integer('32')->after('31');
            $table->integer('33')->after('32');
            $table->integer('34')->after('33');
            $table->integer('35')->after('34');
            $table->integer('36')->after('35');
            $table->integer('37')->after('36');
            $table->integer('38')->after('37');
            $table->integer('39')->after('38');
            $table->integer('40')->after('39');
            $table->integer('41')->after('40');
            $table->integer('42')->after('41');
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

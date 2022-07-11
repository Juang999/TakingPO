<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distributors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->integer('prtnr_add_by')->constrained('users')->nullable();
            $table->integer('prtnr_upd_by')->constrained('users')->nullable();
            $table->integer('db_id')->default(0);
            $table->string('group_code')->nullable();
            $table->integer('partner_group_id')->constrained('parter_groups')->nullable();
            $table->string('level')->default('bronze');
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
        Schema::dropIfExists('distributors');
    }
}

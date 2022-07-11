<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->integer('distributor_id')->constrained('distributors')->nullable();
            $table->string('group_code');
            $table->integer('partner_group_id')->constrained('parter_groups')->nullable();
            $table->date('join_date')->nullable();
            $table->string('training_level')->nullable();
            $table->integer('partner_add_by')->constrained('users')->nullable();
            $table->integer('partner_upd_by')->constrained('users')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agents');
    }
}

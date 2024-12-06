<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique()->nullable();
            $table->integer('parent_id')->nullable();
            $table->string('group_code')->nullable();
            $table->integer('partner_group_id')->constrained('parter_groups')->nullable();
            $table->string('level')->nullable();
            $table->string('training_level')->nullable();
            $table->integer('prtnr_add_by')->constrained('users')->nullable();
            $table->integer('prtnr_upd_by')->constrained('users')->nullable();
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
        Schema::dropIfExists('partners');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMutifStoreMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mutif_store_masters', function (Blueprint $table) {
            $table->id();
            $table->string('mutif_store_name');
            $table->string('mutif_store_code');
            $table->integer('ms_add_by')->constrained('users')->nullable();
            $table->integer('ms_upd_by')->constrained('users')->nullable();
            $table->string('group_code');
            $table->integer('agent_id')->constrained('agents');
            $table->integer('partner_group_id')->constrained('partner_groups');
            $table->string('open_date')->nullable();
            $table->string('status');
            $table->string('msdp')->default('-');
            $table->string('url')->nullable();
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('mutif_store_masters');
    }
}

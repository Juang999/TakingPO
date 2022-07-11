<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMutifStoreAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mutif_store_addresses', function (Blueprint $table) {
            $table->id();
            $table->integer('mutif_store_master_id')->constrained('mutif_store_masters');
            $table->integer('prtnr_add_by')->constrained('users')->nullable();
            $table->integer('prtnr_upd_by')->constrained('users')->nullable();
            $table->text('address');
            $table->string('province');
            $table->string('regency');
            $table->string('district');
            $table->string('phone_1')->nullable();
            $table->string('phone_2')->nullable();
            $table->string('fax_1')->nullable();
            $table->string('fax_2')->nullable();
            $table->string('addr_type')->default('bill');
            $table->string('zip')->nullable();
            $table->string('comment')->nullable();
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
        Schema::dropIfExists('mutif_store_addresses');
    }
}

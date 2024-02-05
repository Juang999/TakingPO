<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_groups', function (Blueprint $table) {
            $table->id();
            $table->integer('prtnr_add_by')->constrained('users')->nullable();
            $table->integer('prtnr_upd_by')->constrained('users')->nullable();
            $table->string('prtnr_code');
            $table->string('prtnr_name');
            $table->string('prtnr_desc');
            $table->decimal('discount', 10, 9)->default(0);
            $table->boolean('is_active')->default(0);
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
        Schema::dropIfExists('partner_groups');
    }
}

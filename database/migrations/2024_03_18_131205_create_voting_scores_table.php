<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVotingScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voting_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('voting_event_id');
            $table->unsignedBigInteger('sample_product_id');
            $table->unsignedBigInteger('attendance_id');
            $table->integer('score');
            $table->string('note');
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
        Schema::dropIfExists('voting_scores');
    }
}

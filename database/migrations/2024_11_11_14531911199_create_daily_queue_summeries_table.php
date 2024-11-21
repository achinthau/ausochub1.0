<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_queue_summeries', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('queue');
            $table->integer('calls');
            $table->integer('answered');
            $table->integer('abandoned');
            $table->integer('agents');
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
        Schema::dropIfExists('daily_queue_summeries');
    }
};

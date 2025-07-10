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
        Schema::create('daily_call_summaries', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('inbound');
            $table->integer('outbound');
            $table->integer('queued');
            $table->integer('abandent');
            $table->integer('answered');
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
        Schema::dropIfExists('daily_call_summaries');
    }
};

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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('topic');
            $table->string('description')->nullable();
            $table->foreignId('lead_id')->nullable();
            $table->foreignId('ticket_category_id')->nullable();
            $table->foreignId('ticket_sub_category_id')->nullable();
            $table->json('tags')->nullable();
            $table->foreignId('ticket_status_id')->nullable();
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
        Schema::dropIfExists('tickets');
    }
};

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
        Schema::create('feed_contact_in_valids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_id')->constrained()->onDelete('cascade');
            $table->string('contact_no_01')->nullable();
            $table->string('contact_no_02')->nullable();
            $table->string('priority_field')->nullable();
            $table->json('data')->nullable();
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
        Schema::dropIfExists('feed_contact_in_valids');
    }
};

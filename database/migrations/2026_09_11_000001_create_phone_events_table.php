<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('phone_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type', 50);
            $table->string('call_id', 191)->nullable();
            $table->string('direction', 16)->nullable();
            $table->string('cli', 32)->nullable();
            $table->string('extension', 16)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();

            $table->index(['event_type', 'created_at']);
            $table->index('call_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('phone_events');
    }
};
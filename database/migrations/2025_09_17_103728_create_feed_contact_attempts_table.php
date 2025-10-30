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
        Schema::create('feed_contact_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feed_contact_valid_id')->nullable();
            $table->unsignedBigInteger('call_status_option_id')->nullable();
            $table->string('comments')->nullable();
            $table->bigInteger('campaign_id')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
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
        Schema::dropIfExists('feed_contact_attempts');
    }
};

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
        Schema::create('campaign_agent_dial_limits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id');
            $table->unsignedBigInteger('agent_id');
            $table->unsignedInteger('max_count')->nullable();
            $table->unsignedInteger('current_count')->default(0);
            $table->string('month')->nullable();
            $table->timestamps();

            $table->unique(['campaign_id', 'agent_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaign_agent_dial_limits');
    }
};

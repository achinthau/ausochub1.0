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
        Schema::create('callback_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->text('contact_number')->nullable();
            $table->text('lead_id')->nullable();
            $table->text('unique_id')->nullable();
            $table->text('cx_ticket_id')->nullable();
            $table->text('src')->nullable();
            $table->datetime('callback_at');
            $table->datetime('called_at')->nullable();
            $table->text('comment')->nullable();
            $table->text('closing_reason')->nullable();
            $table->text('closed_by')->nullable();
            $table->text('campaign')->nullable();
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
        Schema::dropIfExists('callback_customers');
    }
};

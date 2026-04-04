<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create table in AC database (mysql-old connection)
        Schema::connection('mysql-old')->create('ac_agent_performance', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('agent_id');
            $table->string('agent_name');
            $table->string('extension')->nullable();
            
            // Call metrics
            $table->integer('total_calls')->default(0);
            $table->decimal('avg_calls', 10, 2)->default(0);
            $table->integer('total_missed')->default(0);
            $table->decimal('avg_missed', 10, 2)->default(0);
            
            // ACW (After Call Work) metrics in seconds
            $table->integer('acw')->default(0);
            $table->decimal('avg_acw', 10, 2)->default(0);
            
            // Break metrics in seconds
            $table->integer('other_break')->default(0);
            $table->decimal('avg_oth_break', 10, 2)->default(0);
            
            // Time metrics in seconds
            $table->integer('active_time')->default(0);
            $table->integer('talk_time')->default(0);
            $table->decimal('avg_talk_time', 10, 2)->default(0);
            
            // Ticket metrics
            $table->integer('tickets_created_count')->default(0);
            
            // Queues: JSON format for queue-wise total active time
            // Format: {"queue_id": queue_name, "active_time": seconds, ...}
            $table->json('queues')->nullable();
            
            $table->timestamps();
            $table->index(['date', 'agent_id']);
            $table->index('agent_id');
            $table->unique(['date', 'agent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql-old')->dropIfExists('ac_agent_performance');
    }
};

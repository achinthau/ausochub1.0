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
        Schema::create('whatsapp_meta_messages', function (Blueprint $table) {
            $table->id();
            $table->string('sender_id');           // WhatsApp ID / phone number of user
            $table->string('sender_name')->nullable(); // Display profile name if resolved
            $table->text('message_text')->nullable();
            $table->string('message_id')->nullable()->unique(); // WhatsApp Message ID
            $table->boolean('from_me')->default(false); // true = sent by agent
            $table->boolean('is_read')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index('sender_id');
            $table->index('from_me');
            $table->index('is_read');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('whatsapp_meta_messages');
    }
};

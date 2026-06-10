<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messenger_messages', function (Blueprint $table) {
            $table->id();
            $table->string('sender_id');           // Facebook PSID of the user
            $table->string('sender_name')->nullable(); // Display name if resolved
            $table->text('message_text')->nullable();
            $table->string('message_id')->nullable()->unique(); // Meta message ID
            $table->boolean('from_me')->default(false); // true = sent by page
            $table->boolean('is_read')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index('sender_id');
            $table->index('from_me');
            $table->index('is_read');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messenger_messages');
    }
};

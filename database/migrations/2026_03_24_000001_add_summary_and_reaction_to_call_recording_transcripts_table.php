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
        Schema::connection('mysql-old')->table('call_recording_transcripts', function (Blueprint $table) {
            $table->text('summary')->nullable()->after('transcript');
            $table->string('reaction', 32)->nullable()->after('summary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql-old')->table('call_recording_transcripts', function (Blueprint $table) {
            $table->dropColumn(['summary', 'reaction']);
        });
    }
};

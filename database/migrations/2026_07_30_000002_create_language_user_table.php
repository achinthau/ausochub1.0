<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('language_user', function (Blueprint $table) {
            $table->foreignId('language_id')->constrained('user_languages')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->primary(['language_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('language_user');
    }
};

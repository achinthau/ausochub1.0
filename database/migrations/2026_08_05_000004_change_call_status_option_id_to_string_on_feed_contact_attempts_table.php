<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('feed_contact_attempts', function (Blueprint $table) {
            $table->string('call_status_option_id', 255)->nullable()->change();
            $table->string('call_status_option_type', 255)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('feed_contact_attempts', function (Blueprint $table) {
            $table->unsignedBigInteger('call_status_option_id')->nullable()->change();
            $table->bigInteger('call_status_option_type')->nullable()->change();
        });
    }
};

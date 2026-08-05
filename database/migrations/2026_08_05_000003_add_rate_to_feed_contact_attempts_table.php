<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('feed_contact_attempts', function (Blueprint $table) {
            $table->unsignedTinyInteger('rate')->nullable()->after('call_status_option_id');
        });
    }

    public function down()
    {
        Schema::table('feed_contact_attempts', function (Blueprint $table) {
            $table->dropColumn('rate');
        });
    }
};

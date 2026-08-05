<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cx_tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('feed_id')->nullable()->index()->after('id');
        });
    }

    public function down()
    {
        Schema::table('cx_tickets', function (Blueprint $table) {
            $table->dropColumn('feed_id');
        });
    }
};

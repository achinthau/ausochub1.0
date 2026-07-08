<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tickets_report', function (Blueprint $table) {
            $table->string('call_uniqueid')->nullable()->after('call_id')->index();
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->string('call_uniqueid')->nullable()->after('call_id')->index();
        });
    }

    public function down()
    {
        Schema::table('tickets_report', function (Blueprint $table) {
            $table->dropIndex(['call_uniqueid']);
            $table->dropColumn('call_uniqueid');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['call_uniqueid']);
            $table->dropColumn('call_uniqueid');
        });
    }
};
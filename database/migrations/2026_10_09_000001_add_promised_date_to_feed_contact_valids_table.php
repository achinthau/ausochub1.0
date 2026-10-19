<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('feed_contact_valids') && !Schema::hasColumn('feed_contact_valids', 'promised_date')) {
            Schema::table('feed_contact_valids', function (Blueprint $table) {
                $table->date('promised_date')->nullable()->after('comments');
            });
        }

        if (Schema::hasTable('feed_contact_valids_report') && !Schema::hasColumn('feed_contact_valids_report', 'promised_date')) {
            Schema::table('feed_contact_valids_report', function (Blueprint $table) {
                $table->date('promised_date')->nullable()->after('comments');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('feed_contact_valids') && Schema::hasColumn('feed_contact_valids', 'promised_date')) {
            Schema::table('feed_contact_valids', function (Blueprint $table) {
                $table->dropColumn('promised_date');
            });
        }

        if (Schema::hasTable('feed_contact_valids_report') && Schema::hasColumn('feed_contact_valids_report', 'promised_date')) {
            Schema::table('feed_contact_valids_report', function (Blueprint $table) {
                $table->dropColumn('promised_date');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add a unique, immutable link to the source feed_contact_valids row so
     * the report can never be upserted by the non-unique priority_field.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('feed_contact_valids_report')) {
            return;
        }

        Schema::table('feed_contact_valids_report', function (Blueprint $table) {
            $table->unsignedBigInteger('feed_contact_id')->nullable()->after('id');
            $table->unique('feed_contact_id', 'feed_contact_valids_report_feed_contact_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('feed_contact_valids_report')) {
            return;
        }

        Schema::table('feed_contact_valids_report', function (Blueprint $table) {
            $table->dropUnique('feed_contact_valids_report_feed_contact_id_unique');
            $table->dropColumn('feed_contact_id');
        });
    }
};
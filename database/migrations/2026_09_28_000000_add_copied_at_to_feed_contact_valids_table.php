<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Marks a feed_contact_valids row as archived into
     * feed_contact_valids_report, so completed rows are only removed from the
     * active table once the copy is confirmed.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('feed_contact_valids')) {
            return;
        }

        Schema::table('feed_contact_valids', function (Blueprint $table) {
            if (Schema::hasColumn('feed_contact_valids', 'copied_at')) {
                return;
            }

            $table->timestamp('copied_at')->nullable()->after('attempted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('feed_contact_valids')) {
            return;
        }

        Schema::table('feed_contact_valids', function (Blueprint $table) {
            if (!Schema::hasColumn('feed_contact_valids', 'copied_at')) {
                return;
            }

            $table->dropColumn('copied_at');
        });
    }
};

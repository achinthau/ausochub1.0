<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Comments are append-only histories (date/time + agent + comment), so
     * they need more room than a 255 character string column allows.
     *
     * @return void
     */
    public function up()
    {
        foreach (['feed_contact_valids', 'feed_contact_valids_report'] as $tableName) {
            if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, 'comments')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->text('comments')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (['feed_contact_valids', 'feed_contact_valids_report'] as $tableName) {
            if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, 'comments')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->string('comments')->nullable()->change();
            });
        }
    }
};

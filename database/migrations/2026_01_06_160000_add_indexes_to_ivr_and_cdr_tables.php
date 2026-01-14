<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToIvrAndCdrTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql-old')->table('au_ivr_calls', function (Blueprint $table) {
            $table->index('uniqueid', 'au_ivr_calls_uniqueid_index');
            $table->index('date', 'au_ivr_calls_date_index');
        });

        Schema::connection('mysql-old')->table('cdr', function (Blueprint $table) {
            $table->index('uniqueid', 'cdr_uniqueid_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql-old')->table('au_ivr_calls', function (Blueprint $table) {
            $table->dropIndex('au_ivr_calls_uniqueid_index');
            $table->dropIndex('au_ivr_calls_date_index');
        });

        Schema::connection('mysql-old')->table('cdr', function (Blueprint $table) {
            $table->dropIndex('cdr_uniqueid_index');
        });
    }
}

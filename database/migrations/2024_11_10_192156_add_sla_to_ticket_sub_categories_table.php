<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_sub_categories', function (Blueprint $table) {
            $table->decimal('due_in_hours')->nullable()->after('tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_sub_categories', function (Blueprint $table) {
            $table->dropColumn('due_in_hours');
        });
    }
};

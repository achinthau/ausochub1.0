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
        Schema::table('ticket_items', function (Blueprint $table) {
            $table->foreignId('portion_id')->nullable()->change();
            $table->string('barcode')->nullable()->after('item_id');
            $table->foreignId('parent_item_id')->nullable()->after('line_total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_items', function (Blueprint $table) {
            $table->dropColumn('barcode','parent_item_id');
        });
    }
};

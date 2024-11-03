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
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('call_id')->nullable()->after('ticket_sub_category_id');
            $table->string('order_ref')->nullable()->after('tags');
            $table->boolean('crm')->nullable()->after('tags');
            $table->foreignId('outlet_id')->nullable()->after('tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('call_id');
            $table->dropColumn('crm');
            $table->dropColumn('order_ref');
            $table->dropColumn('outlet_id');
        });
    }
};

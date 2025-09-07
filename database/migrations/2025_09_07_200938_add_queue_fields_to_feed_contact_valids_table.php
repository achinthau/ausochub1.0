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
        Schema::table('feed_contact_valids', function (Blueprint $table) {
            //
            $table->dateTime('next_available_at')->nullable()->after('status')->index();
            $table->unsignedSmallInteger('attempt_count')->default(0)->after('next_available_at')->index();
            $table->boolean('in_queue')->default(false)->after('attempt_count')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('feed_contact_valids', function (Blueprint $table) {
            //
            $table->dropColumn(['next_available_at','attempt_count','in_queue']);
        });
    }
};

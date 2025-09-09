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
        Schema::table('campaigns', function (Blueprint $table) {
            //
            $table->unsignedTinyInteger('max_attempts')->default(3)->after('type');
            $table->json('wr_ratio_json')->nullable()->after('max_attempts');
            $table->unsignedInteger('version')->default(1)->after('wr_ratio_json')->index();
            $table->dateTime('started_at')->nullable()->after('version')->index();
            $table->dateTime('stopped_at')->nullable()->after('started_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            //
            $table->dropColumn(['max_attempts','wr_ratio_json','version','started_at','stopped_at']);
        });
    }
};

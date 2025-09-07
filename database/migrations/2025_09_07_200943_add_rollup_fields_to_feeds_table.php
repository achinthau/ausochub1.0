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
        Schema::table('feeds', function (Blueprint $table) {
            //
            $table->unsignedInteger('total_contacts')->default(0)->after('status');
            $table->unsignedInteger('valid_contacts')->default(0)->after('total_contacts');
            $table->unsignedInteger('invalid_contacts')->default(0)->after('valid_contacts');
            $table->unsignedBigInteger('confirmed_by')->nullable()->after('invalid_contacts')->index();
            $table->dateTime('confirmed_at')->nullable()->after('confirmed_by')->index();
            $table->unsignedBigInteger('created_by')->nullable()->after('confirmed_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('feeds', function (Blueprint $table) {
            //
            $table->dropColumn([
                'total_contacts','valid_contacts','invalid_contacts',
                'confirmed_by','confirmed_at','created_by'
            ]);
        });
    }
};

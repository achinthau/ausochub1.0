<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dialer_call_status_options', function (Blueprint $table) {
            $table->string('status')->nullable()->after('type');
        });

        // Backfill the new status column from the existing option type so
        // current behaviour is preserved. Type 4 is the satisfaction
        // "not answered" type and maps to the normal not-answered status.
        $statusByType = [
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '2',
            41 => '41',
            42 => '42',
        ];

        foreach ($statusByType as $type => $status) {
            DB::table('dialer_call_status_options')
                ->where('type', $type)
                ->whereNull('status')
                ->update(['status' => $status]);
        }
    }

    public function down(): void
    {
        Schema::table('dialer_call_status_options', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};

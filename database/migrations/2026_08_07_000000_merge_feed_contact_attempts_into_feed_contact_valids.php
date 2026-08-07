<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Merge the latest call attempt fields onto feed_contact_valids and drop
     * feed_contact_attempts so dialer reads/writes use a single table.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('feed_contact_valids', function (Blueprint $table) {
            $table->string('call_status_option_id', 255)->nullable()->after('status');
            $table->string('call_status_option_type', 255)->nullable()->after('call_status_option_id');
            $table->unsignedTinyInteger('rate')->nullable()->after('call_status_option_type');
            $table->string('comments')->nullable()->after('rate');
            $table->bigInteger('campaign_id')->nullable()->after('comments')->index();
            $table->unsignedBigInteger('updated_by')->nullable()->after('campaign_id')->index();
            $table->timestamp('attempted_at')->nullable()->after('updated_by');
        });

        if (Schema::hasTable('feed_contact_attempts')) {
            DB::statement("
                UPDATE ac_feed_contact_valids fcv
                INNER JOIN (
                    SELECT feed_contact_valid_id, MAX(id) AS last_attempt_id
                    FROM ac_feed_contact_attempts
                    WHERE feed_contact_valid_id IS NOT NULL
                    GROUP BY feed_contact_valid_id
                ) latest ON latest.feed_contact_valid_id = fcv.id
                INNER JOIN ac_feed_contact_attempts a ON a.id = latest.last_attempt_id
                SET fcv.call_status_option_id = a.call_status_option_id,
                    fcv.call_status_option_type = a.call_status_option_type,
                    fcv.rate = a.rate,
                    fcv.comments = a.comments,
                    fcv.campaign_id = a.campaign_id,
                    fcv.updated_by = a.updated_by,
                    fcv.attempted_at = a.created_at
            ");

            Schema::dropIfExists('feed_contact_attempts');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('feed_contact_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feed_contact_valid_id')->nullable();
            $table->string('call_status_option_id', 255)->nullable();
            $table->string('call_status_option_type', 255)->nullable();
            $table->unsignedTinyInteger('rate')->nullable();
            $table->string('comments')->nullable();
            $table->bigInteger('campaign_id')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        DB::statement("
            INSERT INTO ac_feed_contact_attempts
                (feed_contact_valid_id, call_status_option_id, call_status_option_type, rate, comments, campaign_id, updated_by, created_at, updated_at)
            SELECT id, call_status_option_id, call_status_option_type, rate, comments, campaign_id, updated_by, attempted_at, attempted_at
            FROM ac_feed_contact_valids
            WHERE attempted_at IS NOT NULL
        ");

        Schema::table('feed_contact_valids', function (Blueprint $table) {
            $table->dropColumn(['call_status_option_id', 'call_status_option_type', 'rate', 'comments', 'campaign_id', 'updated_by', 'attempted_at']);
        });
    }
};

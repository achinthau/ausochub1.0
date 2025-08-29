<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        DB::statement("
            CREATE OR REPLACE VIEW ac_campaign_metrics AS
            SELECT 
                c.id AS campaign_id,
                c.name,
                c.status,
                c.assigned_feeds,
                c.assigned_users,
                c.type,
                c.created_at,
                c.updated_at,
                COALESCE(
                    (SELECT COUNT(DISTINCT fcv.phone)
                     FROM ac_feed_contact_valids fcv
                     WHERE FIND_IN_SET(fcv.feed_id, c.assigned_feeds)),
                    0
                ) AS contact_count,
                COALESCE(
                    (SELECT COUNT(DISTINCT fcv.phone)
                     FROM ac_feed_contact_valids fcv
                     WHERE FIND_IN_SET(fcv.feed_id, c.assigned_feeds)
                     AND fcv.status IS NOT NULL),
                    0
                ) AS dialed_count,
                COALESCE(
                    (SELECT COUNT(DISTINCT fcv.phone)
                     FROM ac_feed_contact_valids fcv
                     WHERE FIND_IN_SET(fcv.feed_id, c.assigned_feeds)
                     AND fcv.status = 'answered'),
                    0
                ) AS answered_count,
                COALESCE(
                    CASE 
                        WHEN c.assigned_users IS NULL OR c.assigned_users = '' 
                        THEN 0 
                        ELSE LENGTH(c.assigned_users) - 
                             LENGTH(REPLACE(c.assigned_users, ',', '')) + 1 
                    END,
                    0
                ) AS agents_count
            FROM ac_campaigns c
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaign_metrics');
    }
};

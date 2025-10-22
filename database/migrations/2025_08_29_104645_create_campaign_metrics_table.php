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
                c.name AS name,
                c.status AS status,
                c.assigned_feeds AS assigned_feeds,
                c.assigned_users AS assigned_users,
                c.type AS type,
                c.created_at AS created_at,
                c.updated_at AS updated_at,
                COALESCE(
                    (SELECT COUNT(fcv.contact_no_01)
                     FROM ac_feed_contact_valids fcv
                     WHERE FIND_IN_SET(CAST(fcv.feed_id AS CHAR CHARACTER SET utf8mb3), 
                                       CAST(c.assigned_feeds AS CHAR CHARACTER SET utf8mb3))
                    ), 
                    0
                ) AS contact_count,
                COALESCE(
                    (SELECT COUNT(fcv.contact_no_01)
                     FROM ac_feed_contact_valids fcv
                     WHERE FIND_IN_SET(CAST(fcv.feed_id AS CHAR CHARACTER SET utf8mb3), 
                                       CAST(c.assigned_feeds AS CHAR CHARACTER SET utf8mb3))
                       AND fcv.status IS NOT NULL
                    ), 
                    0
                ) AS dialed_count,
                COALESCE(
                    (SELECT COUNT(fcv.contact_no_01)
                     FROM ac_feed_contact_valids fcv
                     WHERE FIND_IN_SET(CAST(fcv.feed_id AS CHAR CHARACTER SET utf8mb3), 
                                       CAST(c.assigned_feeds AS CHAR CHARACTER SET utf8mb3))
                       AND fcv.status = 'answered'
                    ), 
                    0
                ) AS answered_count,
                COALESCE(
                    CASE 
                        WHEN c.assigned_users IS NULL OR c.assigned_users = '' THEN 0
                        ELSE LENGTH(c.assigned_users) - LENGTH(REPLACE(c.assigned_users, ',', '')) + 1
                    END, 
                    0
                ) AS agents_count
            FROM ac_campaigns c;
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

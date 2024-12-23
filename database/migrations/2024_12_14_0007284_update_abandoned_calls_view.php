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
        DB::connection("mysql-old")->getPdo()->exec('DROP VIEW IF EXISTS abandoned_calls;');

        // Create the updated view
        DB::connection("mysql-old")->getPdo()->exec("
            CREATE VIEW abandoned_calls AS
            SELECT
                tt.uniqueid AS uniqueid,
                tt.ani AS ani,
                tt.dnis AS dnis,
                tt.queuename AS queuename,
                tt.called_at AS called_at,
                tt.in_queue AS in_queue,
                tt.connected AS connected,
                tt.disconnected AS disconnected,
                tt.recalled_at AS recalled_at,
                IF(tt.recalled_at IS NULL, 0, 1) AS recalled_status 
            FROM
                (
                SELECT
                    t.uniqueid AS uniqueid,
                    t.ani AS ani,
                    t.dnis AS dnis,
                    t.queuename AS queuename,
                    t.called_at AS called_at,
                    t.in_queue AS in_queue,
                    t.connected AS connected,
                    t.disconnected AS disconnected,
                    (
                    SELECT
                        MIN(queuecount.date) 
                    FROM
                        queuecount 
                    WHERE
                        queuecount.date > t.called_at 
                        AND queuecount.ani = t.ani 
                        AND queuecount.status = 2 
                        AND queuecount.uniqueid <> t.uniqueid
                    ) AS recalled_at 
                FROM
                    (
                    SELECT
                        qc.uniqueid AS uniqueid,
                        qc.ani AS ani,
                        qc.dnis AS dnis,
                        qc.queuename AS queuename,
                        MIN(qc.date) AS called_at,
                        SUM(IF(qc.status = 1, 1, 0)) AS in_queue,
                        SUM(IF(qc.status = 2, 1, 0)) AS connected,
                        SUM(IF(qc.status = 0, 1, 0)) AS disconnected 
                    FROM
                        queuecount qc 
                    GROUP BY
                        qc.uniqueid 
                    HAVING
                        SUM(IF(qc.status = 2, 1, 0)) = 0 
                    ORDER BY
                        MIN(qc.date)
                    ) t
                ) tt
            ORDER BY 
                tt.called_at DESC;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};

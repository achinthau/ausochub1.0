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
        
        DB::connection("mysql-old")->getPdo()->exec("CREATE VIEW abandoned_calls
        AS
        SELECT tt.*,IF(tt.recalled_at IS NULL,0,1) as recalled_status FROM (SELECT 
            t.* ,
            (SELECT MIN(date) FROM queuecount WHERE date>t.called_at AND ani=t.ani and `status`=2 AND uniqueid<>t.uniqueid) as recalled_at
        
        FROM (
        SELECT 
            qc.uniqueid,qc.ani,qc.queuename,MIN(qc.date) as called_at,SUM(IF(qc.`status`=1,1,0)) as in_queue,SUM(IF(qc.`status`=2,1,0)) as connected,SUM(IF(qc.`status`=0,1,0)) as disconnected  
        FROM queuecount  qc
        #WHERE qc.date BETWEEN '2023-05-02 00:00:00' AND NOW()
        GROUP BY qc.uniqueid 
        HAVING SUM(IF(qc.`status`=2,1,0))=0
        ORDER BY MIN(qc.date)) t) tt; #WHERE tt.recalled_at IS NULL
        ");
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection("mysql-old")->getPdo()->exec('DROP VIEW IF EXISTS abandoned_calls;');
    }
};

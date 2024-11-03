<?php

namespace App\Http\Livewire\Dashboard\Admin;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{

    public $total_inbound_call_count = 0;
    public $total_outbound_call_count = 0;
    public $total_queue_count = 0;
    public $total_disconnection_count = 0;
    public $total_answered_count = 0;
    public $abandoned_queue_count = 0;
    public $agent_conntected_count = 0;
    public $queue_wating_count = 0;
    public $on_going = 0;




    public function render()
    {
        $callData = DB::connection('mysql-old')->select("SELECT
            IFNULL(SUM(  IF(a.direction = 'in' AND a.`status`=1 ,1,0)) ,0) AS total_inbound_call_count,
            IFNULL(SUM(  IF(a.direction = 'out' AND a.`status`=1  ,1,0)),0) AS total_outbound_call_count
        FROM
            callcount a 
        WHERE
            
            a.date > CURDATE();")[0];

        $this->total_inbound_call_count = $callData->total_inbound_call_count;
        $this->total_outbound_call_count = $callData->total_outbound_call_count;


        /*  $queueData = DB::connection('mysql-old')->select("SELECT 
                IFNULL(SUM(IF(a.`status`=1,1,0)),0) as total_queue_count ,
                IFNULL(SUM(IF(a.`status`=0,1,0)),0) as total_disconnection_count ,
                IFNULL(SUM(IF(a.`status`=2,1,0)),0) as total_answered_count ,
                IFNULL(SUM(IF(a.`status`=0,1,0))-SUM(IF(a.`status`=2,1,0)),0) as abandoned_queue_count,
                IFNULL(SUM(IF(a.`status`=1,1,0))-SUM(IF(a.`status`=0,1,0)),0) as agent_conntected_count,
                IFNULL(SUM(IF(a.uniqueid NOT IN (SELECT DISTINCT  uniqueid  FROM queuecount aa WHERE aa.date > CURDATE() and aa.`status` IN (2,0)),1,0)),0) as queue_wating_count
            FROM queuecount a 
            WHERE  a.date > CURDATE() ;")[0]; */
        $queueData = DB::connection('mysql-old')->select("SELECT 
        SUM(t1.connected) as total_queue_count,
        SUM(t1.answered) as total_answered_count,
        SUM(t1.disconnected) as total_disconnection_count,
        SUM(t1.abandoned) as abandoned_queue_count,
        
        SUM(t1.queue_wating_count) as queue_wating_count
        

        FROM (
        SELECT t.*,
        IF(t.answered=0 AND t.disconnected=1 AND t.connected=1,1,0) as abandoned,
        IFNULL(IF(t.uniqueid NOT IN (SELECT DISTINCT  uniqueid  FROM queuecount aa WHERE aa.date > CURDATE() and aa.`status` IN (2,0)),1,0),0) as queue_wating_count
    
        FROM 
        (
                SELECT 
                a.uniqueid ,
                    SUM(IF(a.`status`=1,1,0)) as connected,
                    SUM(IF(a.`status`=2,1,0)) as answered,
                    SUM(IF(a.`status`=0,1,0)) as disconnected
                FROM queuecount a 
                WHERE  a.date > CURDATE()
                GROUP BY a.uniqueid 
             ) t
        ) t1;")[0];
        $this->total_queue_count = $queueData->total_queue_count;
        $this->total_disconnection_count = $queueData->total_disconnection_count;
        $this->total_answered_count = $queueData->total_answered_count;
        $this->abandoned_queue_count = $queueData->abandoned_queue_count < 0 ? 0 : $queueData->abandoned_queue_count;
        // $this->agent_conntected_count = $queueData->agent_conntected_count;
        $this->queue_wating_count = $queueData->queue_wating_count;

        //$this->on_going = $this->agent_conntected_count - $this->queue_wating_count; //< 0  ? 0 : $this->agent_conntected_count - $this->queue_wating_count;



        $queueWiseData = DB::connection('mysql-old')->select("SELECT 
                t1.queuename,
                SUM(t1.connected) as total_queue_count,
                SUM(t1.answered) as total_answered_count,
                SUM(t1.disconnected) as total_disconnection_count,
                SUM(t1.abandoned) as abandoned_queue_count,

                SUM(t1.queue_wating_count) as queue_wating_count


                FROM (
                SELECT t.*,
                IF(t.answered=0 AND t.disconnected=1 AND t.connected=1,1,0) as abandoned,
                IFNULL(IF(t.uniqueid NOT IN (SELECT DISTINCT  uniqueid  FROM queuecount aa WHERE aa.date > CURDATE() and aa.`status` IN (2,0)),1,0),0) as queue_wating_count

                FROM 
                (
                        SELECT 
                        a.uniqueid ,a.queuename,
                            SUM(IF(a.`status`=1,1,0)) as connected,
                            SUM(IF(a.`status`=2,1,0)) as answered,
                            SUM(IF(a.`status`=0,1,0)) as disconnected
                        FROM queuecount a 
                        WHERE  a.date > CURDATE()
                        GROUP BY a.uniqueid ,a.queuename
                    ) t
                ) t1 GROUP BY t1.queuename;
            ");
        /*  $queueWiseData = DB::connection('mysql-old')->select("SELECT 
            a.queuename,
                SUM(IF(a.`status`=1,1,0)) as total_queue_count ,
                SUM(IF(a.`status`=0,1,0)) as total_disconnection_count ,
                SUM(IF(a.`status`=2,1,0)) as total_answered_count ,
                SUM(IF(a.`status`=0,1,0))-SUM(IF(a.`status`=2,1,0)) as abandoned_queue_count,
                SUM(IF(a.`status`=1,1,0))-SUM(IF(a.`status`=0,1,0)) as agent_conntected_count,
                SUM(IF(a.uniqueid NOT IN (SELECT DISTINCT  uniqueid  FROM queuecount aa WHERE aa.date > CURDATE() and aa.`status` IN (2,0)),1,0)) as queue_wating_count#
            FROM queuecount a 
            WHERE  a.date > CURDATE() 
            GROUP BY a.queuename;
            "); */


        return view(
            'livewire.dashboard.admin.index',
            [
                'queueWiseData' => $queueWiseData
            ]
        );
    }
}

<?php

namespace App\Console\Commands;

use App\Models\DailyCallSummary;
use App\Models\DailyQueueSummery;
use BladeUIKit\Components\DateTime\Carbon;
use Carbon\Carbon as CarbonCarbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateDailyCallSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:call-summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $date = CarbonCarbon::today()->format('Y-m-d');
        $date = '2024-10-09';

        $inboundCount = DB::connection('mysql-old') // Specify the connection
            ->table('callcount') // Use the correct table name with the prefix
            ->where('direction', 'in')
            ->where('date', 'like', "$date%")
            ->count();

        $outboundCount = DB::connection('mysql-old') // Specify the connection
            ->table('callcount')
            ->where('direction', 'out')
            ->where('date', 'like', "$date%")
            ->count();

        $queuedCount = DB::connection('mysql-old') // Specify the connection
            ->table('queuecount') // Ensure the correct table name here too
            ->where('status', '1')
            ->where('date', 'like', "$date%")
            ->count();

        $answeredCount = DB::connection('mysql-old') // Specify the connection
            ->table('queuecount')
            ->where('status', '2')
            ->where('date', 'like', "$date%")
            ->count();

        $dailyCallSummaryData = [
            'date' => $date,
            'inbound' => $inboundCount,
            'outbound' => $outboundCount,
            'queued' => $queuedCount,
            'abandent' => $queuedCount - $answeredCount,
            'answered' => $answeredCount,
        ];


        DailyCallSummary::create($dailyCallSummaryData);






        // $DailyQueueSummery = [
        //     'date' => $date,
        //     'queue' => 12,
        //     'calls' => 45,
        //     'answered' => 45,
        //     'abandoned' => 45,
        // ];

        // DailyQueueSummery::create($DailyQueueSummery);

        // $date = '2024-10-08';

        $results = DB::connection('mysql-old')
            ->table('queuecount')
            ->where('date', 'like', "$date%")
            ->select('queuename')
            ->selectRaw('SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as total_calls')
            ->selectRaw('SUM(CASE WHEN agent IS NOT NULL THEN 1 ELSE 0 END) as total_answered')
            ->selectRaw('COUNT(DISTINCT agent) as agent_count')
            ->selectRaw('SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) - SUM(CASE WHEN agent IS NOT NULL THEN 1 ELSE 0 END) as total_abandoned')
            ->groupBy('queuename')
            ->get();



        foreach ($results as $result) {

            DailyQueueSummery::updateOrCreate(
                [
                    'date' => $date,
                    'queue' => $result->queuename,
                    'calls' => $result->total_calls,
                    'answered' => $result->total_answered,
                    'abandoned' => $result->total_abandoned,
                    'agents' => $result->agent_count,
                ]
            );
        }




        return Command::SUCCESS;
    }
}

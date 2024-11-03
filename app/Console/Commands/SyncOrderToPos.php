<?php

namespace App\Console\Commands;

use App\Jobs\SyncOrder;
use App\Mail\OrderSynFailEmail;
use App\Mail\TestMail;
use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SyncOrderToPos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:order';

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

        // $id = $this->argument('ticketId');
        // $ticket = Ticket::find($id);
        // SyncOrder::dispatch($ticket)->onQueue('high');
        // $ticket = Ticket::with('lead', 'category', 'subCategory', 'items', 'items.item', 'outlet')->where('id', 42)->first();
        // Mail::to('macorera@gmail.com')->send(new OrderSynFailEmail($ticket,"POS Middleware Connector Down"));
        Mail::to('macorera@gmail.com')->send(new TestMail());
        return Command::SUCCESS;
    }
}

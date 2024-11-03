<?php

namespace App\Jobs;

use App\Mail\OrderSynFailEmail;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SyncOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Ticket $ticket;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $this->ticket->createPosOrder();

        $email = config('app.debug') ? "macorera@gmail.com" : "callcenter.kottugrand@gmail.com";
        try {

            $response = Http::post(config('auso.mrk_api_url') . "/order", [
                'order_details' => $this->ticket
            ]);
            Log::info($response);
            if ($response->successful()) {
                $this->ticket->synced_at = Carbon::now();
                $this->ticket->is_synced = 1;
                $this->ticket->ticket_status_id = 2;
                $this->ticket->bill_no = $response->body();

                $this->ticket->logActivity("Start Processing");

                Mail::to($email)->send(new OrderSynFailEmail($this->ticket, $this->ticket->outlet->title . 'Order Placed Ref: ' . $this->ticket->bill_no, 1));
            } else {
                $this->ticket->is_synced = 0;
                $this->ticket->bill_no = "Sync Failed : Outlet Server Down";
                Mail::to($email)->send(new OrderSynFailEmail($this->ticket, $this->ticket->outlet->title . 'Outlet Server Down Ref: ' . $this->ticket->order_ref));
                $this->ticket->logActivity("Sync Failed : Outlet Server Down");
            }
            $this->ticket->save();
        } catch (\Throwable $th) {
            Mail::to($email)->send(new OrderSynFailEmail($this->ticket, 'POS Middleware Connector Down Ref: ' . $this->ticket->order_ref));
            
            $this->ticket->is_synced = 0;
            $this->ticket->bill_no = "Sync Failed : POS Middleware Connector Down";
            $this->ticket->save();
            $this->ticket->logActivity("Sync Failed : POS Middleware Connector Down");
            
            Log::error($th);
        }
    }
}

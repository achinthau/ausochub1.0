<?php

namespace App\Jobs;

use App\Models\Feed;
use App\Models\FeedContact;
use App\Models\FeedContactValid;
use App\Models\FeedContactInValid;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessFeedFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $feedId;
    protected int $batchSize = 5000;

    public function __construct(int $feedId)
    {
        $this->feedId = $feedId;
    }

    public function handle()
    {
        $feed = Feed::find($this->feedId);
        if (!$feed) {
            Log::error("ProcessFeedFile: Feed not found with ID {$this->feedId}");
            return;
        }

        Log::info("Validating contacts for Feed ID: {$this->feedId}");

        $totalValid   = 0;
        $totalInvalid = 0;

        FeedContact::where('feed_id', $this->feedId)
            ->orderBy('id')
            ->chunk($this->batchSize, function ($contacts) use (&$totalValid, &$totalInvalid) {
                $valid   = [];
                $invalid = [];

                foreach ($contacts as $contact) {
                    $phone1 = $contact->contact_no_01 ?? '';
                    $phone2 = $contact->contact_no_02 ?? '';
                    $cleanPhone = preg_replace('/\D/', '', $phone1);
                    $cleanPhone2 = preg_replace('/\D/', '', $phone2);

                    // Normalize Sri Lankan numbers
                    if (str_starts_with($cleanPhone, '94')) {
                        $cleanPhone = '0' . substr($cleanPhone, 2);
                    } elseif (str_starts_with($cleanPhone, '9') && strlen($cleanPhone) === 9) {
                        $cleanPhone = '0' . $cleanPhone;
                    } elseif (str_starts_with($cleanPhone, '0')) {
                        // 
                    }

                    if (str_starts_with($cleanPhone2, '94')) {
                        $cleanPhone2 = '0' . substr($cleanPhone2, 2);
                    } elseif (str_starts_with($cleanPhone2, '9') && strlen($cleanPhone2) === 9) {
                        $cleanPhone2 = '0' . $cleanPhone2;
                    } elseif (str_starts_with($cleanPhone2, '0')) {
                        // 
                    }

                    $data = [
                        'feed_id'       => $contact->feed_id,
                        'contact_no_01'         => $contact->contact_no_01,
                        'contact_no_02'         => $contact->contact_no_02,
                        'customer_name' => $contact->customer_name,
                        'data'          => $contact->data,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];

                    if ($cleanPhone && preg_match('/^\d{9,10}$/', $cleanPhone)) {
                        $valid[] = $data;
                    } else {
                        if ($cleanPhone2 && preg_match('/^\d{9,10}$/', $cleanPhone2)) {
                        $valid[] = $data;
                    } else {
                        $invalid[] = $data;
                    }
                    }

                    Log::debug("Contact {$contact->id} | Original: {$contact->phone} → Normalized: {$cleanPhone} | " .
                        ($cleanPhone && preg_match('/^\d{9,10}$/', $cleanPhone) ? 'VALID' : 'INVALID'));
                }

                if (!empty($valid)) {
                    FeedContactValid::insert($valid);
                    $totalValid += count($valid);
                    Log::info(count($valid) . " valid contacts inserted.");
                }

                if (!empty($invalid)) {
                    FeedContactInValid::insert($invalid);
                    $totalInvalid += count($invalid);
                    Log::info(count($invalid) . " invalid contacts inserted.");
                }
            });

        $feed->update(['status' => 'completed']);
        Log::info("Feed ID {$this->feedId} validation finished. Valid: {$totalValid}, Invalid: {$totalInvalid}");
    }
}

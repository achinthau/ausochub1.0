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
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Row;

class ProcessFeedFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $feedId;
    protected string $filePath;
    protected int $batchSize = 5000;

    public function __construct(int $feedId, string $filePath)
    {
        $this->feedId = $feedId;
        $this->filePath = $filePath;
    }

    public function handle()
    {
        $feed = Feed::find($this->feedId);
        if (!$feed) {
            Log::error("ProcessFeedFile: Feed not found with ID {$this->feedId}");
            return;
        }

        $feed->update(['status' => 'processing']);
        Log::info("Processing Feed ID: {$this->feedId}");

        // Step 1: Import Excel → FeedContact
        Excel::import(new class($this->feedId, $this->batchSize) implements OnEachRow, WithChunkReading {
            protected int $feedId;
            protected int $batchSize;
            protected array $buffer = [];
            static ?array $header = null;

            public function __construct(int $feedId, int $batchSize)
            {
                $this->feedId = $feedId;
                $this->batchSize = $batchSize;
            }

            public function onRow(Row $row)
            {
                $rowArray = $row->toArray();

                if (!self::$header) {
                    self::$header = array_map(fn($h) => strtolower(trim($h)), $rowArray);
                    Log::info("Excel headers detected: " . implode(", ", self::$header));
                    return;
                }

                if (empty(array_filter($rowArray))) return;

                $rowData = array_combine(self::$header, $rowArray);
                if (!$rowData) {
                    Log::warning("Failed to combine row with header: " . json_encode($rowArray));
                    return;
                }

                $phone = $rowData['phone'] ?? $rowData['contact'] ?? null;
                $phone = is_string($phone) ? preg_replace('/\D/', '', $phone) : $phone;

                $this->buffer[] = [
                    'feed_id'       => $this->feedId,
                    'phone'         => $phone,
                    'customer_name' => $rowData['customer name'] ?? null,
                    'data'          => json_encode($rowData),
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];

                if (count($this->buffer) >= $this->batchSize) {
                    FeedContact::insert($this->buffer);
                    Log::info(count($this->buffer) . " contacts inserted into FeedContact.");
                    $this->buffer = [];
                }
            }

            public function chunkSize(): int
            {
                return $this->batchSize;
            }

            public function __destruct()
            {
                if (!empty($this->buffer)) {
                    FeedContact::insert($this->buffer);
                    Log::info(count($this->buffer) . " contacts inserted into FeedContact (final batch).");
                    $this->buffer = [];
                }
            }
        }, $this->filePath);

        // Step 2: Validate contacts → FeedContactValid / FeedContactInValid
        $totalValid = 0;
        $totalInvalid = 0;

        FeedContact::where('feed_id', $this->feedId)
            ->orderBy('id')
            ->chunk($this->batchSize, function ($contacts) use (&$totalValid, &$totalInvalid) {
                $valid = [];
                $invalid = [];

                foreach ($contacts as $contact) {
                    $cleanPhone = $contact->phone ? preg_replace('/\D/', '', $contact->phone) : null;

                    $data = [
                        'feed_id'       => $contact->feed_id,
                        'phone'         => $cleanPhone,
                        'customer_name' => $contact->customer_name,
                        'data'          => $contact->data,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];

                    if ($cleanPhone && preg_match('/^\d{9,10}$/', $cleanPhone)) {
                        $valid[] = $data;
                    } else {
                        $invalid[] = $data;
                    }
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
        Log::info("Feed ID {$this->feedId} processed successfully. Valid: {$totalValid}, Invalid: {$totalInvalid}");
    }
}

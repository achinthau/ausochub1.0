<?php

namespace App\Console\Commands;

use App\Models\MessengerMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchMessengerNames extends Command
{
    protected $signature   = 'messenger:fetch-names {--force : Re-fetch even if name already set}';
    protected $description = 'Fetch sender display names from Meta Graph API for all unknown Messenger PSIDs';

    public function handle(): int
    {
        $token = env('FACEBOOK_PAGE_ACCESS_TOKEN');

        if (!$token) {
            $this->error('FACEBOOK_PAGE_ACCESS_TOKEN is not set in .env');
            return self::FAILURE;
        }

        // Get distinct PSIDs that need a name
        $query = MessengerMessage::selectRaw('DISTINCT sender_id')
            ->where('from_me', false);

        if (!$this->option('force')) {
            $query->where(function ($q) {
                $q->whereNull('sender_name')
                  ->orWhere('sender_name', 'Facebook User');
            });
        }

        $senderIds = $query->pluck('sender_id');

        if ($senderIds->isEmpty()) {
            $this->info('No PSIDs need name resolution. Use --force to re-fetch all.');
            return self::SUCCESS;
        }

        $this->info("Found {$senderIds->count()} PSID(s) to resolve...");
        $bar = $this->output->createProgressBar($senderIds->count());
        $bar->start();

        $resolved = 0;
        $failed   = 0;

        foreach ($senderIds as $senderId) {
            try {
                $response = Http::timeout(10)->get(
                    "https://graph.facebook.com/v19.0/{$senderId}",
                    ['fields' => 'name', 'access_token' => $token]
                );

                if ($response->successful()) {
                    $data = $response->json();
                    $name = $data['name']
                        ?? trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''))
                        ?: null;

                    if ($name) {
                        MessengerMessage::where('sender_id', $senderId)
                            ->update(['sender_name' => $name]);
                        $this->newLine();
                        $this->line("  ✓ {$senderId} → <info>{$name}</info>");
                        $resolved++;
                    } else {
                        $this->newLine();
                        $this->warn("  ? {$senderId} → no name in response: " . $response->body());
                        $failed++;
                    }
                } else {
                    $this->newLine();
                    $this->warn("  ✗ {$senderId} → HTTP {$response->status()}: " . substr($response->body(), 0, 120));
                    $failed++;
                }
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("  ✗ {$senderId} → " . $e->getMessage());
                $failed++;
            }

            $bar->advance();
            usleep(300_000); // 300 ms between requests – respect rate limits
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Done. Resolved: {$resolved} | Failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}

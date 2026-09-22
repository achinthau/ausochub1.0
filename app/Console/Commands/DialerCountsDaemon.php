<?php

namespace App\Console\Commands;

use App\Models\FeedContactValid;
use App\Models\FeedContactValidReport;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Throwable;

class DialerCountsDaemon extends Command
{
    protected $signature = 'dialer:counts:sync {--once : Run a single tick and exit}';

    protected $description = 'Continuously aggregate per-agent dialed/answered/failed counts into Redis from feed contact attempts';

    /**
     * Single-writer lock so only one counter service instance runs.
     */
    protected const LOCK_KEY = 'dialer:counts:lock';

    protected int $interval = 30;

    public function __construct()
    {
        parent::__construct();

        $this->interval = max(5, (int) env('DIALER_COUNTS_INTERVAL', 30));
    }

    public function handle(): int
    {
        // Reap a lock left behind by a worker that died without releasing it
        // (e.g. killed during a supervisor reload) so a fresh start is not
        // blocked for the full lock TTL.
        $this->releaseStaleLock();

        // Single ticks act as a cron keep-alive: they only run when no
        // long-running daemon holds the lock.
        $isOnce = (bool) $this->option('once');

        if ($isOnce) {
            if (!(bool) Redis::set(self::LOCK_KEY, (string) getmypid(), 'EX', 300, 'NX')) {
                return Command::SUCCESS;
            }

            try {
                $this->tick();
            } finally {
                // Release immediately so the next keep-alive tick is not
                // blocked for the full lock TTL.
                Redis::del(self::LOCK_KEY);
            }

            return Command::SUCCESS;
        }

        if (!(bool) Redis::set(self::LOCK_KEY, (string) getmypid(), 'EX', 300, 'NX')) {
            $this->error('Another dialer counter service instance is already running.');

            return Command::FAILURE;
        }

        $this->info('Dialer counter service started. Interval: ' . $this->interval . 's');

        while (true) {
            try {
                $this->renewLock();
                $this->tick();
            } catch (Throwable $e) {
                Log::error('dialer:counts:sync tick failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            sleep($this->interval);
        }

        return Command::SUCCESS;
    }

    protected function renewLock(): void
    {
        Redis::set(self::LOCK_KEY, (string) getmypid(), 'EX', 300);
    }

    /**
     * Drop the lock when the process that owns it is no longer alive.
     */
    protected function releaseStaleLock(): void
    {
        $owner = Redis::get(self::LOCK_KEY);

        if ($owner === null || !is_numeric($owner)) {
            return;
        }

        if (function_exists('posix_kill') && !@posix_kill((int) $owner, 0)) {
            Redis::del(self::LOCK_KEY);
        }
    }

    protected function tick(): void
    {
        $today = Carbon::now()->format('Y-m-d');

        foreach ($this->queryCounts() as $row) {
            $userId = (int) $row->agent_id;

            if ($userId <= 0) {
                continue;
            }

            $payload = [
                'dialed' => (int) $row->dialed,
                'answered' => (int) $row->answered,
                'failed' => (int) $row->not_answered,
            ];

            // Date-keyed so the daily reset is just a key rotation; the 48h
            // TTL keeps yesterday's key briefly readable without piling up.
            Redis::setex("dialer:counts:{$today}:{$userId}", 60 * 60 * 48, json_encode($payload));
        }
    }

    /**
     * Preserve the original per-agent count semantics:
     *   dialed   = FeedContactValid (status not null) + answered + not answered
     *   answered = FeedContactValidReport (status 1, 41)
     *   failed   = FeedContactValidReport (status 222, 42)  [and dialed - answered]
     *
     * The agent is taken from updated_by instead of an extension match against
     * the Asterisk callcount table.
     */
    protected function queryCounts(): array
    {
        $today = today();

        $validDialed = FeedContactValid::query()
            ->whereDate('attempted_at', $today)
            ->whereNotNull('status')
            ->whereNotNull('updated_by')
            ->selectRaw('updated_by as agent_id, COUNT(*) as total')
            ->groupBy('updated_by')
            ->pluck('total', 'agent_id');

        $answered = FeedContactValidReport::query()
            ->whereDate('attempted_at', $today)
            ->whereNotNull('updated_by')
            ->whereIn('status', [1, 41])
            ->selectRaw('updated_by as agent_id, COUNT(*) as total')
            ->groupBy('updated_by')
            ->pluck('total', 'agent_id');

        $notAnswered = FeedContactValidReport::query()
            ->whereDate('attempted_at', $today)
            ->whereNotNull('updated_by')
            ->whereIn('status', [222, 42])
            ->selectRaw('updated_by as agent_id, COUNT(*) as total')
            ->groupBy('updated_by')
            ->pluck('total', 'agent_id');

        $agentIds = $validDialed->keys()
            ->merge($answered->keys())
            ->merge($notAnswered->keys())
            ->unique();

        $rows = [];

        foreach ($agentIds as $agentId) {
            $base = (int) $validDialed->get($agentId, 0);
            $answeredCount = (int) $answered->get($agentId, 0);
            $notAnsweredCount = (int) $notAnswered->get($agentId, 0);

            $dialed = $base + $answeredCount + $notAnsweredCount;

            $rows[] = (object) [
                'agent_id' => $agentId,
                'dialed' => $dialed,
                'answered' => $answeredCount,
                // failed = dialed - answered (== valid dials + not answered)
                'not_answered' => $dialed - $answeredCount,
            ];
        }

        return $rows;
    }
}
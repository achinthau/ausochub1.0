<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Repositories\DialerNumberService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Throwable;

class DialerNumbersDaemon extends Command
{
    protected $signature = 'dialer:numbers:dispatch {--once : Run a single tick and exit (useful for cron keep-alive)}';

    protected $description = 'Continuously assign dialer numbers to logged-in outbound agents and publish them to Redis';

    /**
     * Single-writer lock so only one dispatcher instance runs.
     */
    protected const LOCK_KEY = 'dialer:dispatcher:lock';

    protected int $interval = 2;

    protected DialerNumberService $service;

    public function __construct()
    {
        parent::__construct();

        $this->interval = max(1, (int) env('DIALER_NUMBER_INTERVAL', 2));
        $this->service = new DialerNumberService();
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
            if (!(bool) Redis::set(self::LOCK_KEY, (string) getmypid(), 'EX', 60, 'NX')) {
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

        if (!$this->acquireLock()) {
            $this->error('Another dialer dispatcher instance is already running.');

            return Command::FAILURE;
        }

        $this->info('Dialer number dispatcher started. Interval: ' . $this->interval . 's');

        while (true) {
            try {
                $this->renewLock();
                $this->tick();
            } catch (Throwable $e) {
                Log::error('dialer:numbers:dispatch tick failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            sleep($this->interval);
        }

        return Command::SUCCESS;
    }

    protected function tick(): void
    {
        $seen = [];

        $dialerUserIds = $this->dialerBoundUserIds();

        if (empty($dialerUserIds)) {
            $this->cleanupStaleNumbers([]);

            return;
        }

        $users = User::with(['languages'])->whereIn('id', $dialerUserIds)->get()->keyBy('id');

        foreach ($dialerUserIds as $userId) {
            $user = $users->get($userId);

            if (!$user) {
                continue;
            }

            $seen[] = $userId;
            $this->dispatchForUser($user);
        }

        $this->cleanupStaleNumbers($seen);
    }

    protected function dialerBoundUserIds(): array
    {
        $loggedInIds = DB::table('sessions')
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->unique()
            ->values()
            ->toArray();

        if (empty($loggedInIds)) {
            return [];
        }

        $dialerIds = [];

        foreach ($loggedInIds as $userId) {
            if (Redis::get("user:{$userId}:bound_type") === 'dialer') {
                $dialerIds[] = (int) $userId;
            }
        }

        return $dialerIds;
    }

    protected function dispatchForUser(User $user): void
    {
        $userId = (int) $user->id;

        // Only refresh the cached "queued" flag when it has expired, so we
        // avoid hitting MySQL for it on every tick.
        if ($this->service->hasQueued($userId) === null) {
            $this->service->setQueued($userId, $user->currentQueues()->active()->exists());
        }

        // Always derive the current contact from MySQL using the same
        // selection the lead window's Next Customer uses, so the dashboard
        // never keeps showing a contact the agent has already moved past.
        $current = $this->service->resolveCurrentContact($user);

        if ($current === null) {
            $this->service->setShown($userId, [
                'contact_id' => null,
                'reason' => 'No available contacts in your assigned campaigns.',
            ]);

            return;
        }

        $shown = $this->service->getShown($userId);

        if ($shown && (int) ($shown['contact_id'] ?? 0) === (int) $current['contact_id']) {
            $this->refreshTtl($userId, $current);

            return;
        }

        $this->service->setShown($userId, $current);
    }

    /**
     * Refresh the shown-number TTL only when it is about to expire, so we keep
     * the key alive during long idle/queued periods without rewriting it on
     * every tick.
     */
    protected function refreshTtl(int $userId, array $current): void
    {
        $ttl = Redis::ttl($this->service->shownKey($userId));

        if ($ttl !== false && $ttl !== -1 && $ttl < 15) {
            $this->service->setShown($userId, $current);
        }
    }

    /**
     * Drop published numbers for agents who are no longer logged in / not
     * dialer-bound anymore, so they never linger after logout.
     */
    protected function cleanupStaleNumbers(array $seen): void
    {
        $keys = Redis::keys(DialerNumberService::SHOWN_PREFIX . ':*');

        if (empty($keys)) {
            return;
        }

        foreach ($keys as $key) {
            if (preg_match('/:(\d+)$/', (string) $key, $matches) !== 1) {
                continue;
            }

            $userId = (int) $matches[1];

            if (!in_array($userId, $seen, true)) {
                Redis::del($this->service->shownKey($userId));
                Redis::del($this->service->queuedKey($userId));
            }
        }
    }

    protected function acquireLock(): bool
    {
        return (bool) Redis::set(self::LOCK_KEY, (string) getmypid(), 'EX', 60, 'NX');
    }

    protected function renewLock(): void
    {
        Redis::set(self::LOCK_KEY, (string) getmypid(), 'EX', 60);
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
}
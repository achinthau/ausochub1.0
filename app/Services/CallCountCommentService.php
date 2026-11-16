<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Throwable;

class CallCountCommentService
{
    /**
     * Redis connection that stores the dialer call links (DB 9).
     */
    private const REDIS_CONNECTION = 'call_links';

    /**
     * Redis hash field that holds the Asterisk uniqueid.
     */
    private const LINK_FIELD = 'linkid';

    /**
     * Bind a value as binary so it can be concatenated with the legacy
     * latin1 comment columns without an illegal collation mix.
     */
    private const BINARY_PARAM = 'CONVERT(? USING binary)';

    /**
     * Cached comment column lengths, keyed by table name.
     *
     * @var array<string, int|null>
     */
    private static array $commentLengths = [];

    /**
     * Append a reaction/comment to the outbound callcount row and its
     * archived report copy.
     *
     * The dialer writes `auso:call:{extension}:{phone}` hashes with a `linkid`
     * field pointing at the Asterisk uniqueid. That link is used first so the
     * comment lands on the exact call. When the link is unavailable the latest
     * matching record for the phone number is used instead.
     *
     * @param  array<int, string|null>  $phones
     */
    public function appendOutboundComment(
        ?string $comment,
        ?string $extension,
        array $phones,
        ?int $reaction = null,
        ?int $agentId = null
    ): void {
        $comment = trim((string) $comment);
        $entry = $comment !== '' ? $this->formatEntry($comment, $agentId) : null;

        $phones = $this->normalizePhones($phones);
        $linkedUniqueId = $this->resolveUniqueId($extension, $phones);

        $callCount = $this->findCallCount($linkedUniqueId, $phones);
        $uniqueId = $linkedUniqueId;

        if ($callCount) {
            // Keep the report lookup on the same call that was just updated,
            // even when the Redis link was stale and the phone fallback won.
            $uniqueId = $callCount->uniqueid ?: $linkedUniqueId;

            $this->updateRow('callcount', $callCount->id, $entry, $reaction);
        }

        foreach ($this->findReportIds($uniqueId, $phones) as $reportId) {
            $this->updateRow('au_callcount_report', $reportId, $entry, $reaction);
        }
    }

    /**
     * Read the linked uniqueid from the dialer's Redis hash.
     *
     * @param  array<int, string>  $phones
     */
    private function resolveUniqueId(?string $extension, array $phones): ?string
    {
        $extension = trim((string) $extension);

        if ($extension === '' || $phones === []) {
            return null;
        }

        try {
            $redis = Redis::connection(self::REDIS_CONNECTION);

            foreach ($phones as $phone) {
                $key = "auso:call:{$extension}:{$phone}";
                $value = $redis->hget($key, self::LINK_FIELD);

                if (is_string($value) && trim($value) !== '') {
                    return trim($value);
                }
            }
        } catch (Throwable $e) {
            Log::warning('dialer.call_link.redis_lookup_failed', [
                'extension' => $extension,
                'phones' => $phones,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Find the active callcount row by linked uniqueid, or the latest
     * outbound row for the phone number as a fallback.
     *
     * @param  array<int, string>  $phones
     */
    private function findCallCount(?string $uniqueId, array $phones): ?object
    {
        if ($uniqueId) {
            $linked = $this->callCountQuery()
                ->where('uniqueid', $uniqueId)
                ->orderByDesc('id')
                ->first();

            if ($linked) {
                return $linked;
            }
        }

        if ($phones === []) {
            return null;
        }

        $latest = $this->callCountQuery()
            ->whereIn('dnis', $phones)
            ->where('direction', 'out')
            ->where('status', 1)
            ->orderByDesc('id')
            ->first();

        if ($latest) {
            return $latest;
        }

        // Some outbound calls are stored as "disout" or with a different
        // status, so fall back to the latest outbound record.
        return $this->callCountQuery()
            ->whereIn('dnis', $phones)
            ->whereIn('direction', ['out', 'disout'])
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Find the report rows by linked uniqueid, or the latest outbound row for
     * the phone number as a fallback.
     *
     * The report table may hold more than one row per call, so all rows for
     * the linked uniqueid are updated (matching the previous behaviour).
     *
     * @param  array<int, string>  $phones
     * @return array<int, int>
     */
    private function findReportIds(?string $uniqueId, array $phones): array
    {
        if ($uniqueId) {
            $linkedIds = $this->reportQuery()
                ->where('uniqueid', $uniqueId)
                ->pluck('id')
                ->all();

            if ($linkedIds !== []) {
                return array_map('intval', $linkedIds);
            }
        }

        if ($phones === []) {
            return [];
        }

        $latest = $this->reportQuery()
            ->whereIn('dnis', $phones)
            ->where('direction', 'out')
            ->where('status', 1)
            ->orderByDesc('id')
            ->first();

        if ($latest) {
            return [(int) $latest->id];
        }

        $fallback = $this->reportQuery()
            ->whereIn('dnis', $phones)
            ->whereIn('direction', ['out', 'disout'])
            ->orderByDesc('id')
            ->first();

        return $fallback ? [(int) $fallback->id] : [];
    }

    private function callCountQuery()
    {
        return DB::connection('mysql-old')
            ->table('callcount')
            ->select('id', 'uniqueid');
    }

    private function reportQuery()
    {
        return DB::connection('mysql-old')
            ->table('au_callcount_report')
            ->select('id', 'uniqueid');
    }

    /**
     * Atomically append the entry (when present) and store the reaction.
     *
     * @param  'callcount'|'au_callcount_report'  $table
     */
    private function updateRow(string $table, int $id, ?string $entry, ?int $reaction): void
    {
        $connection = DB::connection('mysql-old');

        if ($entry === null) {
            $connection->table($table)->where('id', $id)->update([
                'customer_reaction' => $reaction,
            ]);

            return;
        }

        $maxLength = $this->commentMaxLength($table);

        // Read-modify-write is avoided so two agents reacting at the same time
        // cannot drop each other's comment.
        if ($maxLength === null) {
            $connection->update(
                "UPDATE `{$table}` "
                . 'SET `comment` = CASE WHEN `comment` IS NULL OR TRIM(`comment`) = \'\' '
                . 'THEN ? ELSE CONCAT(`comment`, ' . self::BINARY_PARAM . ', ' . self::BINARY_PARAM . ') END, '
                . '`customer_reaction` = ? '
                . 'WHERE `id` = ?',
                [$entry, PHP_EOL, $entry, $reaction, $id]
            );

            return;
        }

        // Legacy varchar columns keep the newest entries when the history
        // would overflow. MySQL runs this server in non-strict mode, so an
        // unguarded append would silently cut off the newest comment.
        $connection->update(
            "UPDATE `{$table}` "
            . 'SET `comment` = CASE '
            . 'WHEN `comment` IS NULL OR TRIM(`comment`) = \'\' THEN RIGHT(?, ?) '
            . 'WHEN CHAR_LENGTH(`comment`) + CHAR_LENGTH(?) + CHAR_LENGTH(?) <= ? '
            . 'THEN CONCAT(`comment`, ' . self::BINARY_PARAM . ', ' . self::BINARY_PARAM . ') '
            . 'ELSE RIGHT(CONCAT(`comment`, ' . self::BINARY_PARAM . ', ' . self::BINARY_PARAM . '), ?) END, '
            . '`customer_reaction` = ? '
            . 'WHERE `id` = ?',
            [
                $entry, $maxLength,
                PHP_EOL, $entry, $maxLength,
                PHP_EOL, $entry,
                PHP_EOL, $entry, $maxLength,
                $reaction, $id,
            ]
        );
    }

    /**
     * Maximum character length of the comments column, or null when it cannot
     * be determined (in which case the append is not length-guarded).
     */
    private function commentMaxLength(string $table): ?int
    {
        if (array_key_exists($table, self::$commentLengths)) {
            return self::$commentLengths[$table];
        }

        try {
            $connection = DB::connection('mysql-old');

            $length = $connection->table('information_schema.COLUMNS')
                ->where('TABLE_SCHEMA', $connection->getDatabaseName())
                ->where('TABLE_NAME', $table)
                ->where('COLUMN_NAME', 'comment')
                ->value('CHARACTER_MAXIMUM_LENGTH');
        } catch (Throwable $e) {
            $length = null;
        }

        return self::$commentLengths[$table] = $length !== null ? (int) $length : null;
    }

    /**
     * Normalize and de-duplicate the phone candidates, preserving order so the
     * raw/local format used by the dialer is tried first.
     *
     * @param  array<int, string|null>  $phones
     * @return array<int, string>
     */
    private function normalizePhones(array $phones): array
    {
        $normalized = [];

        foreach ($phones as $phone) {
            $phone = preg_replace('/\s+/', '', (string) $phone);

            if ($phone === '') {
                continue;
            }

            $normalized[] = $phone;
        }

        return array_values(array_unique($normalized));
    }

    /**
     * Build the same "[date | agent] comment" entry used by feed contacts.
     */
    private function formatEntry(string $comment, ?int $agentId = null): string
    {
        $agentId = $agentId ?: auth()->id();
        $agentName = $agentId ? optional(User::find($agentId))->name : null;

        $agentLabel = $agentName
            ? "Agent {$agentId} - {$agentName}"
            : ($agentId ? "Agent {$agentId}" : 'Agent System');

        return '[' . now()->format('Y-m-d h:i A') . ' | ' . $agentLabel . '] ' . $comment;
    }
}

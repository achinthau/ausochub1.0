<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class SipCredentialProvisioner
{
    private const CACHE_PREFIX = 'ausophone:sip:';

    /**
     * @return array{password: string, expires_in: int, rotated: bool}
     */
    public function issue(string $extension, bool $force = false): array
    {
        if (config('ausophone.credential_strategy') === 'static') {
            return [
                'password'   => $this->staticPassword($extension),
                'expires_in' => (int) config('ausophone.credential_ttl'),
                'rotated'    => false,
            ];
        }

        $ttl = (int) config('ausophone.credential_ttl');
        $key = self::CACHE_PREFIX.$extension;

        if (! $force && ($existing = Cache::get($key))) {
            return [
                'password'   => $existing['password'],
                'expires_in' => max(1, $existing['expires_at'] - time()),
                'rotated'    => false,
            ];
        }

        $password = Str::random(32);

        Cache::put($key, [
            'password'   => $password,
            'expires_at' => time() + $ttl,
        ], $ttl);

        return ['password' => $password, 'expires_in' => $ttl, 'rotated' => true];
    }

    public function revoke(string $extension): void
    {
        Cache::forget(self::CACHE_PREFIX.$extension);
    }

    private function staticPassword(string $extension): string
    {
        // The Asterisk PBX extension secret lives on au_exten (mysql-old), not
        // on the agent row. Read the plaintext password from there.
        $ext = \App\Models\Extension::query()
            ->where('extension', $extension)
            ->first();

        if (! $ext) {
            throw new RuntimeException("No extension found for {$extension}");
        }

        return (string) $ext->password;
    }
}

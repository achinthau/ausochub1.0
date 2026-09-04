<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SipCredentialProvisioner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class PhoneCredentialController extends Controller
{
    public function __construct(private readonly SipCredentialProvisioner $provisioner)
    {
        $this->middleware('auth');
        $this->middleware('throttle:60,1');
    }

    /**
     * Resolve the agent's SIP extension from the users row or the agent row.
     */
    private function extensionFor($user): ?string
    {
        if (! empty($user->extension)) {
            return (string) $user->extension;
        }

        $agent = $user->agent;

        return $agent && ! empty($agent->extension) ? (string) $agent->extension : null;
    }

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $extension = $this->extensionFor($user);

        if (! $extension) {
            return response()->json([
                'message' => 'No SIP extension is assigned to this agent.',
            ], 422);
        }

        try {
            $credential = $this->provisioner->issue($extension);
        } catch (Throwable $e) {
            Log::error('Could not issue SIP credentials', [
                'agent_id' => $user->id,
                'error'    => $e->getMessage(),
            ]);

            return response()->json(['message' => 'The phone system is unavailable.'], 503);
        }

        return $this->credentialResponse($user, $extension, $credential['password'], $credential['expires_in'])
            ->header('Cache-Control', 'no-store, private');
    }

    /**
     * Manual save from the phone's Settings panel (sip-credentials-url).
     *
     * The phone POSTs { extension, password } and expects a full credential
     * object back, which it then uses to register.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'extension' => ['required', 'string', 'max:16'],
            'password'  => ['required', 'string'],
        ]);

        $user = $request->user();
        $extension = $data['extension'];

        // Persist the password to the Asterisk extension table (au_exten) so a
        // later auto-login resolves the same plaintext value.
        try {
            $ext = \App\Models\Extension::where('extension', $extension)->first();
            if ($ext) {
                $ext->password = $data['password'];
                $ext->save();
            }
        } catch (Throwable $e) {
            Log::warning('Could not persist SIP password', ['error' => $e->getMessage()]);
        }

        return $this->credentialResponse($user, $extension, $data['password'], (int) config('ausophone.credential_ttl'))
            ->header('Cache-Control', 'no-store, private');
    }

    public function destroy(Request $request): JsonResponse
    {
        $extension = $this->extensionFor($request->user());

        if ($extension) {
            $this->provisioner->revoke($extension);
        }

        return response()->json(['ok' => true]);
    }

    private function credentialResponse($user, string $extension, string $password, int $expiresIn): JsonResponse
    {
        return response()->json([
            'extension'  => $extension,
            'sip_domain' => config('ausophone.sip_domain'),
            'ws_url'     => config('ausophone.ws_url'),
            'password'   => $password,

            'display_name'     => $user->name,
            'expires_in'       => $expiresIn,
            'register_expires' => config('ausophone.register_expires'),
            'ice_servers'      => config('ausophone.ice_servers'),
            'auto_answer'      => false,
            'branding'         => $this->brandingFor($user),
            'agent'            => [
                'id'        => $user->id,
                'name'      => $user->name,
                'extension' => $extension,
            ],
        ]);
    }

    private function brandingFor($user): array
    {
        return array_filter([
            'logo'            => config('ausophone.branding.logo'),
            'company_name'    => config('ausophone.branding.company_name'),
            'primary_color'   => config('ausophone.branding.primary_color'),
            'show_powered_by' => config('ausophone.branding.show_powered_by'),
            'theme'           => config('ausophone.branding.theme'),
        ], fn ($v) => $v !== null);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PhoneCallController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * CLI → lead lookup (screen-pop).
     */
    public function lookup(Request $request): JsonResponse
    {
        $request->validate(['phone' => ['required', 'string', 'max:32']]);

        $phone = $request->query('phone');
        $lead = Lead::where('contact_number', $phone)->first();

        if (! $lead) {
            return response()->json([
                'found' => false,
                'phone' => $phone,
                'name'  => null,
            ]);
        }

        return response()->json([
            'found'          => true,
            'id'             => $lead->id,
            'name'           => trim(($lead->first_name ?? '').' '.($lead->last_name ?? '')),
            'phone'          => $lead->contact_number,
            'email'          => $lead->email ?? null,
            'account_number' => $lead->nic ?? null,
            'notes'          => $lead->notes ?? null,
        ]);
    }

    /**
     * Store a call record reported by the browser phone.
     */
    public function storeCallRecord(Request $request): JsonResponse
    {
        $data = $request->validate([
            'call_id'         => ['required', 'string', 'max:191'],
            'direction'       => ['nullable', 'string'],
            'customer_number' => ['nullable', 'string', 'max:32'],
            'extension'       => ['nullable', 'string', 'max:16'],
            'start_time'      => ['nullable', 'date'],
            'answer_time'     => ['nullable', 'date'],
            'end_time'        => ['nullable', 'date'],
            'duration'        => ['nullable', 'integer', 'min:0'],
            'end_reason'      => ['nullable', 'string', 'max:64'],
        ]);

        return response()->json(['ok' => true], 201);
    }
}

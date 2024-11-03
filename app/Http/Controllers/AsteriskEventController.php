<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use App\Events\AsteriskEvent;
use Illuminate\Support\Facades\Log;

class AsteriskEventController extends Controller
{

    protected $startTime = []; // Array to store start time for each channel

    public function handleEvent(Request $request)
    {
        $data = $request->json()->all();
        Log::info("Received data in AsteriskEventController: " . json_encode($data));

        // Determine channel identifier (adjust as per your data structure)
        $channelStateDesc = $data['channelStateDesc'] ?? null;

        if ($channelStateDesc) {
            if (!isset($this->startTime[$channelStateDesc])) {
                // Start timer if not already started
                $this->startTime[$channelStateDesc] = now();
            }

            // Calculate duration since start time
            $duration = $this->startTime[$channelStateDesc]->diff(now())->format('%H:%I:%S');

            // Prepare updated data
            $updatedData = [
                'channel' => $data['channel'],
                'callerIDName' => $data['callerIDName'] ?? 'N/A',
                'channelStateDesc' => $data['channelStateDesc'] ?? 'N/A',
                'duration' => $duration,
            ];

            // Fire an event to update Livewire component
            event(new AsteriskEvent($updatedData));

        return response()->json(['message' => 'Events updated successfully']);
        }
        else {
            Log::error('Invalid data received from Node.js:', $data);
            return response()->json(['error' => 'Invalid data received'], 400);
        }
    }
}

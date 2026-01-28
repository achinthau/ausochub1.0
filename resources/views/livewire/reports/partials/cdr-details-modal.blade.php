<x-modal.card title="More Details" blur wire:model="showCdrDetailsModal" x-on:open="$wire.loadTranscription()">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="space-y-4">
            <div>
                <strong>Caller:</strong> {{ $callerName }}
            </div>
            @if($callerEmail)
                <div>
                    <strong>Caller Email:</strong> {{ $callerEmail }}
                </div>
            @endif
            @if($callerAddress)
                <div>
                    <strong>Caller Address:</strong> {{ $callerAddress }}
                </div>
            @endif
            @if($callerWhatsapp)
                <div>
                    <strong>Caller Whatsapp:</strong> {{ $callerWhatsapp }}
                </div>
            @endif
            @if($callerNic)
                <div>
                    <strong>Caller NIC:</strong> {{ $callerNic }}
                </div>
            @endif

            <hr>

            <div>
                <strong>Callee:</strong> {{ $calleeName }}
            </div>
            @if($calleeEmail)
                <div>
                    <strong>Callee Email:</strong> {{ $calleeEmail }}
                </div>
            @endif
            @if($calleeAddress)
                <div>
                    <strong>Callee Address:</strong> {{ $calleeAddress }}
                </div>
            @endif
            @if($calleeWhatsapp)
                <div>
                    <strong>Callee Whatsapp:</strong> {{ $calleeWhatsapp }}
                </div>
            @endif
            @if($calleeNic)
                <div>
                    <strong>Callee NIC:</strong> {{ $calleeNic }}
                </div>
            @endif

        </div>
    </div>

    <hr>
    <div class="mt-4">
    <strong class="block mb-2">Call Transcription:</strong>

    <div
        class="border rounded-lg p-3 bg-gray-50 text-sm
               max-h-64 overflow-y-auto whitespace-pre-wrap"
    >
        @if($isProcessing)
            <div class="flex items-center justify-center p-4">
                <svg class="animate-spin h-5 w-5 text-teal-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-teal-600 font-medium">Loading transcription...</span>
            </div>
        @elseif($transcription)
            {{ $transcription }}
        @else
            <span class="text-gray-500">No transcription available.</span>
        @endif
    </div>
</div>


    <x-slot name="footer">
        <div class="flex justify-between gap-x-4">
            <div>

            </div>

            <div class="flex">
                <x-button flat label="Close" x-on:click="close" />
            </div>
        </div>
    </x-slot>
</x-modal.card>
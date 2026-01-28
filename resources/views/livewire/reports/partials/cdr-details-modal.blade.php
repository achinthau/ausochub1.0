<x-modal.card title="More Details" blur wire:model="showCdrDetailsModal">
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

    <div class="mt-4">
    <strong class="block mb-2">Call Transcription:</strong>

    <div
        class="border rounded-lg p-3 bg-gray-50 text-sm
               max-h-64 overflow-y-auto whitespace-pre-wrap"
    >
        @if($transcription)
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
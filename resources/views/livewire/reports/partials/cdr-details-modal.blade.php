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
        <div>
            <strong>Receiver:</strong> {{ $calleeName }}
        </div>
        @if($calleeEmail)
        <div>
            <strong>Receiver Email:</strong> {{ $calleeEmail }}
        </div>
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

{{-- <div class="p-4">
    <h3 class="text-lg font-bold mb-2">Select Satisfaction Reason</h3>
    <div class="flex gap-2 items-center">
        <select wire:model="selectedSatisfactionReason" class="border p-2 rounded-md">
            <option value="">-- Select a reason --</option>
            @foreach($satisfactionReasons as $reason)
                <option value="{{ $reason }}">{{ $reason }}</option>
            @endforeach
        </select>
    </div>

    <h3 class="text-lg font-bold mt-4 mb-2">Select Dissatisfaction Reason</h3>
    <div class="flex gap-2 items-center">
        <select wire:model="selectedDissatisfactionReason" class="border p-2 rounded-md">
            <option value="">-- Select a reason --</option>
            @foreach($dissatisfactionReasons as $reason)
                <option value="{{ $reason }}">{{ $reason }}</option>
            @endforeach
        </select>
    </div>

    <h3 class="text-lg font-bold mt-4">Selected Reasons</h3>
    <div class="flex flex-wrap gap-2 mt-2">
        @foreach($selectedReasons as $reason)
            @php
                $colorClass = in_array($reason, $satisfactionReasons) ? 'bg-green-200 text-black-700' : 'bg-red-200 text-black-700';
            @endphp
            <span class="px-3 py-1 rounded-md flex items-center {{ $colorClass }}">
                {{ $reason }}
                <button wire:click="removeReason('{{ $reason }}')" class="ml-2 text-black font-bold">&times;</button>
            </span>
        @endforeach
    </div>
</div> --}}

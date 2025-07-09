@php
    $filepath = 'monitor_1/' . $uniqueid . '.wav';
    $hasFile = Storage::disk('public')->exists($filepath);
@endphp

<div class="flex items-center space-x-2" wire:key="{{ $uniqueid }}">
    @if ($hasFile)
        <a href="#" wire:click.prevent="$emit('downloadAudio', '{{ $uniqueid }}')" title="Download">
            <svg class="w-5 h-5 text-blue-500 hover:text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                 viewBox="0 0 16 16">
                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
            </svg>
        </a>
        <audio controls style="width: 200px;">
            <source src="{{ asset($filepath) }}" type="audio/wav">
            Your browser does not support the audio element.
        </audio>
    @else
        <span class="text-gray-400 text-sm">No file</span>
    @endif
</div>

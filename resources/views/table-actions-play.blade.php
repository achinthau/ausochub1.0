<div class="flex space-x-4 justify-around">
    @php
        $hasFile = Storage::disk('asterisk-media-server')->has($id . '.wav');
    @endphp
    @if ($hasFile)
        <a href="#" class="my-auto" role="button" if wire:click.prevent="download('{{ $id }}')">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                viewBox="0 0 16 16">
                <path
                    d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z">
                </path>
                <path
                    d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z">
                </path>
            </svg>
        </a>
    @else
        <div>
            <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                fill="currentColor">
                <path d="M0 0h24v24H0V0z" fill="none"></path>
                <path
                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8 0-1.85.63-3.55 1.69-4.9L16.9 18.31C15.55 19.37 13.85 20 12 20zm6.31-3.1L7.1 5.69C8.45 4.63 10.15 4 12 4c4.42 0 8 3.58 8 8 0 1.85-.63 3.55-1.69 4.9z">
                </path>
            </svg>
        </div>
    @endif

    {{-- @php
    
        
        $url = Storage::disk('asterisk-media-server')->url($id.'.wav');
        
        
    @endphp
    @dump($url)
    <audio controls>
        <source src="{{ $url }}" type="audio/wav">
      Your browser does not support the audio element.
      </audio> --}}

</div>

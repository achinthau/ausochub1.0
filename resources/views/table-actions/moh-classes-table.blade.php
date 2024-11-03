<div class="flex space-x-1 justify-center">

    @foreach (\Illuminate\Support\Facades\Storage::disk('moh-file-server')->files("SoundMOH/moh_$moh_class") as $filename)

        @php
             $url = Storage::disk('moh-file-server')->url("$filename");
        @endphp

        <a href="#" wire:click.prevent="download('{{$filename}}')">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M14,2H6C4.9,2,4,2.9,4,4v16c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2V8L14,2z M6,20V4h7v5h5v11H6z M16,11h-4v3.88 c-0.36-0.24-0.79-0.38-1.25-0.38c-1.24,0-2.25,1.01-2.25,2.25c0,1.24,1.01,2.25,2.25,2.25S13,17.99,13,16.75V13h3V11z">
                </path>
            </svg>
        </a>

        {{-- <audio controls>
            <source src="{{ $url }}" type="audio/wav">
          Your browser does not support the audio element.
          </audio> --}}
    @endforeach
    <a href="#"
        wire:click.prevent="$emitTo('settings.moh.partials.upload-file', 'showUploadFileModal',{{ $id }})"
        class="p-1 text-teal-600 hover:bg-teal-600 hover:text-white rounded">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <g>
                <path fill="none" d="M0 0h24v24H0z"></path>
                <path
                    d="M12 12.586l4.243 4.242-1.415 1.415L13 16.415V22h-2v-5.587l-1.828 1.83-1.415-1.415L12 12.586zM12 2a7.001 7.001 0 0 1 6.954 6.194 5.5 5.5 0 0 1-.953 10.784v-2.014a3.5 3.5 0 1 0-1.112-6.91 5 5 0 1 0-9.777 0 3.5 3.5 0 0 0-1.292 6.88l.18.03v2.014a5.5 5.5 0 0 1-.954-10.784A7 7 0 0 1 12 2z">
                </path>
            </g>
        </svg>
    </a>

</div>

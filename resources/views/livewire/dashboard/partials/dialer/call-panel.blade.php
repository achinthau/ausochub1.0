<div class="bg-white p-4 space-y-2">
    <h1 class="text-xs text-gray-400 font-semibold">Calling Panel</h1>
    <hr>

    {{-- @foreach ($queueWiseData as $data) --}}
    <div class="flex ">
        <div class="flex-1  text-sm font-medium text-secondary-700 dark:text-gray-400 mr-2">
            {{ $phone }}
        </div>
        <div>
            {{-- <svg wire:click="openProfile('{{ $phone }}')" class="w-6 h-6 cursor-pointer text-green-400"
                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"
                data-slot="icon">
                <path fill-rule="evenodd"
                    d="M15 3.75a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0V5.56l-4.72 4.72a.75.75 0 1 1-1.06-1.06l4.72-4.72h-2.69a.75.75 0 0 1-.75-.75Z"
                    clip-rule="evenodd"></path>
                <path fill-rule="evenodd"
                    d="M1.5 4.5a3 3 0 0 1 3-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 0 1-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 0 0 6.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 0 1 1.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 0 1-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5Z"
                    clip-rule="evenodd"></path>
            </svg> --}}
            <div class="relative group">
        <!-- The user's provided SVG with its original attributes -->
        <svg wire:click="openProfile('{{ $phone }}')" class="w-8 h-8 cursor-pointer text-green-400"
            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 17v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2"></path>
            <path d="M21 7V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2"></path>
            <circle cx="12" cy="12" r="1"></circle>
            <path d="M18.944 12.33a1 1 0 0 0 0-.66 7.5 7.5 0 0 0-13.888 0 1 1 0 0 0 0 .66 7.5 7.5 0 0 0 13.888 0">
            </path>
        </svg>

        <!-- The text that appears on hover (the tooltip) -->
        <!-- This element is initially hidden and becomes visible on hover of the parent "group" -->
        <span class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2
                     bg-gray-800 text-white text-xs rounded-lg px-2 py-1
                     opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">
            Open
        </span>
    </div>
        </div>
    </div>
    {{-- @endforeach --}}
</div>

<script>
    window.addEventListener('open-lead-window', event => {
        const leadUrl = event.detail.url;

        // If a window is already open for this lead, close it
        if (window.leadWindow && !window.leadWindow.closed) {
            window.leadWindow.close();
        }

        // Open the new lead window
        window.leadWindow = window.open(leadUrl, '_blank');
        window.leadWindow.focus();
    });
</script>
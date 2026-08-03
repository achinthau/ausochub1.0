<a href="#"
    wire:click.prevent="$emitTo('dialer.settings.campaign.max-dial-count', 'showMaxDialCountModal', {{ $campaign->id }})"
    class="inline-flex items-center justify-center p-1 rounded text-indigo-600 hover:bg-indigo-600 hover:text-white"
    title="Set Max Dial Counts">
    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 5V3h-6M21 9V7.97M3 19v2h6M3 15v1.03M21 3l-7.5 7.5M10.5 13.5L3 21"></path>
</svg>
</a>

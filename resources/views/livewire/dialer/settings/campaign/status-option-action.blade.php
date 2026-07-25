<a href="#"
    wire:click.prevent="$emitTo('dialer.settings.campaign.status-option', 'showStatusOptionModal', {{ $campaign->id }})"
    class="inline-flex items-center justify-center p-1 rounded text-purple-600 hover:bg-purple-600 hover:text-white"
    title="Manage Status Options">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
    </svg>
</a>
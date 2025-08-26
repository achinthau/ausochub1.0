<div class="px-6 py-5 bg-white border-b border-gray-200 flex items-center justify-between">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight pl-32">
        {{ __('Campaign Management') }}
    </h2>

    <div class="flex space-x-2  pr-32">
        {{-- Add Campaign Button --}}
        <button type="button" wire:click="$emitTo('dialer.settings.campaign.create', 'showCreateCampaignModal')"
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300  rounded-md font-semibold text-xs text-gray-400 uppercase tracking-widest hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Campaign
        </button>
    </div>
</div>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @livewire('campaign-table')
    </div>
</div>

@push('modals')
    @livewire('dialer.settings.campaign.create')
@endpush
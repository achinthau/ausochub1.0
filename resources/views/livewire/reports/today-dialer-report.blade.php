<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daily Dialer Report') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-4 mb-4">
                <label for="selectedCampaign" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('Assigned Campaign') }}
                </label>
                <select id="selectedCampaign"
                        wire:model="selectedCampaign"
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full sm:w-96">
                    <option value="">All Campaigns</option>
                    @foreach ($campaigns as $campaign)
                        <option value="{{ $campaign->id }}">
                            {{ $campaign->name }}{{ $campaign->types?->name ? ' (' . $campaign->types->name . ')' : '' }}
                        </option>
                    @endforeach
                </select>
                @unless(auth()->user()->user_type_id === 1)
                    <p class="text-xs text-gray-400 mt-1">{{ __('Only today\'s dialer data is shown.') }}</p>
                @endunless
            </div>

            @livewire('reports.today-dialer-report-table')
        </div>
    </div>
</div>

<x-modal.card :title="$campaignId ? 'Update Campaign' : 'Create Campaign'" blur align="center" wire:model="createCampaignModal" x-on:refresh-modal.window="$refresh">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Campaign Name -->
        <x-input label="Campaign Name" placeholder="Enter campaign name" wire:model.defer="name" />

        <!-- Campaign Selection -->
        <x-select label="Select Campaign Type" placeholder="Choose a Campaign Type" wire:model="campaign_type_id">
            @foreach($campaignTypes as $type)
                <x-select.option :label="$type->name" :value="$type->id" />
            @endforeach
        </x-select>

        <!-- Service Selection -->
        <x-select label="Select Service Type" placeholder="Choose a Service Type" wire:model="service_type">
            {{-- @foreach($campaignTypes as $type) --}}
                <x-select.option label="Satisfaction" value="satisfaction" />
                <x-select.option label="Follow-up" value="follow-up" />
                <x-select.option label="Confirmation" value="confirmation" />
            {{-- @endforeach --}}
        </x-select>

        <x-input label="Hotline" placeholder="Enter the Hotline" wire:model.defer="hotline" />

        <!-- Company Selection -->
        <x-select label="Select Company" placeholder="Choose a company" wire:model="company_id">
            @foreach($companies as $company)
                <x-select.option :label="$company->name" :value="$company->id" />
            @endforeach
        </x-select>

        

        <!-- Assign Users -->
        <x-select
            label="Assign Users"
            placeholder="Choose users"
            wire:model="user_ids"
            :options="$users->map(fn($user) => ['id' => (int) $user->id, 'name' => $user->name])->toArray()"
            option-label="name"
            option-value="id"
            multiselect
            x-init="() => {
                if (window.TomSelect) {
                    new TomSelect($el, { plugins: ['remove_button'], maxItems: null });
                }
            }"
            x-on:livewire:refresh="$el.TomSelect && $el.TomSelect.destroy(); new TomSelect($el, { plugins: ['remove_button'], maxItems: null });"
        />
        

        <!-- Select Feeds -->
        <x-select
            label="Select Feeds"
            placeholder="Choose Feeds"
            wire:model="feed_ids"
            :options="$feeds->map(fn($feed) => ['id' => (int) $feed->id, 'name' => $feed->name])->toArray()"
            option-label="name"
            option-value="id"
            multiselect
            x-init="() => {
                if (window.TomSelect) {
                    new TomSelect($el, { plugins: ['remove_button'], maxItems: null });
                }
            }"
            x-on:livewire:refresh="$el.TomSelect && $el.TomSelect.destroy(); new TomSelect($el, { plugins: ['remove_button'], maxItems: null });"
        />

        <!-- Status Selection -->
        @if($campaignId)
        <x-select label="Select Status" placeholder="Choose a Status" wire:model="status">
            
                <x-select.option label="active" value="1" />
                <x-select.option label="inactive" value="0" />
                <x-select.option label="hold" value="2" />
                {{-- <x-select.option label="running" value="running" /> --}}
                <x-select.option label="completed" value="3" />
                <x-select.option label="canceled" value="4" />
            
        </x-select>
        @endif
        </div>




        <div>
    <div class="max-w-4xl mx-auto px-4 pt-2">
        <h2 class="text-  font-semibold mb-4">Weekly Schedule</h2>

        <!-- Make it 2 columns -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center  px-3 rounded-lg">
                    <label class="capitalize font-medium">{{ $day }}</label>
                    <div class="pr-2">
                        <x-input
                            type="text"
                            wire:model.defer="schedule.{{ $day }}.start"
                            placeholder="Start "
                            class="w-20"
                            x-data
                            x-init="flatpickr($el, {
                                enableTime: true,
                                noCalendar: true,
                                dateFormat: 'h:i K',
                                time_24hr: false
                            })"
                        />
                        @error('schedule.{{ $day }}.start') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>
                    <div>
                        <x-input
                            type="text"
                            wire:model.defer="schedule.{{ $day }}.end"
                            placeholder="End "
                            class="w-20"
                            x-data
                            x-init="flatpickr($el, {
                                enableTime: true,
                                noCalendar: true,
                                dateFormat: 'h:i K',
                                time_24hr: false
                            })"
                        />
                        @error('schedule.{{ $day }}.end') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>
                </div>
            @endforeach
        </div>

        {{-- <div class="mt-6">
            <x-button primary label="Save Schedule" wire:click="saveSchedule" />
        </div> --}}

        {{-- <div class="mt-4">
            <h3 class="text-lg font-medium">Saved Schedule (JSON):</h3>
            <pre class="bg-gray-100 p-4 rounded">{{ $savedSchedule }}</pre>
        </div> --}}
    </div>
</div>







        
    

    <x-slot name="footer">
        <div class="flex justify-between gap-x-4">
            <div></div>
            <div class="flex">
                <x-button flat label="Cancel" x-on:click="close" />
                <x-button primary :label="$campaignId ? 'Update' : 'Save'" wire:click="save" />
            </div>
        </div>
    </x-slot>
</x-modal.card>
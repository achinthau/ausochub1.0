<x-modal.card :title="$campaignId ? 'Update Campaign' : 'Create Campaign'" blur align="center" wire:model="createCampaignModal" x-on:refresh-modal.window="$refresh">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Campaign Name -->
        <x-input label="Campaign Name" placeholder="Enter campaign name" wire:model.defer="name" />

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
        </div>




        <div>
    <div class="max-w-4xl mx-auto p-4">
        <h2 class="text-  font-semibold mb-4">Weekly Schedule</h2>

        <!-- Make it 2 columns -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center border p-3 rounded-lg">
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
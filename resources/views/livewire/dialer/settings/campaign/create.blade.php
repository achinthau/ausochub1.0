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
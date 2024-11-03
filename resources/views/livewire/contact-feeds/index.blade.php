<div>
    <x-slot name="header">
        <div class="flex ">
            <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight ">
                {{ __('Contact Feed ') }}
            </h2>
            <div>
                <x-button icon="upload"  label="Upload Feed"
                    onclick="$openModal('createContactFeedModal') " />
            </div>

        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('tables.contact-feeds-table')
        </div>
    </div>
</div>
@push('modals')
    @livewire('contact-feeds.create')
@endpush
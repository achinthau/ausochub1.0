<div>
    <x-slot name="header">
        <div class="flex">
            <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight ">
                {{ __('Dialer Call Status Report')  }}
            </h2>

        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end">
                @livewire('reports.partials.dialer-attempt-campaigns')
            </div>
            @livewire('reports.dialer-contact-attempt-table')
        </div>
    </div>
</div>
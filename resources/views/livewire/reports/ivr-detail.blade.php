<div>
    <x-slot name="header">
        <div class="flex">
            <h2 class="flex-1 font-semibold text-xl text-gray-800 leading-tight ">
                {{ __('IVR Detail Report')  }}
            </h2>

            
<x-button
    href="{{ route('filament.pages.ivr-performance-dashboard') }}"
    class="relative group flex items-center"
>

    <svg
                    class="w-6 h-6 text-blue-500"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                </svg>

    <span
        class="absolute bottom-full mb-2 hidden group-hover:block
               bg-gray-900 text-white text-xs px-2 py-1 rounded whitespace-nowrap"
    >
        Analytics
    </span>
</x-button>

        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('reports.ivr-detail-table')
        </div>
    </div>
</div>

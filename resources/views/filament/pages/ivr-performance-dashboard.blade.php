<x-filament::page>
    <x-slot name="header"></x-slot>
    <style>
        /* Hide the sidebar */
        .filament-sidebar, 
        aside.filament-sidebar {
            display: none !important;
        }

        /* Remove the left padding/margin usually reserved for the sidebar on the main layout */
        .filament-main,
        main, 
        .filament-app-layout {
            margin-left: 0 !important;
            padding-left: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        /* Center the actual content container */
        .filament-main-content, 
        .filament-page {
            width: 100%;
            max-width: 1400px; /* Or 7xl equivalent */
            margin: 0 auto !important;
        }

        /* Hide the header/title sector */
        .filament-header {
            display: none !important;
        }
    </style>
    
    <div class="grid grid-cols-1 gap-4">
        

        <div class="col-span-full">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">IVR Performance</h2>
        </div>

        {{-- Comparison Chart --}}
        <div class="col-span-full flex justify-center">
            <div class="w-full max-w-4xl">
                @livewire(\App\Filament\Widgets\IvrComparisonChart::class, ['startDate' => $startDate, 'endDate' => $endDate])
            </div>
        </div>

        <div class="col-span-full mb-6">
            <form method="GET" class="flex flex-wrap gap-4 items-center bg-white dark:bg-gray-900 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
                <div class="flex flex-col">
                    <label for="startDate" class="font-semibold text-gray-600 dark:text-gray-400 text-xs uppercase tracking-wider mb-1">Start Date</label>
                    <input type="date" name="startDate" id="startDate" 
                        value="{{ $startDate }}" 
                        min="{{ $minDate }}" 
                        max="{{ $maxDate }}"
                        class="border-gray-200 dark:border-gray-700 bg-transparent rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 dark:text-gray-100 text-sm py-2 px-3" 
                        onchange="this.form.submit()">
                </div>

                <div class="flex flex-col">
                    <label for="endDate" class="font-semibold text-gray-600 dark:text-gray-400 text-xs uppercase tracking-wider mb-1">End Date</label>
                    <input type="date" name="endDate" id="endDate" 
                        value="{{ $endDate }}" 
                        min="{{ $minDate }}" 
                        max="{{ $maxDate }}"
                        class="border-gray-200 dark:border-gray-700 bg-transparent rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 dark:text-gray-100 text-sm py-2 px-3" 
                        onchange="this.form.submit()">
                </div>

                <div class="flex items-center pt-1 mt-4">
                    <span class="text-[10px] text-gray-400 font-medium bg-gray-50 dark:bg-gray-800 px-2 py-1 rounded-full border border-gray-100 dark:border-gray-700 whitespace-nowrap">
                        Restricted to last 30 days
                    </span>
                </div>
                
                <div class="flex items-center pt-6 ml-auto">
                    <a href="{{ route('filament.pages.ivr-performance-dashboard') }}" 
                        class="filament-button filament-button-size-sm inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2rem] px-3 text-xs text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600 dark:bg-gray-800 dark:hover:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:focus:text-primary-400 dark:focus:border-primary-400 whitespace-nowrap">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>

        {{-- Table Widget --}}
        <div class="col-span-full">
            @livewire(\App\Filament\Widgets\IvrComparisonTable::class, ['startDate' => $startDate, 'endDate' => $endDate])
        </div>
    </div>
</x-filament::page>

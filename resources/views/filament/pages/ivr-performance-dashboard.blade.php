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
    
    <div class="grid grid-cols-1 gap-8">
        
        <div class="col-span-full">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">IVR Performance Breakdown</h2>
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
                        onchange="adjustDates('startDate')">
                </div>

                <div class="flex flex-col">
                    <label for="endDate" class="font-semibold text-gray-600 dark:text-gray-400 text-xs uppercase tracking-wider mb-1">End Date</label>
                    <input type="date" name="endDate" id="endDate" 
                        value="{{ $endDate }}" 
                        min="{{ $minDate }}" 
                        max="{{ $maxDate }}"
                        class="border-gray-200 dark:border-gray-700 bg-transparent rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 dark:text-gray-100 text-sm py-2 px-3" 
                        onchange="adjustDates('endDate')">
                </div>

                <div class="flex items-center pt-1 mt-4">
                    <span class="text-[10px] text-gray-400 font-medium bg-gray-50 dark:bg-gray-800 px-2 py-1 rounded-full border border-gray-100 dark:border-gray-700 whitespace-nowrap">
                        Fixed 30-day window
                    </span>
                </div>

                <script>
                    function adjustDates(changedField) {
                        const startInput = document.getElementById('startDate');
                        const endInput = document.getElementById('endDate');
                        const gap = 30;

                        if (changedField === 'startDate') {
                            const date = new Date(startInput.value);
                            date.setDate(date.getDate() + gap);
                            endInput.value = date.toISOString().split('T')[0];
                        } else {
                            const date = new Date(endInput.value);
                            date.setDate(date.getDate() - gap);
                            startInput.value = date.toISOString().split('T')[0];
                        }
                        startInput.form.submit();
                    }
                </script>
                
                <div class="flex items-center pt-6 ml-auto">
                    <a href="{{ route('filament.pages.ivr-performance-dashboard') }}" 
                        class="filament-button filament-button-size-sm inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2rem] px-3 text-xs text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600 dark:bg-gray-800 dark:hover:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:focus:text-primary-400 dark:focus:border-primary-400 whitespace-nowrap">
                        Reset Dashboard
                    </a>
                </div>
            </form>
        </div>

        {{-- Sections per DNIS --}}
        <div class="col-span-full grid grid-cols-1 md:grid-cols-2 gap-6 pb-12">
            @foreach($dnisList as $dnis)
                <div class="flex flex-col bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden transition-all hover:shadow-md">
                    <div class="bg-gray-50/50 dark:bg-gray-800/50 px-4 py-3 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-6 bg-primary-500 rounded-full"></div>
                            <h3 class="font-bold text-gray-800 dark:text-gray-100">{{ $dnis }}</h3>
                        </div>
                        <!-- <span class="text-[10px] font-bold text-primary-600 bg-primary-50 dark:bg-primary-900/30 px-2 py-0.5 rounded-full uppercase tracking-tight">Active</span> -->
                    </div>

                    <div class="p-4 flex-1">
                        @livewire(\App\Filament\Widgets\IvrComparisonChart::class, [
                            'startDate' => $startDate, 
                            'endDate' => $endDate,
                            'dnis' => $dnis
                        ], key('chart-'.$dnis))
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament::page>

<x-filament::page>
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
    </style>
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        @if($technicianName)
            <div class="col-span-full mb-4">
                <h2 class="text-xl font-bold">Performance Metrics for: {{ $technicianName }}</h2>
            </div>
        @else
            <div class="mb-4 text-gray-700">
                <h2 class="text-xl font-bold">All Technicians Performance</h2>
            </div>
        @endif

        <div class="col-span-full mb-6">
            <form method="GET" class="flex flex-wrap gap-4 items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                @if(request()->has('technician'))
                    <input type="hidden" name="technician" value="{{ request('technician') }}">
                @endif
                @if(request()->has('technician_id'))
                    <input type="hidden" name="technician_id" value="{{ request('technician_id') }}">
                @endif

                <div class="flex flex-col">
                    <label for="startDate" class="font-semibold text-gray-600 text-xs uppercase tracking-wider mb-1">Start Date</label>
                    <input type="date" name="startDate" id="startDate" 
                        value="{{ $startDate }}" 
                        min="{{ $minDate }}" 
                        max="{{ $maxDate }}"
                        class="border-gray-200 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 text-sm py-2 px-3" 
                        onchange="this.form.submit()">
                </div>

                <div class="flex flex-col">
                    <label for="endDate" class="font-semibold text-gray-600 text-xs uppercase tracking-wider mb-1">End Date</label>
                    <input type="date" name="endDate" id="endDate" 
                        value="{{ $endDate }}" 
                        min="{{ $minDate }}" 
                        max="{{ $maxDate }}"
                        class="border-gray-200 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-900 text-sm py-2 px-3" 
                        onchange="this.form.submit()">
                </div>

                <div class="flex items-center pt-1 mt-4">
                    <span class="text-[10px] text-gray-400 font-medium bg-gray-50 px-2 py-1 rounded-full border border-gray-100 whitespace-nowrap">
                        Restricted to last 30 days
                    </span>
                </div>
                
                <div class="flex items-center pt-6 ml-auto">
                    <a href="{{ route('filament.pages.technician-performance-dashboard') }}" text-lg
                        class="filament-button filament-button-size-sm inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2rem] px-3 text-xs text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600 dark:bg-gray-800 dark:hover:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:focus:text-primary-400 dark:focus:border-primary-400 whitespace-nowrap">
                        <svg class="w-5 h-5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        All Users
                    </a>
                </div>
            </form>
        </div>
        @livewire(\App\Filament\Widgets\TechnicianComparisonTable::class)
        
        {{-- Widgets will be rendered here automatically by getHeaderWidgets --}}
    </div>
</x-filament::page>

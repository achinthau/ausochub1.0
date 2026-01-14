<x-filament::page>
    <div class="mb-4">
        <form method="GET" class="flex flex-wrap gap-4 items-center bg-white p-4 rounded shadow">
            <h2 class="font-bold text-lg text-gray-700 mr-4">Filters</h2>
            
            <div class="flex flex-col">
                <label for="startDate" class="font-medium text-gray-700 text-sm mb-1">Start Date:</label>
                <input type="date" name="startDate" id="startDate" value="{{ $startDate }}" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-black text-sm" onchange="this.form.submit()">
            </div>

            <div class="flex flex-col">
                <label for="endDate" class="font-medium text-gray-700 text-sm mb-1">End Date:</label>
                <input type="date" name="endDate" id="endDate" value="{{ $endDate }}" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-black text-sm" onchange="this.form.submit()">
            </div>
        </form>
    </div>
    
    <div class="mt-4 space-y-4">
        @livewire(\App\Filament\Widgets\PerformanceStatsOverview::class, ['startDate' => $startDate, 'endDate' => $endDate])
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @livewire(\App\Filament\Widgets\InboundCallsChart::class, ['startDate' => $startDate, 'endDate' => $endDate])
            @livewire(\App\Filament\Widgets\GlobalDispositionPieChart::class, ['startDate' => $startDate, 'endDate' => $endDate])
        </div>
    </div>
</x-filament::page>

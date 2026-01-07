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
            max-width: 1400px;
            margin: 0 auto !important;
        }

        /* Hide the header/title sector */
        .filament-header {
            display: none !important;
        }
    </style>

    <div class="grid grid-cols-1 gap-8">
        <!-- <div class="col-span-full">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Daily Queue Summary Performance</h2>
        </div> -->

        <div class="col-span-full mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-col md:flex-row items-end gap-6">
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                        <input type="date" 
                               wire:model.lazy="startDate" 
                               id="startDate"
                               min="{{ $minDate }}"
                               max="{{ $maxDate }}"
                               class="w-full px-4 py-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary-500 focus:border-primary-500 shadow-sm transition duration-150">
                    </div>
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                        <input type="date" 
                               wire:model.lazy="endDate" 
                               id="endDate"
                               min="{{ $minDate }}"
                               max="{{ $maxDate }}"
                               class="w-full px-4 py-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary-500 focus:border-primary-500 shadow-sm transition duration-150">
                    </div>
                    <div class="flex items-center pb-1">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400 italic">
                            Fixed 30-day window
                        </span>
                    </div>
                    <div class="flex-grow"></div>
                    <div class="pb-1">
                        <button wire:click="$set('startDate', '{{ Carbon\Carbon::now()->subDays(30)->format('Y-m-d') }}'); $set('endDate', '{{ Carbon\Carbon::now()->format('Y-m-d') }}')" 
                                class="px-6 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-bold rounded-lg transition duration-200 shadow-sm border border-gray-200 dark:border-gray-600">
                            Reset Dashboard
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($queueList as $queue)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 flex flex-col h-full overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                            <span class="w-2 h-6 bg-primary-600 rounded-full"></span>
                            Queue: {{ $queue }}
                        </h3>
                    </div>
                    <div class="p-5 flex-grow">
                        @livewire(\App\Filament\Widgets\DailyQueueSummaryChart::class, [
                            'startDate' => $startDate,
                            'endDate' => $endDate,
                            'queue' => $queue,
                        ], key('chart-'.$queue.'-'.$startDate.'-'.$endDate))
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        document.addEventListener('livewire:load', function () {
            const startDateInput = document.getElementById('startDate');
            const endDateInput = document.getElementById('endDate');

            function adjustDates(changed) {
                if (changed === 'start') {
                    let start = new Date(startDateInput.value);
                    if (isNaN(start)) return;
                    let end = new Date(start);
                    end.setDate(start.getDate() + 30);
                    
                    let max = new Date('{{ $maxDate }}');
                    if (end > max) {
                        end = max;
                        start = new Date(end);
                        start.setDate(end.getDate() - 30);
                    }
                    @this.set('startDate', start.toISOString().split('T')[0]);
                    @this.set('endDate', end.toISOString().split('T')[0]);
                } else {
                    let end = new Date(endDateInput.value);
                    if (isNaN(end)) return;
                    let start = new Date(end);
                    start.setDate(end.getDate() - 30);

                    let min = new Date('{{ $minDate }}');
                    if (start < min) {
                        start = min;
                        end = new Date(start);
                        end.setDate(start.getDate() + 30);
                    }
                    @this.set('startDate', start.toISOString().split('T')[0]);
                    @this.set('endDate', end.toISOString().split('T')[0]);
                }
            }

            startDateInput.addEventListener('change', () => adjustDates('start'));
            endDateInput.addEventListener('change', () => adjustDates('end'));
        });
    </script>
</x-filament::page>

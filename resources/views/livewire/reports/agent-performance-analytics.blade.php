<div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('reports.agent-performance-report') }}" class="text-gray-500 hover:text-gray-700 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Agent Performance Analytics') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{
        charts: {},
        donutData: @js($this->donutData),
        init() {
            this.initCharts();
            Livewire.on('refreshCharts', (data) => {
                this.donutData = data;
                this.initCharts();
            });
        },
        initCharts() {
            const donutData = this.donutData;
            
            this.renderChart('talkTimeChart', 'Talk Time (Secs)', ['0-2', '2-5', '5+'], donutData.talkTime, ['#3b82f6', '#10b981', '#f59e0b']);
            this.renderChart('ringTimeChart', 'Ring Time (Secs)', ['0-5', '5-10', '10-15', '15+'], donutData.ringTime, ['#6366f1', '#8b5cf6', '#ec4899', '#ef4444']);
            this.renderChart('holdTimeChart', 'Hold Time (Secs)', ['0-30', '30-120', '120+'], donutData.holdTime, ['#f97316', '#eab308', '#dc2626']);
            this.renderChart('discByChart', 'Disconnected By', ['Agent', 'Caller'], donutData.disconnectedBy, ['#06b6d4', '#14b8a6']);
        },
        renderChart(id, label, labels, data, colors) {
            const ctx = document.getElementById(id).getContext('2d');
            if (this.charts[id]) {
                this.charts[id].destroy();
            }
            this.charts[id] = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        backgroundColor: colors,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        title: {
                            display: true,
                            text: label
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((acc, curr) => acc + curr, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 italic text-sm">
                    <div>
                        <label class="block text-gray-700 mb-1">Start Date</label>
                        <input type="date" wire:model.lazy="startDate" min="{{ \Carbon\Carbon::now()->subDays(60)->format('Y-m-d') }}" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">End Date</label>
                        <input type="date" wire:model.lazy="endDate" min="{{ \Carbon\Carbon::now()->subDays(60)->format('Y-m-d') }}" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Extension</label>
                        <div class="relative">
                            <select wire:model="selectedExtension" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 max-h-40 overflow-y-auto">
                                <option value="">All Extensions</option>
                                @foreach($this->extensions as $ext)
                                    <option value="{{ $ext }}">{{ $ext }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6" style="height: 400px;">
                    <canvas id="talkTimeChart"></canvas>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6" style="height: 400px;">
                    <canvas id="ringTimeChart"></canvas>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6" style="height: 400px;">
                    <canvas id="holdTimeChart"></canvas>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6" style="height: 400px;">
                    <canvas id="discByChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

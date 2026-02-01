<div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('reports.daily-queue-summary-hourly', ['date' => $date, 'queue' => $queue]) }}" class="text-gray-500 hover:text-gray-700 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Hourly Queue Analytics - ') . $queue . ' (' . $date . ')' }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
        chart: null,
        init() {
            this.initChart();
        },
        initChart() {
            this.$nextTick(() => {
                const canvas = this.$refs.canvas;
                if (!canvas) {
                    setTimeout(() => this.initChart(), 200);
                    return;
                }

                if (typeof Chart === 'undefined') {
                    setTimeout(() => this.initChart(), 200);
                    return;
                }

                const ctx = canvas.getContext('2d');
                if (this.chart) {
                    this.chart.destroy();
                }

                this.chart = new Chart(ctx, {
                    type: 'line',
                    data: @js($this->chartData),
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        }
                    }
                });
            });
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900 leading-6">Hourly Queue Trends</h3>
                </div>
                
                <div style="height: 400px;">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

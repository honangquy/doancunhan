<!-- Tab Timeline -->
<div x-show="activeTab === 'timeline'" x-cloak>
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Timeline Submissions & Reviews</h3>
            <div class="flex items-center space-x-2 text-xs">
                <div class="flex items-center">
                    <span class="w-3 h-3 bg-blue-500 rounded mr-1"></span>
                    <span class="text-gray-600">Submissions</span>
                </div>
                <div class="flex items-center">
                    <span class="w-3 h-3 bg-green-500 rounded mr-1"></span>
                    <span class="text-gray-600">Reviews Completed</span>
                </div>
            </div>
        </div>

        <div class="relative" style="height: 400px;">
            <canvas id="timelineChart"></canvas>
        </div>

        @if(empty($timeline) || count($timeline) === 0)
            <div class="text-center text-gray-500 py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <p>Chưa có dữ liệu timeline</p>
            </div>
        @endif
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
        <div class="bg-blue-50 p-4 rounded-lg">
            <div class="flex items-center">
                <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <div>
                    <p class="text-xs text-blue-600 font-semibold">Tổng Submissions</p>
                    <p class="text-2xl font-bold text-blue-700">{{ array_sum(array_column($timeline ?? [], 'submissions')) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 p-4 rounded-lg">
            <div class="flex items-center">
                <svg class="w-8 h-8 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-xs text-green-600 font-semibold">Reviews Completed</p>
                    <p class="text-2xl font-bold text-green-700">{{ array_sum(array_column($timeline ?? [], 'completions')) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 p-4 rounded-lg">
            <div class="flex items-center">
                <svg class="w-8 h-8 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <div>
                    <p class="text-xs text-purple-600 font-semibold">Avg Submissions/Day</p>
                    <p class="text-2xl font-bold text-purple-700">
                        {{ count($timeline ?? []) > 0 ? number_format(array_sum(array_column($timeline, 'submissions')) / count($timeline), 1) : 0 }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-orange-50 p-4 rounded-lg">
            <div class="flex items-center">
                <svg class="w-8 h-8 text-orange-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <div>
                    <p class="text-xs text-orange-600 font-semibold">Peak Submission Day</p>
                    <p class="text-lg font-bold text-orange-700">
                        @php
                            $maxSubmissions = 0;
                            $peakDate = 'N/A';
                            foreach ($timeline ?? [] as $data) {
                                if ($data['submissions'] > $maxSubmissions) {
                                    $maxSubmissions = $data['submissions'];
                                    $peakDate = \Carbon\Carbon::parse($data['date'])->format('d/m');
                                }
                            }
                        @endphp
                        {{ $peakDate }} ({{ $maxSubmissions }})
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(!empty($timeline) && count($timeline) > 0)
        const ctx = document.getElementById('timelineChart');

        if (ctx) {
            const timelineData = @json($timeline);

            const labels = timelineData.map(item => {
                const date = new Date(item.date);
                return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
            });

            const submissionsData = timelineData.map(item => item.submissions);
            const completionsData = timelineData.map(item => item.completions);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Submissions',
                            data: submissionsData,
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Reviews Completed',
                            data: completionsData,
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                title: function(context) {
                                    const index = context[0].dataIndex;
                                    const date = new Date(timelineData[index].date);
                                    return date.toLocaleDateString('vi-VN', {
                                        weekday: 'long',
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric'
                                    });
                                },
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                stepSize: 1,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        }
    @endif
});
</script>

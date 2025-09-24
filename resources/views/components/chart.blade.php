@props([
    'id' => 'chart-' . uniqid(),
    'type' => 'line',
    'data' => [],
    'options' => [],
    'height' => '400px',
    'width' => '100%',
    'key' => null
])

@php
    // Handle both array and JSON string data
    $chartData = is_string($data) ? json_decode($data, true) : $data;
    $chartOptions = is_string($options) ? json_decode($options, true) : $options;
    
    // If data is empty, try to get it from attributes (for Alpine.js syntax)
    if (empty($chartData) && $attributes->has('data')) {
        $chartData = $attributes->get('data');
    }
    if (empty($chartOptions) && $attributes->has('options')) {
        $chartOptions = $attributes->get('options');
    }
@endphp

<div class="chart-container" style="width: {{ $width }}; height: {{ $height }};">
    <canvas id="{{ $id }}" {{ $attributes->merge(['class' => 'w-full h-full']) }}></canvas>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartId = '{{ $id }}';
    const chartType = '{{ $type }}';
    const chartData = @json($chartData);
    const chartOptions = @json($chartOptions);
    
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded');
        return;
    }
    
    const ctx = document.getElementById(chartId);
    if (ctx) {
        try {
            new Chart(ctx, {
                type: chartType,
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: chartOptions.xAxisLabel || ''
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: chartOptions.yAxisLabel || ''
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
            console.log('Chart ' + chartId + ' created successfully');
        } catch (error) {
            console.error('Error creating chart ' + chartId + ':', error);
        }
    } else {
        console.error('Canvas element ' + chartId + ' not found');
    }
});
</script>

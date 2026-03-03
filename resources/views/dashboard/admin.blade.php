@extends('app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="px-4 pt-6 pb-8">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Overview</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Registration statistics at a glance</p>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4 mb-8">

        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 p-6 shadow-lg">
            <div class="relative z-10">
                <p class="text-sm font-medium text-indigo-100">Today</p>
                <p class="mt-2 text-3xl font-bold text-white">{{ number_format($summary['today']) }}</p>
                <p class="mt-1 text-xs text-indigo-200">Registrations</p>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-10">
                <svg class="w-28 h-28" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-6 shadow-lg">
            <div class="relative z-10">
                <p class="text-sm font-medium text-emerald-100">Yesterday</p>
                <p class="mt-2 text-3xl font-bold text-white">{{ number_format($summary['yesterday']) }}</p>
                <p class="mt-1 text-xs text-emerald-200">Registrations</p>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-10">
                <svg class="w-28 h-28" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM9 10H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z"/>
                </svg>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 p-6 shadow-lg">
            <div class="relative z-10">
                <p class="text-sm font-medium text-amber-100">This Month</p>
                <p class="mt-2 text-3xl font-bold text-white">{{ number_format($summary['this_month']) }}</p>
                <p class="mt-1 text-xs text-amber-200">Registrations</p>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-10">
                <svg class="w-28 h-28" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                </svg>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-rose-500 to-rose-600 p-6 shadow-lg">
            <div class="relative z-10">
                <p class="text-sm font-medium text-rose-100">Last Month</p>
                <p class="mt-2 text-3xl font-bold text-white">{{ number_format($summary['last_month']) }}</p>
                <p class="mt-1 text-xs text-rose-200">Registrations</p>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-10">
                <svg class="w-28 h-28" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                </svg>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Daily Registration Trend</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Last 30 days</p>
                </div>
                <div class="flex items-center gap-2 px-3 py-1.5 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg">
                    <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                    <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400">Daily</span>
                </div>
            </div>
            <div class="relative" style="height: 320px;">
                <canvas id="dailyTrendChart"></canvas>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Monthly Registration Trend</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Last 12 months</p>
                </div>
                <div class="flex items-center gap-2 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                    <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">Monthly</span>
                </div>
            </div>
            <div class="relative" style="height: 320px;">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dailyCtx = document.getElementById('dailyTrendChart').getContext('2d');
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: @json($dailyTrend['labels']),
                datasets: [{
                    label: 'Registrations',
                    data: @json($dailyTrend['data']),
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.08)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#6366f1',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e1b4b',
                        titleColor: '#c7d2fe',
                        bodyColor: '#ffffff',
                        padding: 12,
                        cornerRadius: 10,
                        displayColors: false,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#9ca3af',
                            font: { size: 11 },
                            maxRotation: 45,
                            maxTicksLimit: 10,
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(156, 163, 175, 0.15)' },
                        ticks: {
                            color: '#9ca3af',
                            font: { size: 11 },
                            precision: 0,
                        }
                    }
                }
            }
        });

        const monthlyCtx = document.getElementById('monthlyTrendChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: @json($monthlyTrend['labels']),
                datasets: [{
                    label: 'Registrations',
                    data: @json($monthlyTrend['data']),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.08)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#10b981',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#064e3b',
                        titleColor: '#a7f3d0',
                        bodyColor: '#ffffff',
                        padding: 12,
                        cornerRadius: 10,
                        displayColors: false,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#9ca3af',
                            font: { size: 11 },
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(156, 163, 175, 0.15)' },
                        ticks: {
                            color: '#9ca3af',
                            font: { size: 11 },
                            precision: 0,
                        }
                    }
                }
            }
        });
    });
</script>
@endsection

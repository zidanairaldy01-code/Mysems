@extends('layouts.panitia')

@section('title', 'Dashboard Panitia - MySMES')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <h2 class="fw-bold">Dashboard Panitia</h2>
        <p class="text-muted">Selamat bekerja, {{ Auth::user()->nama }}! Pantau event yang sedang berjalan di sini.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 transition-hover">
            <div class="d-flex align-items-center gap-3">
                <div class="summary-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <p class="summary-label mb-1">Total Event Sukses</p>
                    <h4 class="summary-value text-success">{{ number_format($eventSuccess ?? 0) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 transition-hover">
            <div class="d-flex align-items-center gap-3">
                <div class="summary-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <div>
                    <p class="summary-label mb-1">Total Event Ditolak</p>
                    <h4 class="summary-value text-danger">{{ number_format($eventRejected ?? 0) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 transition-hover">
            <div class="d-flex align-items-center gap-3">
                <div class="summary-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <p class="summary-label mb-1">Total Peserta</p>
                    <h4 class="summary-value text-info">{{ number_format($totalPeserta ?? 0) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 transition-hover">
            <div class="d-flex align-items-center gap-3">
                <div class="summary-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <p class="summary-label mb-1">Total Tim yang Ikut</p>
                    <h4 class="summary-value text-warning">{{ number_format($totalTeams ?? 0) }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
                    <div>
                        <h5 class="fw-bold mb-1">Visualisasi Statistik Panitia</h5>
                        <p class="text-muted mb-0">Grafik data real-time untuk event sukses, event ditolak, peserta, dan tim.</p>
                    </div>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary">Interaktif & Responsif</span>
                </div>
                <div class="chart-container" style="min-height: 360px;">
                    <canvas id="panitiaStatsChart" width="800" height="360"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .transition-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .transition-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.08) !important;
    }
    .summary-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 3rem;
        height: 3rem;
        border-radius: 1rem;
        font-size: 1.35rem;
    }
    .summary-label {
        font-size: 0.78rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #6c757d;
        margin-bottom: 0.35rem;
    }
    .summary-value {
        font-size: 1.6rem;
        margin: 0;
    }
    .chart-container canvas {
        width: 100% !important;
        height: 100% !important;
        max-height: 420px;
    }
    @media (max-width: 768px) {
        .summary-icon {
            width: 2.75rem;
            height: 2.75rem;
        }
        .summary-value {
            font-size: 1.4rem;
        }
    }
</style>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('panitiaStatsChart');
            if (!ctx) {
                return;
            }

            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Event Sukses', 'Event Ditolak', 'Peserta', 'Tim'],
                    datasets: [{
                        label: 'Statistik Panitia',
                        data: [
                            {{ $eventSuccess ?? 0 }},
                            {{ $eventRejected ?? 0 }},
                            {{ $totalPeserta ?? 0 }},
                            {{ $totalTeams ?? 0 }}
                        ],
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.12)',
                        pointBackgroundColor: ['#198754', '#dc3545', '#0d6efd', '#fd7e14'],
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        tension: 0.38,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 900,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            padding: 12,
                            borderWidth: 1,
                            borderColor: 'rgba(108, 117, 125, 0.2)',
                            backgroundColor: '#fff',
                            titleColor: '#212529',
                            bodyColor: '#212529',
                            boxPadding: 10,
                            callbacks: {
                                label: function (context) {
                                    return context.label + ': ' + context.formattedValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6c757d',
                                font: {
                                    size: 13
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(108, 117, 125, 0.12)'
                            },
                            ticks: {
                                color: '#6c757d',
                                precision: 0,
                                callback: function(value) {
                                    return value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
@endsection

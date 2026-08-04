@extends('layouts.app')

@section('title', 'Barako Track | SAO Admin Control Panel')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 gap-sm-3">
        <div>
            <h4 class="fw-bold m-0 text-danger"><i class="bi bi-shield-lock-fill me-2"></i> Admin Dashboard</h4>
            <span class="text-muted fs-7">Overview of campus found inventory, pending verification claims, and student
                reports.</span>
        </div>
        <button class="btn btn-secondary-custom btn-sm px-3 py-2 fw-bold shadow-sm" data-bs-toggle="modal"
            data-bs-target="#reportFoundModal">
            <i class="bi bi-box-arrow-in-down me-1"></i> Add Found Item
        </button>
    </div>

    <!-- Admin Stat Cards -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card card-custom stat-card p-3" style="border-left-color: #1e1e2d;">
                <div class="text-muted fs-7 fw-semibold">Found Items in Storage</div>
                <h2 class="fw-bold my-1 text-dark">{{ $storageCount }}</h2>
                <small class="text-muted"><i class="bi bi-archive"></i> Ready for student claim</small>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card card-custom stat-card p-3" style="border-left-color: #fec452;">
                <div class="text-muted fs-7 fw-semibold">Pending Claim Verification</div>
                <h2 class="fw-bold my-1 text-warning">{{ $pendingClaimsCount }}</h2>
                <small class="text-warning fw-semibold"><i class="bi bi-exclamation-circle-fill"></i> Requires SAO
                    decision</small>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card card-custom stat-card p-3" style="border-left-color: #0dcaf0;">
                <div class="text-muted fs-7 fw-semibold">Ready for Pick-up</div>
                <h2 class="fw-bold my-1 text-info">{{ $readyForPickupCount }}</h2>
                <small class="text-info fw-semibold"><i class="bi bi-box-seam"></i> Staged at SAO Office</small>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card card-custom stat-card p-3" style="border-left-color: #198754;">
                <div class="text-muted fs-7 fw-semibold">Claimed Reports</div>
                <h2 class="fw-bold my-1 text-success">{{ $claimedReportsCount }}</h2>
                <small class="text-success fw-semibold"><i class="bi bi-check-all"></i> Successfully returned items</small>
            </div>
        </div>
    </div>

    <!-- Analytics Row 1: Yearly Incident Trend (col-lg-7) + Campus Hazard Radar (col-lg-5) -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-12 col-lg-7">
            <div class="card card-custom p-4 h-100 shadow-sm border-0" style="background: #ffffff; border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold m-0 text-dark">
                            <i class="bi bi-graph-up-arrow text-danger me-2"></i> Yearly Incident & Lost Breakdown (%)
                        </h5>
                        <small class="text-muted">Multi-year trend showing reported lost items & yearly loss share (%)</small>
                    </div>
                    <span class="badge bg-danger-subtle text-danger border border-danger p-2 fw-bold" style="font-size: 0.78rem;">
                        <i class="bi bi-fire me-1"></i> Peak Year: {{ $peakYearInfo }}
                    </span>
                </div>
                <div style="position: relative; height: 310px; width: 100%;">
                    <canvas id="yearlyTrendChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="card card-custom p-4 h-100 shadow-sm border-0" style="background: #ffffff; border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold m-0 text-dark">
                            <i class="bi bi-radar text-warning me-2"></i> Campus Hazard Radar
                        </h5>
                        <small class="text-muted">High-loss risk zone analysis across campus locations</small>
                    </div>
                    <span class="badge bg-warning-subtle text-dark border border-warning p-1 px-2 fw-bold" style="font-size: 0.75rem;">
                        <i class="bi bi-shield-exclamation me-1"></i> Location Radar
                    </span>
                </div>
                <div style="position: relative; height: 310px; width: 100%;" class="d-flex align-items-center justify-content-center">
                    <canvas id="hazardRadarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Row 2: Category Bar Chart (col-lg-7) + Inventory Status Doughnut (col-lg-5) -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-12 col-lg-7">
            <div class="card card-custom p-4 h-100 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold m-0 text-dark"><i class="bi bi-bar-chart-line-fill text-danger me-2"></i> Category Analytics</h5>
                        <small class="text-muted">Comparison of surrendered found items vs student lost reports</small>
                    </div>
                    <span class="badge bg-light text-dark border"><i class="bi bi-filter"></i> By Category</span>
                </div>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="card card-custom p-4 h-100 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold m-0 text-dark"><i class="bi bi-pie-chart-fill text-warning me-2"></i> Inventory Status</h5>
                        <small class="text-muted">Storage breakdown by item status</small>
                    </div>
                    <span class="badge bg-light text-dark border"><i class="bi bi-box-seam"></i> Real-time</span>
                </div>
                <div style="position: relative; height: 300px; width: 100%;" class="d-flex align-items-center justify-content-center">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Urgent Pending Verification Section Preview -->
    <div class="card card-custom p-4 mb-4 border-warning shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold m-0 text-dark"><i class="bi bi-shield-exclamation text-warning me-2"></i> Urgent Claims
                Requiring Review</h5>
            <a href="{{ route('admin.claims') }}" class="btn btn-sm btn-warning text-dark fw-bold">View All Pending Claims
                <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead class="table-light">
                    <tr>
                        <th>Claim ID</th>
                        <th>Student Claimant</th>
                        <th>Target Found Item</th>
                        <th>Proof Description</th>
                        <th>CNN AI Match Score</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPendingClaims as $pClaim)
                        @php
                            $score = $pClaim->match_score ?? 0;
                            $badgeColor =
                                $score >= 85 ? 'bg-success' : ($score >= 50 ? 'bg-warning text-dark' : 'bg-secondary');
                        @endphp
                        <tr>
                            <td><strong class="text-warning">#CLM-{{ $pClaim->id }}</strong></td>
                            <td>
                                <div class="fw-bold">{{ $pClaim->user->name ?? 'Student' }}</div>
                                <small class="text-muted">{{ $pClaim->user->student_id_number ?? 'UB Student' }}</small>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">{{ $pClaim->foundItem->title ?? 'Item' }}</div>
                                <small class="text-muted"><i class="bi bi-building"></i>
                                    {{ $pClaim->foundItem->storage_location ?? 'SAO' }}</small>
                            </td>
                            <td><small>{{ Str::limit($pClaim->proof_description, 45) }}</small></td>
                            <td>
                                @if ($score > 0)
                                    <span class="badge {{ $badgeColor }} p-2 fw-bold"><i class="bi bi-cpu-fill me-1"></i>
                                        {{ $score }}% Match</span>
                                @else
                                    <span class="badge bg-light text-dark border"><i class="bi bi-eye"></i> Manual
                                        Review</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.claims') }}"
                                    class="btn btn-sm btn-outline-warning text-dark fw-bold">Verify Claim</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3"><i
                                    class="bi bi-check-circle text-success me-1"></i> No urgent pending claims.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Chart.js 4 Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const categoryLabels = @json($categoryLabels);
            const foundCategoryCounts = @json($foundCategoryCounts);
            const lostCategoryCounts = @json($lostCategoryCounts);

            const statusLabels = @json($statusLabels);
            const statusCounts = @json($statusCounts);

            const yearlyLabels = @json($yearlyLabels);
            const yearlyLostData = @json($yearlyLostData);
            const yearlyFoundData = @json($yearlyFoundData);
            const yearlyLostPercentage = @json($yearlyLostPercentage);

            const locationLabels = @json($locationLabels);
            const locationLostCounts = @json($locationLostCounts);
            const locationFoundCounts = @json($locationFoundCounts);

            // 1. Yearly Breakdown Line Chart with Dual Axis (Count & Percentage)
            const ctxYearly = document.getElementById('yearlyTrendChart').getContext('2d');
            new Chart(ctxYearly, {
                type: 'line',
                data: {
                    labels: yearlyLabels,
                    datasets: [
                        {
                            label: 'Lost Reports Count',
                            data: yearlyLostData,
                            borderColor: '#752738', // UB Maroon
                            backgroundColor: 'rgba(117, 39, 56, 0.08)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Found Items Count',
                            data: yearlyFoundData,
                            borderColor: '#0dcaf0', // Cyan
                            backgroundColor: 'rgba(13, 202, 240, 0.08)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Lost Share (% of Total)',
                            data: yearlyLostPercentage,
                            borderColor: '#dc3545', // Red
                            backgroundColor: '#dc3545',
                            borderWidth: 2,
                            borderDash: [6, 4],
                            fill: false,
                            tension: 0.35,
                            pointStyle: 'rectRot',
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: { family: 'Inter', size: 11, weight: '600' },
                                usePointStyle: true,
                                padding: 12
                            }
                        },
                        tooltip: {
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.dataset.yAxisID === 'y1') {
                                        label += context.parsed.y + '%';
                                    } else {
                                        label += context.parsed.y + ' items';
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Inter', size: 11, weight: '600' } }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Items',
                                font: { family: 'Inter', size: 10, weight: '600' }
                            },
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: { family: 'Inter', size: 10 }
                            },
                            grid: { color: 'rgba(0, 0, 0, 0.05)' }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Lost Share (%)',
                                font: { family: 'Inter', size: 10, weight: '600' }
                            },
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                font: { family: 'Inter', size: 10 },
                                callback: function(value) {
                                    return value + '%';
                                }
                            },
                            grid: { drawOnChartArea: false }
                        }
                    }
                }
            });

            // 2. Campus Hazard Radar Chart
            const ctxRadar = document.getElementById('hazardRadarChart').getContext('2d');
            new Chart(ctxRadar, {
                type: 'radar',
                data: {
                    labels: locationLabels,
                    datasets: [
                        {
                            label: 'Last Known Location (Lost)',
                            data: locationLostCounts,
                            backgroundColor: 'rgba(220, 53, 69, 0.25)', // Red fill
                            borderColor: '#dc3545',
                            pointBackgroundColor: '#dc3545',
                            pointBorderColor: '#ffffff',
                            pointHoverBackgroundColor: '#ffffff',
                            pointHoverBorderColor: '#dc3545',
                            borderWidth: 2
                        },
                        {
                            label: 'Location Found (Surrendered)',
                            data: locationFoundCounts,
                            backgroundColor: 'rgba(25, 135, 84, 0.25)', // Green fill
                            borderColor: '#198754',
                            pointBackgroundColor: '#198754',
                            pointBorderColor: '#ffffff',
                            pointHoverBackgroundColor: '#ffffff',
                            pointHoverBorderColor: '#198754',
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { family: 'Inter', size: 11, weight: '600' },
                                usePointStyle: true,
                                padding: 10
                            }
                        },
                        tooltip: {
                            padding: 10,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        r: {
                            angleLines: { color: 'rgba(0, 0, 0, 0.08)' },
                            grid: { color: 'rgba(0, 0, 0, 0.08)' },
                            pointLabels: {
                                font: { family: 'Inter', size: 11, weight: '600' },
                                color: '#1e293b'
                            },
                            ticks: {
                                backdropColor: 'transparent',
                                font: { family: 'Inter', size: 9 },
                                precision: 0
                            }
                        }
                    }
                }
            });

            // 3. Category Bar Chart (Found vs Lost Items)
            const ctxCategory = document.getElementById('categoryChart').getContext('2d');
            new Chart(ctxCategory, {
                type: 'bar',
                data: {
                    labels: categoryLabels,
                    datasets: [
                        {
                            label: 'Surrendered Found Items',
                            data: foundCategoryCounts,
                            backgroundColor: 'rgba(117, 39, 56, 0.85)', // UB Maroon
                            borderColor: '#752738',
                            borderWidth: 1.5,
                            borderRadius: 6
                        },
                        {
                            label: 'Student Lost Reports',
                            data: lostCategoryCounts,
                            backgroundColor: 'rgba(254, 196, 82, 0.85)', // Gold Accent
                            borderColor: '#fec452',
                            borderWidth: 1.5,
                            borderRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: { family: 'Inter', size: 12, weight: '600' },
                                usePointStyle: true,
                                padding: 15
                            }
                        },
                        tooltip: {
                            padding: 12,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Inter', size: 11 } }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: { family: 'Inter', size: 11 }
                            },
                            grid: { color: 'rgba(0, 0, 0, 0.05)' }
                        }
                    }
                }
            });

            // 4. Inventory Status Doughnut Chart
            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusCounts,
                        backgroundColor: [
                            '#198754', // Available - Green
                            '#fec452', // Claim Pending - Gold
                            '#0dcaf0', // Ready for Pick-up - Cyan
                            '#6c757d', // Claimed - Secondary Gray
                            '#212529'  // Disposed - Dark
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { family: 'Inter', size: 11, weight: '500' },
                                usePointStyle: true,
                                padding: 12
                            }
                        },
                        tooltip: {
                            padding: 12,
                            cornerRadius: 8
                        }
                    },
                    cutout: '65%'
                }
            });
        });
    </script>
@endsection

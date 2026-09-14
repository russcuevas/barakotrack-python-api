@extends('layouts.app')

@section('title', 'All Campus Lost Item Reports | SAO Admin')

@section('content')
    @include('partials.loading_overlay')

    @php
        $totalReports = $allLostReports->count();
        $openCount = $allLostReports->where('status', 'open')->count();
        $claimPendingCount = $allLostReports->where('status', 'claim_pending')->count();
        $resolvedCount = $allLostReports->where('status', 'resolved')->count();
    @endphp

    <!-- Page Header (Image 2 style) -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 gap-sm-3">
        <div>
            <h3 class="fw-bold m-0" style="color: var(--primary-color);">All Campus Lost Item Reports</h3>
            <p class="text-muted fs-7 mb-0 mt-1">Monitor student lost item reports and track recovery statuses across
                University of Batangas.</p>
        </div>
    </div>

    <!-- Mini Metric Cards (Image 2 style) -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="mini-stat-card">
                <div class="mini-stat-icon" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
                    <i class="bi bi-journal-text"></i>
                </div>
                <div>
                    <div class="mini-stat-label">Total Lost Reports</div>
                    <div class="mini-stat-value">{{ $totalReports }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="mini-stat-card">
                <div class="mini-stat-icon" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                    <i class="bi bi-search"></i>
                </div>
                <div>
                    <div class="mini-stat-label">Active Campus Searches</div>
                    <div class="mini-stat-value text-danger">{{ $openCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="mini-stat-card">
                <div class="mini-stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <div class="mini-stat-label">Claim Under Verification</div>
                    <div class="mini-stat-value text-warning">{{ $claimPendingCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="mini-stat-card">
                <div class="mini-stat-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <div class="mini-stat-label">Resolved / Returned</div>
                    <div class="mini-stat-value text-success">{{ $resolvedCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lost Reports Table Card (Image 2 style) -->
    <div class="card card-custom p-4 mb-4 shadow-sm border-0" style="background: #ffffff; border-radius: 12px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-journal-text text-danger fs-5"></i>
                <h5 class="fw-bold m-0 text-dark">Student Lost Reports Directory</h5>
            </div>
            <span class="badge bg-light text-secondary border px-3 py-1 fw-semibold">
                <i class="bi bi-card-checklist me-1"></i> {{ $totalReports }} Logged Reports
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead>
                    <tr style="font-size: 0.8rem; color: #1e293b; border-bottom: 2px solid #f1f5f9;">
                        <th class="py-3 px-3">Ref No.</th>
                        <th class="py-3 px-3">Student Reporter</th>
                        <th class="py-3 px-3">Item Details</th>
                        <th class="py-3 px-3">Category</th>
                        <th class="py-3 px-3">Date Lost & Location</th>
                        <th class="py-3 px-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allLostReports as $report)
                        <tr style="font-size: 0.82rem; border-bottom: 1px solid #f8fafc;">
                            <td class="py-3 px-3 fw-bold" style="color: #691220;">
                                LST-{{ $report->created_at ? $report->created_at->format('Ymd') : '20260914' }}-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="py-3 px-3">
                                <div class="fw-bold text-dark">{{ $report->user->name ?? 'Student' }}</div>
                                <small class="text-muted">UB-{{ $report->user->student_id_number ?? 'Student' }}</small>
                            </td>
                            <td class="py-3 px-3">
                                <div class="d-flex align-items-center gap-2">
                                    @if ($report->image_path)
                                        <img src="{{ $report->image_path }}" class="rounded border" width="40"
                                            height="40" style="object-fit: cover;" alt="{{ $report->title }}">
                                    @else
                                        <div class="rounded border bg-light d-flex align-items-center justify-content-center text-muted"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi bi-image fs-6 opacity-50"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark">{{ $report->title }}</div>
                                        <small class="text-muted">{{ Str::limit($report->description, 50) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <span class="badge bg-light text-secondary border px-2 py-1">
                                    {{ $report->category->name ?? 'General' }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-muted">
                                <div>{{ $report->date_lost ? $report->date_lost->format('M d, Y') : 'N/A' }}</div>
                                <small class="text-muted"><i class="bi bi-geo-alt text-danger me-1"></i>
                                    {{ $report->location }}</small>
                            </td>
                            <td class="py-3 px-3 text-center">
                                @if ($report->status === 'open')
                                    <span
                                        class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 fw-semibold">
                                        <i class="bi bi-search me-1"></i> Open Search
                                    </span>
                                @elseif($report->status === 'claim_pending')
                                    <span
                                        class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 fw-semibold">
                                        <i class="bi bi-clock-history me-1"></i> Claim Pending
                                    </span>
                                @elseif($report->status === 'resolved')
                                    <span
                                        class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-1 fw-semibold">
                                        <i class="bi bi-check-circle me-1"></i> Resolved
                                    </span>
                                @else
                                    <span
                                        class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-1 fw-semibold">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                <h6 class="fw-bold text-dark mb-1">No Campus Lost Reports Logged</h6>
                                <p class="fs-7 text-muted mb-0">No active or historical student lost reports recorded in the
                                    database.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

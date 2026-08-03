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
@endsection

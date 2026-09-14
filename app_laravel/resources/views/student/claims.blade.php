@extends('layouts.app')

@section('title', 'My Claims Tracker | Barako Track')

@section('content')
    @include('partials.loading_overlay')

    @php
        $totalClaims = $claims->count();
        $pendingCount = $claims->where('status', 'pending')->count();
        $readyPickupCount = $claims
            ->filter(function ($c) {
                return $c->status === 'approved' && optional($c->foundItem)->status !== 'claimed';
            })
            ->count();
        $claimedCount = $claims
            ->filter(function ($c) {
                return $c->status === 'approved' && optional($c->foundItem)->status === 'claimed';
            })
            ->count();
    @endphp

    <!-- Page Header (Image 2 style) -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 gap-sm-3">
        <div>
            <h3 class="fw-bold m-0" style="color: var(--primary-color);">My Submitted Claims</h3>
            <p class="text-muted fs-7 mb-0 mt-1">Monitor SAO verification decisions and retrieval status for your claimed
                items.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('student.found-items') }}" class="btn btn-outline-secondary btn-sm px-3 py-2 fw-semibold">
                <i class="bi bi-box-seam me-1"></i> Browse Found Items
            </a>
        </div>
    </div>

    <!-- Mini Metric Cards (Image 2 style) -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="mini-stat-card">
                <div class="mini-stat-icon" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
                    <i class="bi bi-collection-fill"></i>
                </div>
                <div>
                    <div class="mini-stat-label">Total Claims</div>
                    <div class="mini-stat-value">{{ $totalClaims }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="mini-stat-card">
                <div class="mini-stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <div class="mini-stat-label">Under SAO Review</div>
                    <div class="mini-stat-value text-warning">{{ $pendingCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="mini-stat-card">
                <div class="mini-stat-icon" style="background: rgba(13, 202, 240, 0.12); color: #0dcaf0;">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <div>
                    <div class="mini-stat-label">Ready for Pick-up</div>
                    <div class="mini-stat-value text-info">{{ $readyPickupCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="mini-stat-card">
                <div class="mini-stat-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="mini-stat-label">Completed Claims</div>
                    <div class="mini-stat-value text-success">{{ $claimedCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Claims Table Card (Image 2 style) -->
    <div class="card card-custom p-4 mb-4 shadow-sm border-0" style="background: #ffffff; border-radius: 12px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-shield-check text-danger fs-5"></i>
                <h5 class="fw-bold m-0 text-dark">Claims History & Decisions</h5>
            </div>
            <span class="badge bg-light text-secondary border px-3 py-1 fw-semibold">
                <i class="bi bi-card-checklist me-1"></i> {{ $totalClaims }} Submitted
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead>
                    <tr style="font-size: 0.8rem; color: #1e293b; border-bottom: 2px solid #f1f5f9;">
                        <th class="py-3 px-3">Ref No.</th>
                        <th class="py-3 px-3">Target Found Item</th>
                        <th class="py-3 px-3">Submitted Proof Details</th>
                        <th class="py-3 px-3">Date Submitted</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3">SAO Admin Feedback</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($claims as $claim)
                        <tr style="font-size: 0.82rem; border-bottom: 1px solid #f8fafc;">
                            <td class="py-3 px-3 fw-bold" style="color: #691220;">
                                CLM-{{ $claim->created_at ? $claim->created_at->format('Ymd') : '20260914' }}-{{ str_pad($claim->id, 4, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="py-3 px-3">
                                <div class="d-flex align-items-center gap-2">
                                    @if (optional($claim->foundItem)->image_path)
                                        <img src="{{ $claim->foundItem->image_path }}" class="rounded border" width="42"
                                            height="42" style="object-fit: cover;" alt="Item">
                                    @else
                                        <div class="rounded border bg-light d-flex align-items-center justify-content-center text-muted"
                                            style="width: 42px; height: 42px;">
                                            <i class="bi bi-box-seam text-secondary"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark">
                                            {{ $claim->foundItem ? $claim->foundItem->title : 'Item #' . $claim->found_item_id }}
                                        </div>
                                        <small class="text-muted"><i class="bi bi-building"></i> Storage:
                                            <strong>{{ $claim->foundItem->storage_location ?? 'SAO Storage' }}</strong></small>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3" style="max-width: 250px;">
                                <p class="text-secondary mb-1 fs-7" style="line-height: 1.35;">
                                    {{ Str::limit($claim->proof_description, 70) }}</p>
                                @if ($claim->proof_image)
                                    <a href="{{ $claim->proof_image }}" target="_blank"
                                        class="btn btn-xs btn-light border py-0 px-2 fs-7 fw-semibold"
                                        style="font-size: 0.72rem;">
                                        <i class="bi bi-image text-primary me-1"></i> View Attached Photo
                                    </a>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-muted">
                                {{ $claim->created_at->format('M d, Y') }}<br>
                                <small class="text-muted">{{ $claim->created_at->format('h:i A') }}</small>
                            </td>
                            <td class="py-3 px-3">
                                @if ($claim->status === 'pending')
                                    <span
                                        class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 fw-semibold">
                                        <i class="bi bi-clock-history me-1"></i> Under Review
                                    </span>
                                @elseif($claim->status === 'approved')
                                    @if (optional($claim->foundItem)->status === 'claimed')
                                        <span
                                            class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-1 fw-semibold">
                                            <i class="bi bi-check2-all me-1"></i> Claimed & Retrieved
                                        </span>
                                    @else
                                        <span
                                            class="badge rounded-pill bg-info-subtle text-info border border-info-subtle px-3 py-1 fw-semibold">
                                            <i class="bi bi-box-seam-fill me-1"></i> Ready for Pick-up
                                        </span>
                                    @endif
                                @elseif($claim->status === 'rejected')
                                    <span
                                        class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 fw-semibold">
                                        <i class="bi bi-x-circle-fill me-1"></i> Rejected
                                    </span>
                                @else
                                    <span
                                        class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-1 fw-semibold">
                                        {{ ucfirst($claim->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                @if ($claim->admin_notes)
                                    <div class="p-2 rounded bg-light border text-dark fs-7"
                                        style="line-height: 1.35; max-width: 220px;">
                                        <i class="bi bi-chat-left-text text-secondary me-1"></i> {{ $claim->admin_notes }}
                                    </div>
                                @else
                                    <span class="text-muted fs-7 fst-italic">Pending SAO inspection...</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                <h6 class="fw-bold text-dark mb-1">No Claims Submitted Yet</h6>
                                <p class="fs-7 text-muted mb-3">You haven't submitted any claim requests for surrendered
                                    found items.</p>
                                <a href="{{ route('student.found-items') }}"
                                    class="btn btn-sm btn-primary-custom px-4 fw-bold">
                                    <i class="bi bi-search me-1"></i> Browse Found Items Directory
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

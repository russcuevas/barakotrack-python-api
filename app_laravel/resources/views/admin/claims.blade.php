@extends('layouts.app')

@section('title', 'Claim Verification Management | SAO Admin')

@section('content')
@include('partials.loading_overlay')

@php
    $pendingCount = $pendingClaims->count();
    $processedCount = $processedClaims->count();
    $readyForPickupCount = $processedClaims->filter(function($c) {
        return $c->status === 'approved' && optional($c->foundItem)->status === 'ready_for_pickup';
    })->count();
    $completedCount = $processedClaims->filter(function($c) {
        return $c->status === 'approved' && optional($c->foundItem)->status === 'claimed';
    })->count();
@endphp

<!-- Page Header (Image 2 style) -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 gap-sm-3">
    <div>
        <h3 class="fw-bold m-0" style="color: var(--primary-color);">Student Claim Verification Panel</h3>
        <p class="text-muted fs-7 mb-0 mt-1">Review student proof of ownership, inspect evidence photos, and issue verification approvals.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.inventory') }}" class="btn btn-outline-secondary btn-sm px-3 py-2 fw-semibold">
            <i class="bi bi-archive me-1"></i> Found Inventory
        </a>
    </div>
</div>

<!-- Mini Metric Cards (Image 2 style) -->
<div class="row g-3 g-md-4 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="mini-stat-card">
            <div class="mini-stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <div class="mini-stat-label">Pending Decision</div>
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
                <div class="mini-stat-value text-info">{{ $readyForPickupCount }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="mini-stat-card">
            <div class="mini-stat-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <div class="mini-stat-label">Delivered & Claimed</div>
                <div class="mini-stat-value text-success">{{ $completedCount }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="mini-stat-card">
            <div class="mini-stat-icon" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
                <i class="bi bi-journal-check"></i>
            </div>
            <div>
                <div class="mini-stat-label">Processed History</div>
                <div class="mini-stat-value">{{ $processedCount }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Claims Verification Table Card (Image 2 style) -->
<div class="card card-custom p-4 mb-4 shadow-sm border-0" style="background: #ffffff; border-radius: 12px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-clock-history text-danger fs-5"></i>
            <h5 class="fw-bold m-0 text-dark">Pending Claims Awaiting Decision</h5>
        </div>
        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 fw-semibold">
            <i class="bi bi-exclamation-circle me-1"></i> {{ $pendingCount }} Action Required
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle m-0">
            <thead>
                <tr style="font-size: 0.8rem; color: #1e293b; border-bottom: 2px solid #f1f5f9;">
                    <th class="py-3 px-3">Ref No.</th>
                    <th class="py-3 px-3">Student Claimant</th>
                    <th class="py-3 px-3">Item to Claim</th>
                    <th class="py-3 px-3">Proof Description & Evidence</th>
                    <th class="py-3 px-3">CNN AI Match</th>
                    <th class="py-3 px-3 text-center" style="min-width: 220px;">Verification Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingClaims as $claim)
                @php
                    $score = $claim->match_score ?? 0;
                    $badgeClass = $score >= 85 ? 'bg-success-subtle text-success border border-success-subtle' : ($score >= 50 ? 'bg-warning-subtle text-warning border border-warning-subtle' : 'bg-light text-secondary border');
                @endphp
                <tr style="font-size: 0.82rem; border-bottom: 1px solid #f8fafc;">
                    <td class="py-3 px-3 fw-bold" style="color: #691220;">
                        CLM-{{ $claim->created_at ? $claim->created_at->format('Ymd') : '20260914' }}-{{ str_pad($claim->id, 4, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="py-3 px-3">
                        <div class="fw-bold text-dark">{{ $claim->user->name ?? 'Student' }}</div>
                        <small class="text-muted">ID: {{ $claim->user->student_id_number ?? 'UB-Student' }}</small><br>
                        <small class="text-muted"><i class="bi bi-telephone text-secondary"></i> {{ $claim->user->phone ?? 'N/A' }}</small>
                    </td>
                    <td class="py-3 px-3">
                        <div class="d-flex align-items-center gap-2">
                            @if(optional($claim->foundItem)->image_path)
                                <img src="{{ $claim->foundItem->image_path }}" class="rounded border" width="42" height="42" style="object-fit: cover;" alt="{{ $claim->foundItem->title }}">
                            @else
                                <div class="rounded border bg-light d-flex align-items-center justify-content-center text-muted" style="width: 42px; height: 42px;">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                            @endif
                            <div>
                                <div class="fw-bold text-dark">{{ $claim->foundItem->title ?? 'Found Item' }}</div>
                                <small class="text-muted d-block"><i class="bi bi-geo-alt text-danger"></i> {{ $claim->foundItem->location ?? 'Campus' }}</small>
                                <span class="badge bg-light text-secondary border"><i class="bi bi-building me-1"></i> {{ $claim->foundItem->storage_location ?? 'SAO' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-3" style="max-width: 250px;">
                        <p class="fs-7 text-secondary mb-1 bg-light p-2 rounded border" style="line-height: 1.35;">{{ $claim->proof_description }}</p>
                        @if($claim->proof_image)
                            <button class="btn btn-xs btn-light border py-1 px-2 fs-7 fw-semibold shadow-sm"
                                onclick="openProofModal('{{ $claim->proof_image }}', '{{ addslashes($claim->user->name ?? 'Student') }}', '{{ addslashes($claim->foundItem->title ?? 'Item') }}')">
                                <i class="bi bi-image text-primary me-1"></i> View Proof Photo
                            </button>
                        @endif
                    </td>
                    <td class="py-3 px-3">
                        @if($score > 0)
                            <span class="badge {{ $badgeClass }} p-2 fw-bold rounded-pill">
                                <i class="bi bi-cpu-fill me-1"></i> {{ $score }}% Match
                            </span>
                        @else
                            <span class="badge bg-light text-secondary border px-2 py-1"><i class="bi bi-eye"></i> Manual Review</span>
                        @endif
                    </td>
                    <td class="py-3 px-3">
                        <div class="d-flex flex-column gap-2">
                            <!-- Approve Form -->
                            <form action="{{ route('admin.claims.approve', $claim->id) }}" method="POST" class="m-0">
                                @csrf
                                <div class="input-group input-group-sm mb-1">
                                    <input type="text" name="admin_notes" class="form-control" style="font-size: 0.76rem;" placeholder="Staff note..." value="Proof verified by SAO Staff. Ready for pickup.">
                                </div>
                                <button class="btn btn-sm btn-success w-100 fw-bold py-1">
                                    <i class="bi bi-check-circle-fill me-1"></i> Approve Claim
                                </button>
                            </form>

                            <!-- Reject Form -->
                            <form action="{{ route('admin.claims.reject', $claim->id) }}" method="POST" class="m-0">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger w-100 py-1" onclick="return confirm('Are you sure you want to reject this claim?')">
                                    <i class="bi bi-x-circle me-1"></i> Reject Claim
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-check2-circle fs-2 d-block mb-2 text-success opacity-50"></i>
                        <h6 class="fw-bold text-dark mb-1">All Caught Up!</h6>
                        <p class="fs-7 text-muted mb-0">No pending student claims currently requiring SAO decision.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Processed Claims History Table Card (Image 2 style) -->
<div class="card card-custom p-4 mb-4 shadow-sm border-0" style="background: #ffffff; border-radius: 12px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-journal-check text-danger fs-5"></i>
            <h5 class="fw-bold m-0 text-dark">Processed Claims History</h5>
        </div>
        <span class="badge bg-light text-secondary border px-3 py-1 fw-semibold">
            <i class="bi bi-clock-history me-1"></i> Audit Trail
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle m-0">
            <thead>
                <tr style="font-size: 0.8rem; color: #1e293b; border-bottom: 2px solid #f1f5f9;">
                    <th class="py-3 px-3">Ref No.</th>
                    <th class="py-3 px-3">Student</th>
                    <th class="py-3 px-3">Target Found Item</th>
                    <th class="py-3 px-3">Proof Photo</th>
                    <th class="py-3 px-3">CNN AI Match</th>
                    <th class="py-3 px-3">Status</th>
                    <th class="py-3 px-3">Admin Notes</th>
                    <th class="py-3 px-3">Verified By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($processedClaims as $pClaim)
                @php
                    $pScore = $pClaim->match_score ?? 0;
                    $pBadgeColor = $pScore >= 85 ? 'bg-success-subtle text-success border border-success-subtle' : ($pScore >= 50 ? 'bg-warning-subtle text-warning border border-warning-subtle' : 'bg-light text-secondary border');
                @endphp
                <tr style="font-size: 0.82rem; border-bottom: 1px solid #f8fafc;">
                    <td class="py-3 px-3 fw-bold" style="color: #691220;">
                        CLM-{{ $pClaim->created_at ? $pClaim->created_at->format('Ymd') : '20260914' }}-{{ str_pad($pClaim->id, 4, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="py-3 px-3">
                        <div class="fw-bold text-dark">{{ $pClaim->user->name ?? 'Student' }}</div>
                        <small class="text-muted">{{ $pClaim->user->student_id_number ?? 'UB-Student' }}</small>
                    </td>
                    <td class="py-3 px-3">
                        <div class="d-flex align-items-center gap-2">
                            @if(optional($pClaim->foundItem)->image_path)
                                <img src="{{ $pClaim->foundItem->image_path }}" class="rounded border shadow-sm" width="38" height="38" style="object-fit: cover;" alt="{{ $pClaim->foundItem->title }}">
                            @endif
                            <div>
                                <div class="fw-bold text-dark">{{ $pClaim->foundItem->title ?? 'Item' }}</div>
                                <small class="text-muted"><i class="bi bi-building"></i> {{ $pClaim->foundItem->storage_location ?? 'SAO' }}</small>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-3">
                        @if($pClaim->proof_image)
                            <button class="btn btn-xs btn-light border py-1 px-2 fs-7 shadow-sm"
                                onclick="openProofModal('{{ $pClaim->proof_image }}', '{{ addslashes($pClaim->user->name ?? 'Student') }}', '{{ addslashes($pClaim->foundItem->title ?? 'Item') }}')">
                                <i class="bi bi-image text-primary me-1"></i> View Photo
                            </button>
                        @else
                            <small class="text-muted">None</small>
                        @endif
                    </td>
                    <td class="py-3 px-3">
                        @if($pScore > 0)
                            <span class="badge {{ $pBadgeColor }} px-2 py-1"><i class="bi bi-cpu-fill me-1"></i> {{ $pScore }}%</span>
                        @else
                            <small class="text-muted">N/A</small>
                        @endif
                    </td>
                    <td class="py-3 px-3">
                        @if($pClaim->status === 'approved')
                            @if(optional($pClaim->foundItem)->status === 'ready_for_pickup')
                                <span class="badge rounded-pill bg-info-subtle text-info border border-info-subtle mb-1 d-block py-1">
                                    <i class="bi bi-box-seam me-1"></i> Ready for Pick-up
                                </span>
                                <form action="{{ route('admin.claims.mark-claimed', $pClaim->id) }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-success py-1 px-2 fw-bold w-100" onclick="return confirm('Confirm that student has physically picked up this item?')">
                                        <i class="bi bi-check-lg me-1"></i> Mark as Claimed
                                    </button>
                                </form>
                            @elseif(optional($pClaim->foundItem)->status === 'claimed')
                                <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-1">
                                    <i class="bi bi-check2-all me-1"></i> Claimed & Delivered
                                </span>
                            @else
                                <span class="badge rounded-pill bg-success-subtle text-success px-3 py-1">Approved</span>
                            @endif
                        @elseif($pClaim->status === 'rejected')
                            <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-1">Rejected</span>
                        @endif
                    </td>
                    <td class="py-3 px-3"><small class="text-secondary">{{ $pClaim->admin_notes ?: 'N/A' }}</small></td>
                    <td class="py-3 px-3"><small class="text-muted">{{ $pClaim->verifier->name ?? 'SAO Staff' }}</small></td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No processed claims history yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: View Proof Photo -->
<div class="modal fade" id="proofPhotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #691220 0%, #4d0a15 100%);">
                <h5 class="modal-header-title fw-bold m-0 text-warning">
                    <i class="bi bi-image-fill me-2"></i> Proof of Ownership Document
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3 text-start bg-light p-3 rounded border">
                    <div class="fw-bold text-dark fs-6" id="modalClaimantName">Claimant</div>
                    <small class="text-danger fw-semibold" id="modalItemTitle">Item</small>
                </div>
                <div class="p-2 rounded border bg-white shadow-sm d-inline-block w-100">
                    <img id="modalProofImage" src="" class="img-fluid rounded" style="max-height: 480px; object-fit: contain;" alt="Proof Evidence Photo">
                </div>
            </div>
            <div class="modal-footer bg-light">
                <a id="modalProofDownload" href="" target="_blank" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Open Original Image
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openProofModal(imageSrc, claimantName, itemTitle) {
        document.getElementById('modalProofImage').src = imageSrc;
        document.getElementById('modalProofDownload').href = imageSrc;
        document.getElementById('modalClaimantName').innerText = "Student Claimant: " + claimantName;
        document.getElementById('modalItemTitle').innerText = "Claimed Found Item: " + itemTitle;
        var proofModal = new bootstrap.Modal(document.getElementById('proofPhotoModal'));
        proofModal.show();
    }
</script>
@endsection

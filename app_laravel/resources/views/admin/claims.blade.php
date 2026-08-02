@extends('layouts.app')

@section('title', 'Claim Verification Management | SAO Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold m-0 text-dark"><i class="bi bi-shield-exclamation text-warning me-2"></i> Student Claim Verification Panel</h4>
        <span class="text-muted">Review student proof of ownership, inspect evidence, and issue approvals or rejections.</span>
    </div>
</div>

<!-- Pending Claims Verification Table -->
<div class="card card-custom p-4 mb-4 border-warning shadow-sm">
    <h5 class="fw-bold mb-3 text-warning"><i class="bi bi-clock-history me-2"></i> Pending Claims Awaiting Decision</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle m-0">
            <thead class="table-dark">
                <tr>
                    <th>Claim ID</th>
                    <th>Student Claimant</th>
                    <th>Item to Claim</th>
                    <th>Proof Description & Evidence</th>
                    <th>CNN AI Match Score</th>
                    <th>Verification Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingClaims as $claim)
                @php
                    $score = $claim->match_score ?? 0;
                    $badgeColor = $score >= 85 ? 'bg-success' : ($score >= 50 ? 'bg-warning text-dark' : 'bg-secondary');
                @endphp
                <tr>
                    <td><strong class="text-warning">#CLM-{{ $claim->id }}</strong></td>
                    <td>
                        <div class="fw-bold">{{ $claim->user->name ?? 'Student' }}</div>
                        <small class="text-muted">ID: {{ $claim->user->student_id_number ?? 'UB-Student' }}</small><br>
                        <small class="text-muted"><i class="bi bi-telephone"></i> {{ $claim->user->phone ?? 'N/A' }}</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if(optional($claim->foundItem)->image_path)
                                <img src="{{ $claim->foundItem->image_path }}" class="rounded border shadow-sm" width="50" height="50" style="object-fit: cover;" alt="{{ $claim->foundItem->title }}">
                            @endif
                            <div>
                                <div class="fw-bold text-primary">{{ $claim->foundItem->title ?? 'Found Item' }}</div>
                                <small class="text-muted"><i class="bi bi-geo-alt"></i> Found: {{ $claim->foundItem->location ?? 'Campus' }}</small><br>
                                <span class="badge bg-secondary"><i class="bi bi-building"></i> {{ $claim->foundItem->storage_location ?? 'SAO' }}</span>
                            </div>
                        </div>
                    </td>
                    <td style="max-width: 260px;">
                        <p class="fs-7 mb-2 bg-light p-2 rounded border">{{ $claim->proof_description }}</p>
                        @if($claim->proof_image)
                            <button class="btn btn-xs btn-outline-primary py-1 px-2 fs-7 fw-semibold"
                                onclick="openProofModal('{{ $claim->proof_image }}', '{{ addslashes($claim->user->name ?? 'Student') }}', '{{ addslashes($claim->foundItem->title ?? 'Item') }}')">
                                <i class="bi bi-image me-1"></i> View Proof Photo
                            </button>
                        @endif
                    </td>
                    <td>
                        @if($score > 0)
                            <span class="badge {{ $badgeColor }} p-2 fw-bold"><i class="bi bi-cpu-fill me-1"></i> {{ $score }}% Match</span>
                        @else
                            <span class="badge bg-light text-dark border"><i class="bi bi-eye"></i> Manual Review</span>
                        @endif
                    </td>
                    <td style="width: 250px;">
                        <!-- Approve Form -->
                        <form action="{{ route('admin.claims.approve', $claim->id) }}" method="POST" class="mb-2">
                            @csrf
                            <div class="input-group input-group-sm mb-1">
                                <input type="text" name="admin_notes" class="form-control" placeholder="Admin note..." value="Proof verified by SAO Staff. Ready for pickup.">
                            </div>
                            <button class="btn btn-sm btn-success w-100 fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Approve Claim</button>
                        </form>

                        <!-- Reject Form -->
                        <form action="{{ route('admin.claims.reject', $claim->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Are you sure you want to reject this claim?')">
                                <i class="bi bi-x-circle me-1"></i> Reject Claim
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4"><i class="bi bi-check2-all text-success me-1"></i> No pending claims requiring verification.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Processed Claims History Table -->
<div class="card card-custom p-4 mb-4">
    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-journal-check me-2"></i> Processed Claims History</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle m-0">
            <thead class="table-light">
                <tr>
                    <th>Claim ID</th>
                    <th>Student</th>
                    <th>Target Found Item</th>
                    <th>Proof Photo</th>
                    <th>CNN AI Match</th>
                    <th>Status</th>
                    <th>Admin Notes</th>
                    <th>Verified By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($processedClaims as $pClaim)
                @php
                    $pScore = $pClaim->match_score ?? 0;
                    $pBadgeColor = $pScore >= 85 ? 'bg-success' : ($pScore >= 50 ? 'bg-warning text-dark' : 'bg-secondary');
                @endphp
                <tr>
                    <td><strong>#CLM-{{ $pClaim->id }}</strong></td>
                    <td>{{ $pClaim->user->name ?? 'Student' }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if(optional($pClaim->foundItem)->image_path)
                                <img src="{{ $pClaim->foundItem->image_path }}" class="rounded border shadow-sm" width="40" height="40" style="object-fit: cover;" alt="{{ $pClaim->foundItem->title }}">
                            @endif
                            <div>
                                <div class="fw-bold text-dark">{{ $pClaim->foundItem->title ?? 'Item' }}</div>
                                <small class="text-muted"><i class="bi bi-building"></i> {{ $pClaim->foundItem->storage_location ?? 'SAO' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($pClaim->proof_image)
                            <button class="btn btn-xs btn-outline-secondary py-0 px-2 fs-7"
                                onclick="openProofModal('{{ $pClaim->proof_image }}', '{{ addslashes($pClaim->user->name ?? 'Student') }}', '{{ addslashes($pClaim->foundItem->title ?? 'Item') }}')">
                                <i class="bi bi-image me-1"></i> View Photo
                            </button>
                        @else
                            <small class="text-muted">None</small>
                        @endif
                    </td>
                    <td>
                        @if($pScore > 0)
                            <span class="badge {{ $pBadgeColor }}"><i class="bi bi-cpu-fill me-1"></i> {{ $pScore }}%</span>
                        @else
                            <small class="text-muted">N/A</small>
                        @endif
                    </td>
                    <td>
                        @if($pClaim->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($pClaim->status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @endif
                    </td>
                    <td><small class="text-muted">{{ $pClaim->admin_notes }}</small></td>
                    <td><small class="text-muted">{{ $pClaim->verifier->name ?? 'SAO Staff' }}</small></td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-3">No processed claims history yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: View Proof Photo -->
<div class="modal fade" id="proofPhotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #1e1e2d;">
                <h5 class="modal-header-title fw-bold m-0">
                    <i class="bi bi-image-fill me-2 text-warning"></i> Proof of Ownership Document
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3 text-start bg-light p-3 rounded border">
                    <div class="fw-bold text-dark fs-6" id="modalClaimantName">Claimant</div>
                    <small class="text-primary fw-semibold" id="modalItemTitle">Item</small>
                </div>
                <div class="p-2 rounded border bg-white shadow-sm d-inline-block w-100">
                    <img id="modalProofImage" src="" class="img-fluid rounded" style="max-height: 480px; object-fit: contain;" alt="Proof Evidence Photo">
                </div>
            </div>
            <div class="modal-footer">
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

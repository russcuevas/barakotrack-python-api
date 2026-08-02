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
                    <th>Verification Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingClaims as $claim)
                <tr>
                    <td><strong class="text-warning">#CLM-{{ $claim->id }}</strong></td>
                    <td>
                        <div class="fw-bold">{{ $claim->user->name ?? 'Student' }}</div>
                        <small class="text-muted">ID: {{ $claim->user->student_id_number ?? 'UB-Student' }}</small><br>
                        <small class="text-muted"><i class="bi bi-telephone"></i> {{ $claim->user->phone ?? 'N/A' }}</small>
                    </td>
                    <td>
                        <div class="fw-bold text-primary">{{ $claim->foundItem->title ?? 'Found Item' }}</div>
                        <small class="text-muted"><i class="bi bi-geo-alt"></i> Found: {{ $claim->foundItem->location ?? 'Campus' }}</small><br>
                        <span class="badge bg-secondary"><i class="bi bi-building"></i> {{ $claim->foundItem->storage_location ?? 'SAO' }}</span>
                    </td>
                    <td style="max-width: 280px;">
                        <p class="fs-7 mb-2 bg-light p-2 rounded border">{{ $claim->proof_description }}</p>
                        @if($claim->proof_image)
                            <a href="{{ $claim->proof_image }}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2 fs-7">
                                <i class="bi bi-image me-1"></i> View Proof Photo
                            </a>
                        @endif
                    </td>
                    <td style="width: 260px;">
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
                    <td colspan="5" class="text-center text-muted py-4"><i class="bi bi-check2-all text-success me-1"></i> No pending claims requiring verification.</td>
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
                    <th>Status</th>
                    <th>Admin Notes</th>
                    <th>Verified By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($processedClaims as $pClaim)
                <tr>
                    <td><strong>#CLM-{{ $pClaim->id }}</strong></td>
                    <td>{{ $pClaim->user->name ?? 'Student' }}</td>
                    <td>{{ $pClaim->foundItem->title ?? 'Item' }}</td>
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
                    <td colspan="6" class="text-center text-muted py-3">No processed claims history yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'My Claims Tracker | Barako Track')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold m-0" style="color: var(--primary-color);"><i class="bi bi-shield-check me-2 text-warning"></i> My Submitted Claims Tracker</h4>
        <span class="text-muted">Monitor SAO verification decisions and instructions for picking up your claimed items.</span>
    </div>
</div>

<div class="card card-custom p-4 mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle m-0">
            <thead class="table-light">
                <tr>
                    <th>Claim ID</th>
                    <th>Target Found Item</th>
                    <th>Submitted Proof Details</th>
                    <th>Date Submitted</th>
                    <th>Status</th>
                    <th>SAO Admin Feedback</th>
                </tr>
            </thead>
            <tbody>
                @forelse($claims as $claim)
                <tr>
                    <td><strong class="text-primary">#CLM-{{ $claim->id }}</strong></td>
                    <td>
                        <div class="fw-bold">{{ $claim->foundItem ? $claim->foundItem->title : 'Found Item #' . $claim->found_item_id }}</div>
                        <small class="text-muted"><i class="bi bi-building"></i> Storage: {{ $claim->foundItem->storage_location ?? 'SAO Office' }}</small>
                    </td>
                    <td><small>{{ Str::limit($claim->proof_description, 60) }}</small></td>
                    <td>{{ $claim->created_at->format('M d, Y h:i A') }}</td>
                    <td>
                        @if($claim->status === 'pending')
                            <span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i> Under Review</span>
                        @elseif($claim->status === 'approved')
                            @if(optional($claim->foundItem)->status === 'claimed')
                                <span class="badge bg-secondary p-2"><i class="bi bi-check2-all me-1"></i> Claimed & Picked Up</span>
                            @else
                                <span class="badge bg-info text-dark p-2"><i class="bi bi-box-seam-fill me-1"></i> Ready for Pick-up</span>
                            @endif
                        @elseif($claim->status === 'rejected')
                            <span class="badge bg-danger"><i class="bi bi-x-circle-fill me-1"></i> Rejected</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($claim->status) }}</span>
                        @endif
                    </td>
                    <td>
                        @if($claim->admin_notes)
                            <span class="text-dark fs-7 bg-light p-1 px-2 rounded border">{{ $claim->admin_notes }}</span>
                        @else
                            <span class="text-muted fs-7">Awaiting SAO inspection...</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        No claims submitted yet. Browse the Found Items directory to submit a claim for your item.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

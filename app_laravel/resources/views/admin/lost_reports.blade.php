@extends('layouts.app')

@section('title', 'All Campus Lost Item Reports | SAO Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold m-0" style="color: var(--primary-color);"><i class="bi bi-journal-text me-2"></i> All Campus Lost Item Reports</h4>
        <span class="text-muted">Monitor all student lost item reports across University of Batangas campus.</span>
    </div>
</div>

<div class="card card-custom p-4 mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle m-0">
            <thead class="table-light">
                <tr>
                    <th>Report ID</th>
                    <th>Student Reporter</th>
                    <th>Item & Description</th>
                    <th>Category</th>
                    <th>Date Lost & Location</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allLostReports as $report)
                <tr>
                    <td><strong>#LST-{{ $report->id }}</strong></td>
                    <td>
                        <div class="fw-bold text-dark">{{ $report->user->name ?? 'Student' }}</div>
                        <small class="text-muted">ID: {{ $report->user->student_id_number ?? 'UB-Student' }}</small>
                    </td>
                    <td>
                        <div class="fw-bold">{{ $report->title }}</div>
                        <small class="text-muted">{{ Str::limit($report->description, 60) }}</small>
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ $report->category->name }}</span></td>
                    <td>
                        <div>{{ $report->date_lost->format('M d, Y') }}</div>
                        <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $report->location }}</small>
                    </td>
                    <td>
                        @if($report->status === 'open')
                            <span class="badge bg-danger">Open Search</span>
                        @elseif($report->status === 'claim_pending')
                            <span class="badge bg-warning text-dark">Claim Pending</span>
                        @elseif($report->status === 'resolved')
                            <span class="badge bg-success">Resolved</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No campus lost item reports submitted.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

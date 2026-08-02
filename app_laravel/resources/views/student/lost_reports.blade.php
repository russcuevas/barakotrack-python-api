@extends('layouts.app')

@section('title', 'My Reported Lost Items | Barako Track')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold m-0" style="color: var(--primary-color);"><i class="bi bi-card-checklist me-2"></i> My Reported Lost Items</h4>
        <span class="text-muted">Track the status of items you reported lost on campus.</span>
    </div>
    <button class="btn btn-primary-custom px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#reportLostModal">
        <i class="bi bi-plus-circle-fill me-1"></i> Report New Lost Item
    </button>
</div>

<div class="card card-custom p-4 mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle m-0">
            <thead class="table-light">
                <tr>
                    <th>Report ID</th>
                    <th>Item Title & Description</th>
                    <th>Category</th>
                    <th>Date Lost</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lostReports as $report)
                <tr>
                    <td><strong>#LST-{{ $report->id }}</strong></td>
                    <td>
                        <div class="fw-bold text-dark">{{ $report->title }}</div>
                        <small class="text-muted">{{ Str::limit($report->description, 60) }}</small>
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ $report->category->name }}</span></td>
                    <td>{{ $report->date_lost->format('M d, Y') }}</td>
                    <td><i class="bi bi-geo-alt text-danger me-1"></i> {{ $report->location }}</td>
                    <td>
                        @if($report->status === 'open')
                            <span class="badge bg-danger">Open Search</span>
                        @elseif($report->status === 'claim_pending')
                            <span class="badge bg-warning text-dark">Claim Pending</span>
                        @elseif($report->status === 'resolved')
                            <span class="badge bg-success">Resolved</span>
                        @endif
                    </td>
                    <td>
                        @if($report->status === 'open')
                            <form action="{{ route('student.lost-reports.resolve', $report->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-outline-success">Mark Resolved</button>
                            </form>
                        @else
                            <span class="text-muted fs-7">N/A</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        You have not submitted any lost item reports yet. Click "Report New Lost Item" above to report one.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

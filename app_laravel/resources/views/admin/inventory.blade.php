@extends('layouts.app')

@section('title', 'SAO Storage Inventory Directory | SAO Admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 gap-sm-3">
        <div>
            <h4 class="fw-bold m-0" style="color: var(--primary-color);"><i class="bi bi-archive-fill me-2"></i> SAO Storage
                Inventory Directory</h4>
            <span class="text-muted fs-7">Register surrendered found items, assign cabinet storage locations, and update
                statuses.</span>
        </div>
        <button class="btn btn-primary-custom btn-sm px-3 py-2 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#reportFoundModal">
            <i class="bi bi-box-arrow-in-down me-1"></i> Add Found Item
        </button>
    </div>

    <div class="card card-custom p-4 mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead class="table-light">
                    <tr>
                        <th>Item ID</th>
                        <th>Title & Description</th>
                        <th>Category</th>
                        <th>Date Found & Location</th>
                        <th>Storage Cabinet / Safe</th>
                        <th>Status</th>
                        <th>Update Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventory as $item)
                        <tr>
                            <td><strong>#FND-{{ $item->id }}</strong></td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->title }}</div>
                                <small class="text-muted">{{ Str::limit($item->description, 60) }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $item->category->name }}</span></td>
                            <td>
                                <div>{{ $item->date_found->format('M d, Y') }}</div>
                                <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $item->location }}</small>
                            </td>
                            <td><span class="badge bg-info text-dark"><i class="bi bi-box-seam"></i>
                                    {{ $item->storage_location }}</span></td>
                            <td>
                                @if ($item->status === 'available')
                                    <span class="badge bg-success">Available</span>
                                @elseif($item->status === 'claim_pending')
                                    <span class="badge bg-warning text-dark">Claim Pending</span>
                                @elseif($item->status === 'ready_for_pickup')
                                    <span class="badge bg-info text-dark"><i class="bi bi-box-seam me-1"></i> Ready for Pick-up</span>
                                @elseif($item->status === 'claimed')
                                    <span class="badge bg-secondary"><i class="bi bi-check2-all me-1"></i> Claimed</span>
                                @elseif($item->status === 'disposed')
                                    <span class="badge bg-dark">Disposed</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.inventory.update', $item->id) }}" method="POST"
                                    class="d-flex gap-1">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="available" {{ $item->status === 'available' ? 'selected' : '' }}>
                                            Available</option>
                                        <option value="claim_pending"
                                            {{ $item->status === 'claim_pending' ? 'selected' : '' }}>Claim Pending</option>
                                        <option value="ready_for_pickup"
                                            {{ $item->status === 'ready_for_pickup' ? 'selected' : '' }}>Ready for Pick-up</option>
                                        <option value="claimed" {{ $item->status === 'claimed' ? 'selected' : '' }}>Claimed
                                        </option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No found items registered in inventory
                                yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'SAO Storage Inventory Directory | SAO Admin')

@section('content')
    @include('partials.loading_overlay')

    @php
        $totalCount = $inventory->count();
        $availableCount = $inventory->where('status', 'available')->count();
        $claimPendingCount = $inventory->where('status', 'claim_pending')->count();
        $readyPickupCount = $inventory->where('status', 'ready_for_pickup')->count();
        $claimedCount = $inventory->where('status', 'claimed')->count();
    @endphp

    <!-- Page Header (Image 2 style) -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 gap-sm-3">
        <div>
            <h3 class="fw-bold m-0" style="color: var(--primary-color);">SAO Storage Inventory Directory</h3>
            <p class="text-muted fs-7 mb-0 mt-1">Register surrendered found items, assign cabinet storage locations, and
                update statuses.</p>
        </div>
        <button class="btn btn-primary-custom btn-sm px-3 py-2 shadow-sm fw-bold" data-bs-toggle="modal"
            data-bs-target="#reportFoundModal">
            <i class="bi bi-box-arrow-in-down me-1"></i> Add Found Item
        </button>
    </div>

    <!-- Mini Metric Cards (Image 2 style) -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="mini-stat-card">
                <div class="mini-stat-icon" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
                    <i class="bi bi-archive-fill"></i>
                </div>
                <div>
                    <div class="mini-stat-label">Total Inventory</div>
                    <div class="mini-stat-value">{{ $totalCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="mini-stat-card">
                <div class="mini-stat-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="mini-stat-label">Available for Claim</div>
                    <div class="mini-stat-value text-success">{{ $availableCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="mini-stat-card">
                <div class="mini-stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <div class="mini-stat-label">Pending Claim Verification</div>
                    <div class="mini-stat-value text-warning">{{ $claimPendingCount }}</div>
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
    </div>

    <!-- Inventory Table Card (Image 2 style) -->
    <div class="card card-custom p-4 mb-4 shadow-sm border-0" style="background: #ffffff; border-radius: 12px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-archive text-danger fs-5"></i>
                <h5 class="fw-bold m-0 text-dark">Stored Items & Status Control</h5>
            </div>
            <span class="badge bg-light text-secondary border px-3 py-1 fw-semibold">
                <i class="bi bi-building me-1"></i> SAO Storage Facilities
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead>
                    <tr style="font-size: 0.8rem; color: #1e293b; border-bottom: 2px solid #f1f5f9;">
                        <th class="py-3 px-3">Ref No.</th>
                        <th class="py-3 px-3">Item Details</th>
                        <th class="py-3 px-3">Category</th>
                        <th class="py-3 px-3">Date Found & Location</th>
                        <th class="py-3 px-3">Storage Cabinet / Room</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3 text-center" style="min-width: 170px;">Manage Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventory as $item)
                        <tr style="font-size: 0.82rem; border-bottom: 1px solid #f8fafc;">
                            <td class="py-3 px-3 fw-bold" style="color: #691220;">
                                FND-{{ $item->created_at ? $item->created_at->format('Ymd') : '20260914' }}-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="py-3 px-3">
                                <div class="d-flex align-items-center gap-2">
                                    @if ($item->image_path)
                                        <img src="{{ $item->image_path }}" class="rounded border" width="42"
                                            height="42" style="object-fit: cover;" alt="{{ $item->title }}">
                                    @else
                                        <div class="rounded border bg-light d-flex align-items-center justify-content-center text-muted"
                                            style="width: 42px; height: 42px;">
                                            <i class="bi bi-box-seam text-secondary"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark">{{ $item->title }}</div>
                                        <small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <span class="badge bg-light text-secondary border px-2 py-1">
                                    {{ $item->category->name ?? 'General' }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-muted">
                                <div>{{ $item->date_found ? $item->date_found->format('M d, Y') : 'N/A' }}</div>
                                <small class="text-muted"><i class="bi bi-geo-alt text-danger me-1"></i>
                                    {{ $item->location }}</small>
                            </td>
                            <td class="py-3 px-3">
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <i class="bi bi-building me-1 text-info"></i>
                                    {{ $item->storage_location ?: 'SAO Storage' }}
                                </span>
                            </td>
                            <td class="py-3 px-3">
                                @if ($item->status === 'available')
                                    <span
                                        class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-1 fw-semibold">
                                        &check; Available
                                    </span>
                                @elseif($item->status === 'claim_pending')
                                    <span
                                        class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 fw-semibold">
                                        &bull; Claim Pending
                                    </span>
                                @elseif($item->status === 'ready_for_pickup')
                                    <span
                                        class="badge rounded-pill bg-info-subtle text-info border border-info-subtle px-3 py-1 fw-semibold">
                                        <i class="bi bi-box-seam me-1"></i> Ready for Pick-up
                                    </span>
                                @elseif($item->status === 'claimed')
                                    <span
                                        class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-1 fw-semibold">
                                        <i class="bi bi-check2-all me-1"></i> Claimed
                                    </span>
                                @elseif($item->status === 'disposed')
                                    <span class="badge rounded-pill bg-dark text-white px-3 py-1 fw-semibold">
                                        Disposed
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                <form action="{{ route('admin.inventory.destroy', $item->id) }}" method="POST"
                                    data-confirm="Are you sure you want to delete this inventory item? This action cannot be undone."
                                    class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger px-2 py-1"
                                        title="Delete Item">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                <h6 class="fw-bold text-dark mb-1">No Found Items Registered Yet</h6>
                                <p class="fs-7 text-muted mb-3">Click below to record a newly surrendered item to the SAO
                                    storage.</p>
                                <button class="btn btn-sm btn-primary-custom px-4 fw-bold" data-bs-toggle="modal"
                                    data-bs-target="#reportFoundModal">
                                    <i class="bi bi-plus-circle-fill me-1"></i> Register New Found Item
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

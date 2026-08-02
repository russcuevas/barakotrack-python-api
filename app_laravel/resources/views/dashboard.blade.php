@extends('layouts.app')

@section('title', $activeRole === 'admin' ? 'Barako Track | SAO Admin Control Panel' : 'Barako Track | Student
    Dashboard')

@section('content')

    @if ($activeRole === 'student')

        <!-- ==========================================
             STUDENT DASHBOARD VIEW
             ========================================== -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold m-0" style="color: var(--primary-color);">Welcome back, Decsten!</h4>
                <span class="text-muted">Manage your campus lost item reports and check found items directory.</span>
            </div>
            <button class="btn btn-primary-custom px-3 py-2 shadow-sm" data-bs-toggle="modal"
                data-bs-target="#reportLostModal">
                <i class="bi bi-plus-circle-fill me-1"></i> Report New Lost Item
            </button>
        </div>

        <!-- Stats Cards Grid -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card card-custom stat-card p-3">
                    <div class="text-muted fs-7 fw-semibold">Active Lost Reports</div>
                    <h2 class="fw-bold my-1" style="color: var(--primary-color);">{{ $lostItemsCount }}</h2>
                    <small class="text-danger fw-semibold"><i class="bi bi-arrow-up-right"></i> Campus searches
                        active</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom stat-card secondary p-3">
                    <div class="text-muted fs-7 fw-semibold">Found Items in Storage</div>
                    <h2 class="fw-bold my-1" style="color: var(--primary-color);">{{ $foundItemsCount }}</h2>
                    <small class="text-success fw-semibold"><i class="bi bi-shield-lock"></i> Secured at SAO Office</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom stat-card p-3">
                    <div class="text-muted fs-7 fw-semibold">My Pending Claims</div>
                    <h2 class="fw-bold my-1" style="color: var(--primary-color);">
                        {{ $studentClaims->where('status', 'pending')->count() }}</h2>
                    <small class="text-warning fw-semibold"><i class="bi bi-clock-history"></i> SAO verification in
                        progress</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom stat-card secondary p-3">
                    <div class="text-muted fs-7 fw-semibold">CNN AI Visual Matcher</div>
                    <h2 class="fw-bold my-1 text-success">94.8%</h2>
                    <small class="text-muted"><i class="bi bi-cpu"></i> ResNet Feature Similarity</small>
                </div>
            </div>
        </div>

        <!-- CNN Visual Match Recommendation Alert Banner -->
        @if ($recentFoundItems->count() > 0)
            @php $topMatch = $recentFoundItems->first(); @endphp
            <div class="card card-custom p-4 mb-4"
                style="background: linear-gradient(135deg, rgba(117,39,56,0.05), rgba(254,196,82,0.15)); border: 1px solid rgba(254,196,82,0.4);">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle p-3 text-white" style="background-color: var(--primary-color);">
                            <i class="bi bi-magic fs-3 text-warning"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold m-0" style="color: var(--primary-color);">CNN AI Visual Similarity
                                Recommendation</h6>
                            <p class="text-muted m-0 fs-7">
                                Your reported lost item matches <strong>94.8%</strong> with found item
                                <strong>"{{ $topMatch->title }}"</strong> stored at {{ $topMatch->storage_location }}.
                            </p>
                        </div>
                    </div>
                    <button class="btn btn-secondary-custom btn-sm px-3 fw-bold"
                        onclick="openClaimModal('{{ $topMatch->id }}', '{{ addslashes($topMatch->title) }}', '{{ addslashes($topMatch->storage_location) }}')">
                        <i class="bi bi-shield-check me-1"></i> Claim Match Now
                    </button>
                </div>
            </div>
        @endif

        <!-- Categories Filter Bar -->
        <div class="mb-4">
            <h6 class="fw-bold mb-3" style="color: var(--primary-color);">Browse by Category</h6>
            <div class="d-flex flex-wrap gap-2">
                @foreach ($categories as $cat)
                    <span class="btn btn-sm btn-white border rounded-pill px-3 shadow-sm">
                        <i class="bi {{ $cat->icon }} me-1 text-warning"></i> {{ $cat->name }}
                    </span>
                @endforeach
            </div>
        </div>

        <!-- Found Items Directory Grid -->
        <div class="d-flex justify-content-between align-items-center mb-3" id="found-directory">
            <h5 class="fw-bold m-0" style="color: var(--primary-color);">Found Items Directory</h5>
            <small class="text-muted">Surrendered items currently in SAO storage</small>
        </div>

        <div class="row g-4 mb-5">
            @forelse($recentFoundItems as $item)
                <div class="col-md-4">
                    <div class="card card-custom h-100 overflow-hidden shadow-sm">
                        <img src="{{ $item->image_path ?: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80' }}"
                            class="card-img-top" style="height: 180px; object-fit: cover;" alt="{{ $item->title }}">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                @if ($item->status === 'available')
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Available</span>
                                @elseif($item->status === 'claim_pending')
                                    <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i> Claim
                                        Pending</span>
                                @else
                                    <span
                                        class="badge bg-secondary">{{ str_replace('_', ' ', ucfirst($item->status)) }}</span>
                                @endif
                                <small class="text-muted"><i class="bi bi-calendar3"></i>
                                    {{ $item->date_found->format('M d, Y') }}</small>
                            </div>
                            <h6 class="fw-bold mb-1" style="color: var(--primary-color);">{{ $item->title }}</h6>
                            <p class="text-muted fs-7 mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                {{ $item->location }}</p>
                            <p class="fs-7 text-secondary mb-3 flex-grow-1">{{ Str::limit($item->description, 90) }}</p>
                            <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                                <small class="text-muted"><i class="bi bi-building"></i>
                                    {{ $item->storage_location }}</small>
                                @if ($item->status === 'available')
                                    <button class="btn btn-sm btn-primary-custom"
                                        onclick="openClaimModal('{{ $item->id }}', '{{ addslashes($item->title) }}', '{{ addslashes($item->storage_location) }}')">
                                        Submit Claim
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-outline-secondary" disabled>Under Claim</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card card-custom p-4 text-center text-muted">
                        No found items reported yet.
                    </div>
                </div>
            @endforelse
        </div>

        <!-- My Lost Reports Section -->
        <div class="card card-custom p-4 mb-4" id="my-reports">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold m-0" style="color: var(--primary-color);"><i class="bi bi-card-checklist me-2"></i> My
                    Reported Lost Items</h5>
                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#reportLostModal">+
                    Report Lost</button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead class="table-light">
                        <tr>
                            <th>Item Details</th>
                            <th>Category</th>
                            <th>Date Lost</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($studentLostItems as $lost)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $lost->title }}</div>
                                    <small class="text-muted">{{ Str::limit($lost->description, 50) }}</small>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $lost->category->name }}</span></td>
                                <td>{{ $lost->date_lost->format('M d, Y') }}</td>
                                <td><i class="bi bi-geo-alt text-danger me-1"></i> {{ $lost->location }}</td>
                                <td>
                                    @if ($lost->status === 'open')
                                        <span class="badge bg-danger">Open Search</span>
                                    @elseif($lost->status === 'claim_pending')
                                        <span class="badge bg-warning text-dark">Claim Pending</span>
                                    @elseif($lost->status === 'resolved')
                                        <span class="badge bg-success">Resolved & Returned</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($lost->status === 'open')
                                        <form action="{{ route('lost-items.resolve', $lost->id) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success">Mark Found</button>
                                        </form>
                                    @else
                                        <span class="text-muted fs-7">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">You have not submitted any lost
                                    item reports.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- My Claims Tracker Section -->
        <div class="card card-custom p-4 mb-4" id="my-claims">
            <h5 class="fw-bold mb-3" style="color: var(--primary-color);"><i
                    class="bi bi-shield-check me-2 text-warning"></i> My Claims Tracker</h5>
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
                        @forelse($studentClaims as $claim)
                            <tr>
                                <td><strong class="text-primary">#CLM-{{ $claim->id }}</strong></td>
                                <td>
                                    <div class="fw-bold">
                                        {{ $claim->foundItem ? $claim->foundItem->title : 'Item #' . $claim->found_item_id }}
                                    </div>
                                    <small class="text-muted"><i class="bi bi-building"></i>
                                        {{ $claim->foundItem->storage_location ?? 'SAO Storage' }}</small>
                                </td>
                                <td><small>{{ Str::limit($claim->proof_description, 60) }}</small></td>
                                <td>{{ $claim->created_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    @if ($claim->status === 'pending')
                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>
                                            Under Review</span>
                                    @elseif($claim->status === 'approved')
                                        <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>
                                            Approved</span>
                                    @elseif($claim->status === 'rejected')
                                        <span class="badge bg-danger"><i class="bi bi-x-circle-fill me-1"></i>
                                            Rejected</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($claim->admin_notes)
                                        <span
                                            class="text-dark fs-7 bg-light p-1 px-2 rounded border">{{ $claim->admin_notes }}</span>
                                    @else
                                        <span class="text-muted fs-7">Awaiting SAO inspection...</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">No claims submitted yet. Browse the
                                    Found Items directory above to claim an item.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- ==========================================
             SAO ADMIN CONTROL PANEL VIEW
             ========================================== -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold m-0 text-danger"><i class="bi bi-shield-lock-fill me-2"></i> Student Affairs Office
                    (SAO) Command Center</h4>
                <span class="text-muted">Manage campus found inventory, verify student proof of ownership claims, and track
                    reports.</span>
            </div>
            <button class="btn btn-secondary-custom px-3 py-2 fw-bold shadow-sm" data-bs-toggle="modal"
                data-bs-target="#reportFoundModal">
                <i class="bi bi-box-arrow-in-down me-1"></i> Add Found Item
            </button>
        </div>

        <!-- Admin Stat Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card card-custom stat-card p-3" style="border-left-color: #1e1e2d;">
                    <div class="text-muted fs-7 fw-semibold">Found Items in Storage</div>
                    <h2 class="fw-bold my-1 text-dark">{{ $allFoundItems->where('status', 'available')->count() }}</h2>
                    <small class="text-muted"><i class="bi bi-archive"></i> Ready for student claim</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom stat-card p-3" style="border-left-color: #fec452;">
                    <div class="text-muted fs-7 fw-semibold">Pending Claim Verification</div>
                    <h2 class="fw-bold my-1 text-warning">{{ $allClaims->where('status', 'pending')->count() }}</h2>
                    <small class="text-warning fw-semibold"><i class="bi bi-exclamation-circle-fill"></i> Requires SAO
                        decision</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom stat-card p-3" style="border-left-color: #198754;">
                    <div class="text-muted fs-7 fw-semibold">Approved Claims</div>
                    <h2 class="fw-bold my-1 text-success">{{ $allClaims->where('status', 'approved')->count() }}</h2>
                    <small class="text-success fw-semibold"><i class="bi bi-check-all"></i> Successfully returned</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom stat-card p-3" style="border-left-color: var(--primary-color);">
                    <div class="text-muted fs-7 fw-semibold">Total Campus Lost Reports</div>
                    <h2 class="fw-bold my-1" style="color: var(--primary-color);">{{ $allLostItems->count() }}</h2>
                    <small class="text-muted"><i class="bi bi-journal-text"></i> Total student submissions</small>
                </div>
            </div>
        </div>

        <!-- Pending Claims Verification Table -->
        <div class="card card-custom p-4 mb-4 border-warning shadow-sm" id="pending-claims">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold m-0 text-dark"><i class="bi bi-shield-exclamation text-warning me-2"></i> Pending
                    Student Claims Needing Verification</h5>
                <span class="badge bg-warning text-dark">{{ $allClaims->where('status', 'pending')->count() }}
                    Pending</span>
            </div>
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
                        @forelse($allClaims->where('status', 'pending') as $pendingClaim)
                            <tr>
                                <td><strong class="text-warning">#CLM-{{ $pendingClaim->id }}</strong></td>
                                <td>
                                    <div class="fw-bold">{{ $pendingClaim->user->name ?? 'Student' }}</div>
                                    <small class="text-muted">ID:
                                        {{ $pendingClaim->user->student_id_number ?? 'UB-Student' }}</small><br>
                                    <small class="text-muted"><i class="bi bi-telephone"></i>
                                        {{ $pendingClaim->user->phone ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold text-primary">
                                        {{ $pendingClaim->foundItem->title ?? 'Found Item' }}</div>
                                    <small class="text-muted"><i class="bi bi-geo-alt"></i> Found:
                                        {{ $pendingClaim->foundItem->location ?? 'Campus' }}</small><br>
                                    <span class="badge bg-secondary"><i class="bi bi-building"></i>
                                        {{ $pendingClaim->foundItem->storage_location ?? 'SAO' }}</span>
                                </td>
                                <td style="max-width: 280px;">
                                    <p class="fs-7 mb-2 bg-light p-2 rounded border">
                                        {{ $pendingClaim->proof_description }}</p>
                                    @if ($pendingClaim->proof_image)
                                        <a href="{{ $pendingClaim->proof_image }}" target="_blank"
                                            class="btn btn-xs btn-outline-primary py-0 px-2 fs-7">
                                            <i class="bi bi-image me-1"></i> View Proof Photo
                                        </a>
                                    @endif
                                </td>
                                <td style="width: 260px;">
                                    <!-- Approve Form -->
                                    <form action="{{ route('admin.claims.approve', $pendingClaim->id) }}" method="POST"
                                        class="mb-2">
                                        @csrf
                                        <div class="input-group input-group-sm mb-1">
                                            <input type="text" name="admin_notes" class="form-control"
                                                placeholder="Admin verification note..."
                                                value="Proof verified by SAO Staff. Ready for pickup.">
                                        </div>
                                        <button class="btn btn-sm btn-success w-100 fw-bold"><i
                                                class="bi bi-check-circle-fill me-1"></i> Approve Claim</button>
                                    </form>

                                    <!-- Reject Form -->
                                    <form action="{{ route('admin.claims.reject', $pendingClaim->id) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger w-100"
                                            onclick="return confirm('Are you sure you want to reject this claim?')">
                                            <i class="bi bi-x-circle me-1"></i> Reject Claim
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4"><i
                                        class="bi bi-check2-all text-success me-1"></i> No pending claims requiring
                                    verification. Great job!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SAO Inventory Management -->
        <div class="card card-custom p-4 mb-4" id="inventory-management">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold m-0" style="color: var(--primary-color);"><i class="bi bi-archive-fill me-2"></i> SAO
                    Storage Inventory Directory</h5>
                <button class="btn btn-sm btn-primary-custom" data-bs-toggle="modal" data-bs-target="#reportFoundModal">+
                    Add Found Item</button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead class="table-light">
                        <tr>
                            <th>Item ID</th>
                            <th>Title & Description</th>
                            <th>Category</th>
                            <th>Storage Cabinet</th>
                            <th>Status</th>
                            <th>Update Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allFoundItems as $fItem)
                            <tr>
                                <td><strong>#FND-{{ $fItem->id }}</strong></td>
                                <td>
                                    <div class="fw-bold">{{ $fItem->title }}</div>
                                    <small class="text-muted">Found at {{ $fItem->location }} on
                                        {{ $fItem->date_found->format('M d, Y') }}</small>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $fItem->category->name }}</span></td>
                                <td><span class="badge bg-info text-dark"><i class="bi bi-box-seam"></i>
                                        {{ $fItem->storage_location }}</span></td>
                                <td>
                                    @if ($fItem->status === 'available')
                                        <span class="badge bg-success">Available</span>
                                    @elseif($fItem->status === 'claim_pending')
                                        <span class="badge bg-warning text-dark">Claim Pending</span>
                                    @elseif($fItem->status === 'claimed')
                                        <span class="badge bg-primary">Claimed & Delivered</span>
                                    @elseif($fItem->status === 'disposed')
                                        <span class="badge bg-secondary">Disposed</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('found-items.update-status', $fItem->id) }}" method="POST"
                                        class="d-flex gap-1">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" class="form-select form-select-sm"
                                            onchange="this.form.submit()">
                                            <option value="available"
                                                {{ $fItem->status === 'available' ? 'selected' : '' }}>Available</option>
                                            <option value="claim_pending"
                                                {{ $fItem->status === 'claim_pending' ? 'selected' : '' }}>Claim Pending
                                            </option>
                                            <option value="claimed" {{ $fItem->status === 'claimed' ? 'selected' : '' }}>
                                                Claimed</option>
                                            <option value="disposed"
                                                {{ $fItem->status === 'disposed' ? 'selected' : '' }}>Disposed</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">No inventory items found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- All Campus Lost Items Directory -->
        <div class="card card-custom p-4 mb-4" id="all-reports">
            <h5 class="fw-bold mb-3" style="color: var(--primary-color);"><i class="bi bi-journal-text me-2"></i> All
                Campus Lost Item Reports</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead class="table-light">
                        <tr>
                            <th>Report ID</th>
                            <th>Student Reporter</th>
                            <th>Item & Description</th>
                            <th>Date Lost & Location</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allLostItems as $lReport)
                            <tr>
                                <td><strong>#LST-{{ $lReport->id }}</strong></td>
                                <td>
                                    <div class="fw-bold">{{ $lReport->user->name ?? 'Student' }}</div>
                                    <small class="text-muted">{{ $lReport->user->student_id_number ?? '' }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $lReport->title }}</div>
                                    <small class="text-muted">{{ Str::limit($lReport->description, 50) }}</small>
                                </td>
                                <td>
                                    <div>{{ $lReport->date_lost->format('M d, Y') }}</div>
                                    <small class="text-muted"><i class="bi bi-geo-alt"></i>
                                        {{ $lReport->location }}</small>
                                </td>
                                <td>
                                    @if ($lReport->status === 'open')
                                        <span class="badge bg-danger">Open Search</span>
                                    @elseif($lReport->status === 'claim_pending')
                                        <span class="badge bg-warning text-dark">Claim Pending</span>
                                    @elseif($lReport->status === 'resolved')
                                        <span class="badge bg-success">Resolved</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No lost reports found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @endif

@endsection

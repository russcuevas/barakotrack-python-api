@extends('layouts.app')

@section('title', 'Barako Track | Student Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0" style="color: var(--primary-color);">Welcome back, {{ $user->name ?? 'Student' }}!</h4>
            <span class="text-muted">Manage your campus lost item reports and check found items directory.</span>
        </div>
        <button class="btn btn-primary-custom px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#reportLostModal">
            <i class="bi bi-plus-circle-fill me-1"></i> Report New Lost Item
        </button>
    </div>

    <!-- Stats Cards Grid -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card card-custom stat-card p-3">
                <div class="text-muted fs-7 fw-semibold">Active Lost Reports</div>
                <h2 class="fw-bold my-1" style="color: var(--primary-color);">{{ $lostItemsCount }}</h2>
                <small class="text-danger fw-semibold"><i class="bi bi-arrow-up-right"></i> Campus searches active</small>
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
                <h2 class="fw-bold my-1" style="color: var(--primary-color);">{{ $pendingClaimsCount }}</h2>
                <small class="text-warning fw-semibold"><i class="bi bi-clock-history"></i> SAO verification in progress</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom stat-card secondary p-3">
                <div class="text-muted fs-7 fw-semibold">CNN AI Matches Found</div>
                <h2 class="fw-bold my-1 text-success">{{ $aiMatchesCount }}</h2>
                <small class="text-muted"><i class="bi bi-cpu"></i> For your reported lost items</small>
            </div>
        </div>
    </div>

    <!-- CNN Visual Match Recommendations Table (Only shown if student's items have matches >45%) -->
    @if ($aiMatches->count() > 0)
        <div class="card card-custom p-4 mb-4 border-warning shadow-sm" style="background: linear-gradient(135deg, rgba(117,39,56,0.02), rgba(254,196,82,0.08));">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-magic fs-4 text-warning"></i>
                    <h5 class="fw-bold m-0" style="color: var(--primary-color);">CNN AI Visual Similarity Recommendations ({{ $aiMatches->count() }})</h5>
                </div>
                <a href="{{ route('student.matcher') }}" class="btn btn-sm btn-outline-warning text-dark fw-bold">View Full Matcher <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle m-0 bg-white rounded shadow-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Your Reported Lost Item</th>
                            <th>Top Matched Found Item</th>
                            <th>SAO Storage Location</th>
                            <th>AI Visual Match Score</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($aiMatches as $match)
                            @php
                                $lost = $match['lost_item'];
                                $found = $match['found_item'];
                                $score = $match['score'];
                                $badgeColor = $score >= 85 ? 'bg-success' : 'bg-warning text-dark';
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $lost->title }}</div>
                                    <small class="text-muted"><i class="bi bi-geo-alt text-danger me-1"></i> Lost: {{ $lost->location }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $found->image_path ?: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80' }}" class="rounded" width="40" height="40" style="object-fit: cover;" alt="{{ $found->title }}">
                                        <div>
                                            <div class="fw-bold text-primary">{{ $found->title }}</div>
                                            <small class="text-muted"><i class="bi bi-calendar3"></i> Found: {{ $found->date_found->format('M d, Y') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark"><i class="bi bi-building"></i> {{ $found->storage_location }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $badgeColor }} p-2 fw-bold"><i class="bi bi-cpu-fill me-1"></i> {{ $score }}% Match</span>
                                </td>
                                <td>
                                @if ($found->status === 'available')
                                    <button class="btn btn-sm btn-secondary-custom fw-bold px-3"
                                        onclick="openClaimModal('{{ $found->id }}', '{{ addslashes($found->title) }}', '{{ addslashes($found->storage_location) }}', '{{ $lost->id }}')">
                                        <i class="bi bi-shield-check me-1"></i> Claim Match
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-outline-secondary fw-bold px-3" disabled>
                                        <i class="bi bi-clock me-1"></i> Under Claim
                                    </button>
                                @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Quick Found Items Preview -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold m-0" style="color: var(--primary-color);">Recent Found Items</h5>
        <a href="{{ route('student.found-items') }}" class="text-decoration-none fw-semibold fs-7">View Full Directory <i
                class="bi bi-chevron-right"></i></a>
    </div>

    <div class="row g-4 mb-4">
        @foreach ($recentFoundItems as $item)
            <div class="col-md-4">
                <div class="card card-custom h-100 overflow-hidden shadow-sm">
                    <img src="{{ $item->image_path ?: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80' }}"
                        class="card-img-top" style="height: 160px; object-fit: cover;" alt="{{ $item->title }}">
                    <div class="card-body">
                        @if ($item->status === 'available')
                            <span class="badge bg-success mb-2"><i class="bi bi-check-circle me-1"></i> Available</span>
                        @elseif ($item->status === 'claim_pending')
                            <span class="badge bg-warning text-dark mb-2"><i class="bi bi-clock me-1"></i> Claim Pending</span>
                        @else
                            <span class="badge bg-secondary mb-2">{{ str_replace('_', ' ', ucfirst($item->status)) }}</span>
                        @endif
                        <h6 class="fw-bold mb-1" style="color: var(--primary-color);">{{ $item->title }}</h6>
                        <p class="text-muted fs-7 mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i>
                            {{ $item->location }}</p>
                        @if ($item->status === 'available')
                            <button class="btn btn-sm btn-primary-custom w-100 mt-2"
                                onclick="openClaimModal('{{ $item->id }}', '{{ addslashes($item->title) }}', '{{ addslashes($item->storage_location) }}')">
                                Submit Claim
                            </button>
                        @else
                            <button class="btn btn-sm btn-outline-secondary w-100 mt-2" disabled>
                                <i class="bi bi-clock me-1"></i> Under Claim
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Barako Track | Student Dashboard')

@section('content')
    @include('partials.loading_overlay')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 gap-sm-3">
        <div>
            <h4 class="fw-bold m-0" style="color: var(--primary-color);">Welcome back, {{ $user->name ?? 'Student' }}!</h4>
            <span class="text-muted fs-7">Manage your campus lost item reports and check found items directory.</span>
        </div>
        <button class="btn btn-primary-custom btn-sm px-3 py-2 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#reportLostModal">
            <i class="bi bi-plus-circle-fill me-1"></i> Report New Lost Item
        </button>
    </div>

    <!-- Stats Cards Grid -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card card-custom stat-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted fs-7 fw-semibold">Active Lost Reports</div>
                        <h2 class="fw-bold my-1" style="color: var(--primary-color);">{{ $lostItemsCount }}</h2>
                        <small class="text-danger fw-semibold"><i class="bi bi-arrow-up-right me-1"></i>Active searches</small>
                    </div>
                    <div class="stat-icon-wrapper" style="background: rgba(220, 38, 38, 0.1); color: #dc2626;">
                        <i class="bi bi-search"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card card-custom stat-card secondary p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted fs-7 fw-semibold">Found Items in Storage</div>
                        <h2 class="fw-bold my-1" style="color: var(--primary-color);">{{ $foundItemsCount }}</h2>
                        <small class="text-success fw-semibold"><i class="bi bi-shield-check me-1"></i>Secured at SAO</small>
                    </div>
                    <div class="stat-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card card-custom stat-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted fs-7 fw-semibold">My Pending Claims</div>
                        <h2 class="fw-bold my-1" style="color: var(--primary-color);">{{ $pendingClaimsCount }}</h2>
                        <small class="text-warning fw-semibold"><i class="bi bi-clock-history me-1"></i>Under SAO review</small>
                    </div>
                    <div class="stat-icon-wrapper" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <i class="bi bi-file-earmark-check-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card card-custom stat-card secondary p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted fs-7 fw-semibold">CNN AI Matches Found</div>
                        <h2 class="fw-bold my-1 text-success">{{ $aiMatchesCount }}</h2>
                        <small class="text-muted fw-semibold"><i class="bi bi-cpu me-1"></i>MobileNetV2 scans</small>
                    </div>
                    <div class="stat-icon-wrapper" style="background: rgba(117, 39, 56, 0.1); color: var(--primary-color);">
                        <i class="bi bi-stars"></i>
                    </div>
                </div>
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
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($lost->image_path)
                                            <img src="{{ $lost->image_path }}" class="rounded" width="40" height="40" style="object-fit: cover;" alt="{{ $lost->title }}">
                                        @endif
                                        <div>
                                            <div class="fw-bold text-dark">{{ $lost->title }}</div>
                                            <small class="text-muted"><i class="bi bi-geo-alt text-danger me-1"></i> Lost: {{ $lost->location }}</small>
                                        </div>
                                    </div>
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
        <h5 class="fw-bold m-0" style="color: var(--primary-color);">
            <i class="bi bi-box-seam text-warning me-1"></i> Recent Found Items in Storage
        </h5>
        <a href="{{ route('student.found-items') }}" class="text-decoration-none fw-semibold fs-7">
            View Full Directory <i class="bi bi-chevron-right"></i>
        </a>
    </div>

    <div class="row g-4 mb-4">
        @forelse ($recentFoundItems as $item)
            <div class="col-md-4">
                <div class="card card-custom h-100 overflow-hidden shadow-sm border-0 position-relative">
                    <div class="position-relative overflow-hidden" style="height: 180px; background-color: #1e1e2d;">
                        <img src="{{ $item->image_path ?: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80' }}"
                            class="w-100 h-100" style="object-fit: cover; transition: transform 0.3s ease;" alt="{{ $item->title }}"
                            onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        <div class="position-absolute top-0 start-0 m-2">
                            @if ($item->status === 'available')
                                <span class="badge bg-success shadow-sm"><i class="bi bi-check-circle me-1"></i> Available</span>
                            @elseif ($item->status === 'claim_pending')
                                <span class="badge bg-warning text-dark shadow-sm"><i class="bi bi-clock me-1"></i> Claim Pending</span>
                            @else
                                <span class="badge bg-secondary shadow-sm">{{ str_replace('_', ' ', ucfirst($item->status)) }}</span>
                            @endif
                        </div>
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-dark bg-opacity-75 text-white shadow-sm">
                                <i class="bi bi-calendar3 me-1"></i> {{ $item->date_found ? $item->date_found->format('M d') : 'Recent' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-1" style="color: var(--primary-color);">{{ $item->title }}</h6>
                            <p class="text-muted fs-7 mb-2">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $item->location }}
                            </p>
                            <small class="text-muted d-block mb-3">
                                <i class="bi bi-building me-1"></i> Storage: <strong>{{ $item->storage_location ?: 'SAO Storage' }}</strong>
                            </small>
                        </div>
                        @if ($item->status === 'available')
                            <button class="btn btn-sm btn-primary-custom w-100 fw-bold"
                                onclick="openClaimModal('{{ $item->id }}', '{{ addslashes($item->title) }}', '{{ addslashes($item->storage_location) }}')">
                                <i class="bi bi-shield-check me-1"></i> Submit Claim
                            </button>
                        @else
                            <button class="btn btn-sm btn-outline-secondary w-100" disabled>
                                <i class="bi bi-clock me-1"></i> Under Claim Review
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card card-custom p-4 text-center text-muted shadow-sm border-0">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-warning"></i>
                    <h6 class="fw-bold text-dark mb-1">No Surrendered Found Items Yet</h6>
                    <small>Items surrendered to the Student Affairs Office (SAO) will appear here for student claims.</small>
                </div>
            </div>
        @endforelse
    </div>
@endsection

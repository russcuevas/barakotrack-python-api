@extends('layouts.app')

@section('title', 'CNN AI Visual Matcher | Barako Track')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold m-0" style="color: var(--primary-color);"><i class="bi bi-cpu-fill text-warning me-2"></i> CNN
                AI Visual Matcher</h4>
            <span class="text-muted">Automated visual similarity & hybrid AI matching between your reported lost items and
                SAO found inventory.</span>
        </div>
        <button class="btn btn-primary-custom px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#reportLostModal">
            <i class="bi bi-plus-circle-fill me-1"></i> Report New Lost Item
        </button>
    </div>

    <!-- CNN AI Status Header Card -->
    <div class="card card-custom p-4 mb-4"
        style="background: linear-gradient(135deg, #1e1e2d 0%, #3a151f 100%); color: white;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 bg-warning text-dark">
                    <i class="bi bi-cpu-fill fs-2"></i>
                </div>
                <div>
                    <h5 class="fw-bold m-0 text-warning">BarakoTrack CNN Visual Feature</h5>
                    <p class="m-0 fs-7 text-white-50">MobileNetV2</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success p-2 px-3"><i class="bi bi-broadcast me-1"></i> Matcher Engine: Online</span>
            </div>
        </div>
    </div>

    <!-- Match Results Section -->
    @forelse($matches as $matchGroup)
        @php
            $lost = $matchGroup['lost_item'];
            $candidates = $matchGroup['candidate_matches'];
        @endphp

        <div class="card card-custom p-4 mb-4 shadow-sm border-warning">
            <!-- Reported Lost Item Header with Uploaded Image -->
            <div class="d-flex justify-content-between align-items-center pb-3 border-bottom mb-3 flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    @if ($lost->image_path)
                        <img src="{{ $lost->image_path }}" class="rounded border shadow-sm"
                            style="width: 75px; height: 75px; object-fit: cover;" alt="{{ $lost->title }}">
                    @else
                        <div class="rounded border bg-light d-flex flex-column align-items-center justify-content-center text-muted"
                            style="width: 75px; height: 75px;">
                            <i class="bi bi-image fs-3"></i>
                            <small style="font-size: 0.65rem;">No Image</small>
                        </div>
                    @endif
                    <div>
                        <span class="badge bg-danger mb-1"><i class="bi bi-search me-1"></i> Your Lost Report
                            #LST-{{ $lost->id }}</span>
                        <h5 class="fw-bold m-0" style="color: var(--primary-color);">{{ $lost->title }}</h5>
                        <small class="text-muted d-block"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Lost at
                            {{ $lost->location }} on {{ $lost->date_lost->format('M d, Y') }}</small>
                        <small class="text-secondary fs-7">{{ Str::limit($lost->description, 80) }}</small>
                    </div>
                </div>
                <div>
                    <span class="badge bg-light text-dark border p-2"><i class="bi bi-tag me-1"></i>
                        {{ $lost->category->name }}</span>
                </div>
            </div>

            <!-- Candidate Found Items Matches -->
            <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-magic text-warning me-2"></i> Potential Visual Matches Found
                in Inventory ({{ count($candidates) }})</h6>

            <div class="row g-4">
                @forelse($candidates as $candidate)
                    @php
                        $found = $candidate['found_item'];
                        $score = $candidate['score'];
                        $confidence = $candidate['confidence'];
                        $scoreColor =
                            $score >= 85 ? 'bg-success' : ($score >= 65 ? 'bg-warning text-dark' : 'bg-info text-dark');
                    @endphp

                    <div class="col-md-6">
                        <div class="card card-custom h-100 border p-3 style-match-card">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge {{ $scoreColor }} fw-bold p-2"><i class="bi bi-cpu-fill me-1"></i>
                                    {{ $score }}% Visual Match</span>
                                <span class="badge bg-light text-dark border fs-7">{{ $confidence }}</span>
                            </div>

                            <!-- Match Score Progress Bar -->
                            <div class="progress mb-3" style="height: 8px;">
                                <div class="progress-bar {{ $score >= 85 ? 'bg-success' : 'bg-warning' }}"
                                    role="progressbar" style="width: {{ $score }}%;"></div>
                            </div>

                            <div class="d-flex gap-3 align-items-center mb-3">
                                <img src="{{ $found->image_path ?: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80' }}"
                                    class="rounded border" style="width: 80px; height: 80px; object-fit: cover;"
                                    alt="{{ $found->title }}">
                                <div>
                                    <h6 class="fw-bold mb-1" style="color: var(--primary-color);">{{ $found->title }}</h6>
                                    <small class="text-muted d-block"><i class="bi bi-geo-alt text-danger me-1"></i> Found:
                                        {{ $found->location }}</small>
                                    <small class="text-muted d-block"><i class="bi bi-building text-primary me-1"></i>
                                        {{ $found->storage_location }}</small>
                                </div>
                            </div>

                            <p class="fs-7 text-secondary mb-3">{{ Str::limit($found->description, 80) }}</p>

                            <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                                <small class="text-muted"><i class="bi bi-calendar3"></i>
                                    {{ $found->date_found->format('M d, Y') }}</small>
                                @if ($found->status === 'available')
                                    <button class="btn btn-sm btn-secondary-custom fw-bold px-3"
                                        onclick="openClaimModal('{{ $found->id }}', '{{ addslashes($found->title) }}', '{{ addslashes($found->storage_location) }}', '{{ $lost->id }}')">
                                        <i class="bi bi-shield-check me-1"></i> Claim AI Match
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-outline-secondary" disabled>
                                        <i class="bi bi-clock me-1"></i> Under Claim
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-light border text-muted m-0 p-3">
                            <i class="bi bi-info-circle me-1"></i> No candidate items currently in storage meet the CNN
                            visual similarity threshold for this report.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    @empty
        <div class="card card-custom p-5 text-center text-muted mb-4">
            <i class="bi bi-cpu fs-1 text-warning mb-2"></i>
            <h5>No Active Reported Lost Items Found</h5>
            <p class="fs-7 text-secondary mb-3">You have not submitted any active lost item reports yet. Report a lost item
                to enable automated CNN visual matching against SAO inventory.</p>
            <div>
                <button class="btn btn-primary-custom px-4" data-bs-toggle="modal" data-bs-target="#reportLostModal">
                    <i class="bi bi-plus-circle me-1"></i> Report Lost Item Now
                </button>
            </div>
        </div>
    @endforelse

@endsection

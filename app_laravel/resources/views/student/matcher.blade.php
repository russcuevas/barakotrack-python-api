@extends('layouts.app')

@section('title', 'CNN AI Visual Matcher | Barako Track')

@section('content')
    @include('partials.loading_overlay')
    <!-- Page Header (Image 2 style) -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 gap-sm-3">
        <div>
            <h3 class="fw-bold m-0" style="color: var(--primary-color);">CNN AI Visual Matcher</h3>
            <p class="text-muted fs-7 mb-0 mt-1">Automated visual similarity & hybrid AI matching between your reported lost
                items and SAO found inventory.</p>
        </div>
    </div>

    <!-- CNN AI Status Header Card (Image 2 style) -->
    <div class="card card-custom p-4 mb-4 border-0 shadow-sm"
        style="background: linear-gradient(135deg, #691220 0%, #4d0a15 100%); color: white; border-radius: 12px;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 d-flex align-items-center justify-content-center"
                    style="background: #fec452; color: #691220; width: 50px; height: 50px;">
                    <i class="bi bi-cpu-fill fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold m-0 text-white">BarakoTrack Deep Neural Visual Engine</h5>
                    <p class="m-0 fs-7" style="color: rgba(255,255,255,0.75);">MobileNetV2 CNN Feature Vector Extraction &
                        Cosine Similarity</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill bg-success text-white shadow-sm px-3 py-2 fw-semibold">
                    <i class="bi bi-broadcast me-1"></i> Matcher Engine: Online
                </span>
            </div>
        </div>
    </div>

    <!-- Match Results Section -->
    @forelse($matches as $matchGroup)
        @php
            $lost = $matchGroup['lost_item'];
            $candidates = $matchGroup['candidate_matches'];
        @endphp

        <div class="card card-custom p-4 mb-4 shadow-sm border-0" style="background: #ffffff; border-radius: 12px;">
            <!-- Reported Lost Item Header with Uploaded Image -->
            <div class="d-flex justify-content-between align-items-center pb-3 border-bottom mb-3 flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    @if ($lost->image_path)
                        <img src="{{ $lost->image_path }}" class="rounded border shadow-sm"
                            style="width: 70px; height: 70px; object-fit: cover;" alt="{{ $lost->title }}">
                    @else
                        <div class="rounded border bg-light d-flex flex-column align-items-center justify-content-center text-muted"
                            style="width: 70px; height: 70px;">
                            <i class="bi bi-image fs-4 opacity-50"></i>
                            <small style="font-size: 0.65rem;">No Image</small>
                        </div>
                    @endif
                    <div>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle mb-1 fw-bold">
                            <i class="bi bi-search me-1"></i> Lost Report #LST-{{ $lost->id }}
                        </span>
                        <h5 class="fw-bold m-0" style="color: var(--primary-color);">{{ $lost->title }}</h5>
                        <small class="text-muted d-block"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Lost at
                            {{ $lost->location }} on {{ $lost->date_lost->format('M d, Y') }}</small>
                        <small class="text-secondary fs-7">{{ Str::limit($lost->description, 85) }}</small>
                    </div>
                </div>
                <div>
                    <span class="badge bg-light text-secondary border px-3 py-2 fw-semibold">
                        <i class="bi bi-tag me-1"></i> {{ $lost->category->name ?? 'General' }}
                    </span>
                </div>
            </div>

            <!-- Candidate Found Items Matches -->
            <h6 class="fw-bold mb-3 text-dark">
                <i class="bi bi-magic text-warning me-2"></i> Potential Visual Matches in Storage ({{ count($candidates) }})
            </h6>

            <div class="row g-3 g-md-4">
                @forelse($candidates as $candidate)
                    @php
                        $found = $candidate['found_item'];
                        $score = $candidate['score'];
                        $confidence = $candidate['confidence'];
                        $scoreBadge =
                            $score >= 85
                                ? 'bg-success-subtle text-success border border-success-subtle'
                                : ($score >= 65
                                    ? 'bg-warning-subtle text-warning border border-warning-subtle'
                                    : 'bg-info-subtle text-info border border-info-subtle');
                        $barColor = $score >= 85 ? 'bg-success' : ($score >= 65 ? 'bg-warning' : 'bg-info');
                    @endphp

                    <div class="col-12 col-md-6">
                        <div class="card h-100 border p-3" style="border-radius: 12px; background: #fafafa;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge {{ $scoreBadge }} fw-bold px-3 py-1 rounded-pill">
                                    <i class="bi bi-cpu-fill me-1"></i> {{ $score }}% Visual Match
                                </span>
                                <span class="badge bg-light text-muted border fs-7">{{ $confidence }}</span>
                            </div>

                            <!-- Match Score Progress Bar -->
                            <div class="progress mb-3 shadow-none" style="height: 6px; background-color: #e2e8f0;">
                                <div class="progress-bar {{ $barColor }}" role="progressbar"
                                    style="width: {{ $score }}%;"></div>
                            </div>

                            <div class="d-flex gap-3 align-items-center mb-3">
                                <img src="{{ $found->image_path ?: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80' }}"
                                    class="rounded border" style="width: 72px; height: 72px; object-fit: cover;"
                                    alt="{{ $found->title }}">
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">{{ $found->title }}</h6>
                                    <small class="text-muted d-block"><i class="bi bi-geo-alt text-danger me-1"></i> Found:
                                        {{ $found->location }}</small>
                                    <small class="text-muted d-block"><i class="bi bi-building text-primary me-1"></i>
                                        Storage:
                                        <strong>{{ $found->storage_location }}</strong></small>
                                </div>
                            </div>

                            <p class="fs-7 text-secondary mb-3">{{ Str::limit($found->description, 80) }}</p>

                            <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                                <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>
                                    {{ $found->date_found ? $found->date_found->format('M d, Y') : 'Recent' }}</small>
                                @if ($found->status === 'available')
                                    <button class="btn btn-sm btn-primary-custom fw-bold px-3"
                                        onclick="openClaimModal('{{ $found->id }}', '{{ addslashes($found->title) }}', '{{ addslashes($found->storage_location) }}', '{{ $lost->id }}')">
                                        <i class="bi bi-shield-check me-1"></i> Claim Match
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
                        <div class="alert alert-light border text-muted m-0 p-3" style="border-radius: 8px;">
                            <i class="bi bi-info-circle me-1"></i> No candidate items currently in storage meet the CNN
                            visual similarity threshold for this report.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    @empty
        <div class="card card-custom p-5 text-center text-muted mb-4 border-0 shadow-sm"
            style="background: #ffffff; border-radius: 12px;">
            <i class="bi bi-cpu fs-1 text-secondary mb-2 d-block opacity-50"></i>
            <h5 class="fw-bold text-dark mb-1">No Active Reported Lost Items Found</h5>
            <p class="fs-7 text-muted mb-3">You have not submitted any active lost item reports yet. Report a lost item to
                enable automated CNN visual matching against SAO inventory.</p>
        </div>
    @endforelse
@endsection

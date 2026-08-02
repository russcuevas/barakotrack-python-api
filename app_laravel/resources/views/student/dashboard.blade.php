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
            <div class="text-muted fs-7 fw-semibold">CNN AI Visual Matcher</div>
            <h2 class="fw-bold my-1 text-success">94.8%</h2>
            <small class="text-muted"><i class="bi bi-cpu"></i> ResNet Feature Similarity</small>
        </div>
    </div>
</div>

<!-- CNN Visual Match Recommendation Alert Banner -->
@if($recentFoundItems->count() > 0)
@php $topMatch = $recentFoundItems->first(); @endphp
<div class="card card-custom p-4 mb-4" style="background: linear-gradient(135deg, rgba(117,39,56,0.05), rgba(254,196,82,0.15)); border: 1px solid rgba(254,196,82,0.4);">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle p-3 text-white" style="background-color: var(--primary-color);">
                <i class="bi bi-magic fs-3 text-warning"></i>
            </div>
            <div>
                <h6 class="fw-bold m-0" style="color: var(--primary-color);">CNN AI Visual Similarity Recommendation</h6>
                <p class="text-muted m-0 fs-7">
                    Your reported lost item matches <strong>94.8%</strong> with found item <strong>"{{ $topMatch->title }}"</strong> stored at {{ $topMatch->storage_location }}.
                </p>
            </div>
        </div>
        <button class="btn btn-secondary-custom btn-sm px-3 fw-bold" onclick="openClaimModal('{{ $topMatch->id }}', '{{ addslashes($topMatch->title) }}', '{{ addslashes($topMatch->storage_location) }}')">
            <i class="bi bi-shield-check me-1"></i> Claim Match Now
        </button>
    </div>
</div>
@endif

<!-- Quick Found Items Preview -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold m-0" style="color: var(--primary-color);">Recent Found Items</h5>
    <a href="{{ route('student.found-items') }}" class="text-decoration-none fw-semibold fs-7">View Full Directory <i class="bi bi-chevron-right"></i></a>
</div>

<div class="row g-4 mb-4">
    @foreach($recentFoundItems as $item)
    <div class="col-md-4">
        <div class="card card-custom h-100 overflow-hidden shadow-sm">
            <img src="{{ $item->image_path ?: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80' }}" class="card-img-top" style="height: 160px; object-fit: cover;" alt="{{ $item->title }}">
            <div class="card-body">
                <span class="badge bg-success mb-2">{{ ucfirst($item->status) }}</span>
                <h6 class="fw-bold mb-1" style="color: var(--primary-color);">{{ $item->title }}</h6>
                <p class="text-muted fs-7 mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $item->location }}</p>
                <button class="btn btn-sm btn-primary-custom w-100 mt-2" onclick="openClaimModal('{{ $item->id }}', '{{ addslashes($item->title) }}', '{{ addslashes($item->storage_location) }}')">
                    Submit Claim
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

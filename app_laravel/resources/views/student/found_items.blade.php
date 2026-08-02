@extends('layouts.app')

@section('title', 'Found Items Directory | Barako Track')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold m-0" style="color: var(--primary-color);"><i class="bi bi-box-seam-fill me-2"></i> Found Items Directory</h4>
        <span class="text-muted">Browse items surrendered to SAO office and submit a claim request.</span>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card card-custom p-3 mb-4">
    <form action="{{ route('student.found-items') }}" method="GET" class="row g-3 align-items-center">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Search item title, description, or location..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-4">
            <select name="category" class="form-select" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary-custom w-100">Filter</button>
        </div>
    </form>
</div>

<!-- Items Grid -->
<div class="row g-4">
    @forelse($foundItems as $item)
    <div class="col-md-4">
        <div class="card card-custom h-100 overflow-hidden shadow-sm">
            <img src="{{ $item->image_path ?: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80' }}" class="card-img-top" style="height: 180px; object-fit: cover;" alt="{{ $item->title }}">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    @if($item->status === 'available')
                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Available</span>
                    @elseif($item->status === 'claim_pending')
                        <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i> Claim Pending</span>
                    @else
                        <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                    @endif
                    <small class="text-muted"><i class="bi bi-calendar3"></i> {{ $item->date_found->format('M d, Y') }}</small>
                </div>
                <h6 class="fw-bold mb-1" style="color: var(--primary-color);">{{ $item->title }}</h6>
                <p class="text-muted fs-7 mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $item->location }}</p>
                <p class="fs-7 text-secondary mb-3 flex-grow-1">{{ Str::limit($item->description, 90) }}</p>
                <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                    <small class="text-muted"><i class="bi bi-building"></i> {{ $item->storage_location }}</small>
                    @if($item->status === 'available')
                        <button class="btn btn-sm btn-primary-custom" onclick="openClaimModal('{{ $item->id }}', '{{ addslashes($item->title) }}', '{{ addslashes($item->storage_location) }}')">
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
            No found items matched your search criteria.
        </div>
    </div>
    @endforelse
</div>
@endsection

@extends('layouts.app')

@section('title', 'Found Items Directory | Barako Track')

@section('content')
    <style>
        .found-item-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 16px;
            overflow: hidden;
            background: #ffffff;
        }

        .found-item-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12) !important;
        }

        .item-img-wrapper {
            position: relative;
            height: 200px;
            overflow: hidden;
            background-color: #1e1e2d;
        }

        .item-img-wrapper img {
            transition: transform 0.3s ease;
        }

        .found-item-card:hover .item-img-wrapper img {
            transform: scale(1.05);
        }

        .badge-overlay-top {
            position: absolute;
            top: 12px;
            left: 12px;
            right: 12px;
            z-index: 2;
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold m-0" style="color: var(--primary-color);">
                <i class="bi bi-box-seam-fill text-warning me-2"></i> Found Items Directory
            </h4>
            <span class="text-muted">Browse items surrendered to SAO office and submit claim requests.</span>
        </div>
        <div>
            <span class="badge bg-primary-custom p-2 px-3 fs-7 shadow-sm">
                <i class="bi bi-boxes me-1"></i> {{ $foundItems->count() }} Item(s) Loaded
            </span>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="card card-custom p-3 mb-4 shadow-sm border-0">
        <form action="{{ route('student.found-items') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                        placeholder="Search item title, description, or location..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="category" class="form-select" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary-custom w-100 fw-bold">
                    <i class="bi bi-funnel-fill me-1"></i> Filter
                </button>
                @if (request('search') || request('category'))
                    <a href="{{ route('student.found-items') }}" class="btn btn-outline-secondary" title="Clear Filters">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Items Grid -->
    <div class="row g-4">
        @forelse($foundItems as $item)
            <div class="col-md-6 col-lg-4">
                <div class="card found-item-card h-100 shadow-sm">
                    <div class="item-img-wrapper">
                        <div class="badge-overlay-top d-flex justify-content-between align-items-center">
                            @if ($item->status === 'available')
                                <span class="badge bg-success shadow-sm p-2 px-3 fw-bold"><i
                                        class="bi bi-check-circle-fill me-1"></i> Available</span>
                            @elseif($item->status === 'claim_pending')
                                <span class="badge bg-warning text-dark shadow-sm p-2 px-3 fw-bold"><i
                                        class="bi bi-clock-history me-1"></i> Claim Pending</span>
                            @elseif($item->status === 'ready_for_pickup')
                                <span class="badge bg-info text-dark shadow-sm p-2 px-3 fw-bold"><i
                                        class="bi bi-box-seam me-1"></i> Ready for Pick-up</span>
                            @elseif($item->status === 'claimed')
                                <span class="badge bg-secondary shadow-sm p-2 px-3 fw-bold"><i
                                        class="bi bi-check2-all me-1"></i> Claimed</span>
                            @else
                                <span
                                    class="badge bg-secondary shadow-sm p-2 px-3 fw-bold">{{ ucfirst($item->status) }}</span>
                            @endif

                            <span class="badge bg-dark bg-opacity-75 text-white shadow-sm p-2">
                                <i class="bi bi-tag-fill me-1 text-warning"></i>
                                {{ $item->category->name ?? 'General' }}
                            </span>
                        </div>

                        @if ($item->image_path)
                            <img src="{{ $item->image_path }}" class="w-100 h-100"
                                style="object-fit: cover; cursor: pointer;" alt="{{ $item->title }}"
                                onclick="openImagePreviewModal('{{ $item->image_path }}', '{{ addslashes($item->title) }}')"
                                onerror="this.onerror=null; this.outerHTML='<div class=\'w-100 h-100 bg-light text-muted d-flex flex-column align-items-center justify-content-center text-center p-3\'><i class=\'bi bi-exclamation-triangle fs-2 mb-1 text-warning\'></i><small class=\'fw-bold\'>Invalid image try again</small></div>';">
                        @else
                            <div
                                class="w-100 h-100 bg-light text-muted d-flex flex-column align-items-center justify-content-center text-center p-3">
                                <i class="bi bi-image fs-1 mb-1 text-secondary opacity-50"></i>
                                <span class="fw-bold fs-7">No image</span>
                            </div>
                        @endif
                    </div>

                    <div class="card-body d-flex flex-column p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted"><i class="bi bi-calendar3 me-1 text-primary"></i>
                                {{ $item->date_found->format('M d, Y') }}</small>
                            <small class="text-muted"><i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                {{ $item->location }}</small>
                        </div>

                        <h6 class="fw-bold mb-2 text-dark" style="color: var(--primary-color);">{{ $item->title }}</h6>
                        <p class="fs-7 text-secondary mb-3 flex-grow-1" style="line-height: 1.4;">
                            {{ Str::limit($item->description, 95) }}</p>

                        <div class="pt-2 border-top d-flex justify-content-between align-items-center mt-auto">
                            <small class="text-muted fw-semibold fs-7" title="Storage Location">
                                <i class="bi bi-building me-1 text-info"></i> {{ $item->storage_location }}
                            </small>

                            @if ($item->status === 'available')
                                <button class="btn btn-sm btn-primary-custom fw-bold px-3"
                                    onclick="openClaimModal('{{ $item->id }}', '{{ addslashes($item->title) }}', '{{ addslashes($item->storage_location) }}')">
                                    <i class="bi bi-shield-check me-1"></i> Submit Claim
                                </button>
                            @elseif($item->status === 'ready_for_pickup')
                                <button class="btn btn-sm btn-info text-dark disabled fw-bold" disabled><i class="bi bi-box-seam me-1"></i> Ready for Pick-up</button>
                            @elseif($item->status === 'claimed')
                                <button class="btn btn-sm btn-secondary disabled fw-bold" disabled><i class="bi bi-check2-all me-1"></i> Claimed</button>
                            @else
                                <button class="btn btn-sm btn-outline-secondary disabled fw-bold" disabled>Under Claim</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card card-custom p-5 text-center text-muted shadow-sm">
                    <i class="bi bi-inbox fs-1 text-secondary mb-2 d-block opacity-50"></i>
                    <h5 class="fw-bold text-dark">No Found Items Found</h5>
                    <p class="fs-7 text-muted mb-3">No surrendered items matched your current search or category filter
                        criteria.</p>
                    @if (request('search') || request('category'))
                        <div>
                            <a href="{{ route('student.found-items') }}" class="btn btn-sm btn-primary-custom px-4">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Clear Filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    <!-- Modal: Image Preview (Fallback) -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true" style="z-index: 1065;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold" id="imagePreviewTitle"><i class="bi bi-image me-2 text-warning"></i> Item Image Preview</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0 text-center bg-dark">
                    <img id="previewModalImage" src="" class="img-fluid rounded-bottom" style="max-height: 75vh; object-fit: contain;" alt="Image Preview">
                </div>
            </div>
        </div>
    </div>

    <script>
        function openImagePreviewModal(src, title) {
            if (!src || src.startsWith('data:image/svg+xml')) return;
            const modalEl = document.getElementById('imagePreviewModal');
            if (modalEl) {
                document.getElementById('imagePreviewTitle').innerHTML = `<i class="bi bi-image me-2 text-warning"></i> ${title || 'Item Image Preview'}`;
                document.getElementById('previewModalImage').src = src;
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        }
    </script>
@endsection

@extends('layouts.app')

@section('title', 'My Reported Lost Items | Barako Track')

@section('content')
    @include('partials.loading_overlay')
    <style>
        .scan-container {
            position: relative;
            width: 130px;
            height: 130px;
            margin: 0 auto;
            border-radius: 16px;
            overflow: hidden;
            border: 2px solid var(--primary-color);
            background-color: #0f172a;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }

        .scan-laser {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, transparent, #fec452, #ffffff, #fec452, transparent);
            box-shadow: 0 0 12px #fec452, 0 0 20px #fec452;
            animation: laserScanMove 1.4s infinite ease-in-out alternate;
        }

        @keyframes laserScanMove {
            0% {
                top: 0%;
            }

            100% {
                top: 96%;
            }
        }

        .badge-open-search {
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .badge-open-search:hover {
            transform: translateY(-1px) scale(1.03);
            box-shadow: 0 4px 10px rgba(220, 38, 38, 0.3);
        }
    </style>

    @php
        $totalReports = $lostReports->count();
        $openCount = $lostReports->where('status', 'open')->count();
        $claimPendingCount = $lostReports->where('status', 'claim_pending')->count();
        $resolvedCount = $lostReports->where('status', 'resolved')->count();
    @endphp

    <!-- Page Header (Image 2 style) -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 gap-sm-3">
        <div>
            <h3 class="fw-bold m-0" style="color: var(--primary-color);">My Reported Lost Items</h3>
            <p class="text-muted fs-7 mb-0 mt-1">Track lost reports, run live CNN visual matcher scans, and monitor claims.
            </p>
        </div>
        <button class="btn btn-primary-custom btn-sm px-3 py-2 shadow-sm fw-bold" data-bs-toggle="modal"
            data-bs-target="#reportLostModal">
            <i class="bi bi-plus-circle-fill me-1"></i> Report Lost Item
        </button>
    </div>

    <!-- Mini Metric Cards (Image 2 style) -->
    <div class="row g-3 g-md-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="mini-stat-card">
                <div class="mini-stat-icon" style="background: rgba(59, 130, 246, 0.12); color: #3b82f6;">
                    <i class="bi bi-journal-text"></i>
                </div>
                <div>
                    <div class="mini-stat-label">Total Reports</div>
                    <div class="mini-stat-value">{{ $totalReports }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="mini-stat-card">
                <div class="mini-stat-icon" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                    <i class="bi bi-search"></i>
                </div>
                <div>
                    <div class="mini-stat-label">Active Searches</div>
                    <div class="mini-stat-value text-danger">{{ $openCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="mini-stat-card">
                <div class="mini-stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <div class="mini-stat-label">Claim Under Review</div>
                    <div class="mini-stat-value text-warning">{{ $claimPendingCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="mini-stat-card">
                <div class="mini-stat-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <div class="mini-stat-label">Resolved / Returned</div>
                    <div class="mini-stat-value text-success">{{ $resolvedCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lost Reports Table Card (Image 2 style) -->
    <div class="card card-custom p-4 mb-4 shadow-sm border-0" style="background: #ffffff; border-radius: 12px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-card-checklist text-danger fs-5"></i>
                <h5 class="fw-bold m-0 text-dark">Lost Reports Registry</h5>
            </div>
            <span class="badge bg-light text-secondary border px-3 py-1 fw-semibold">
                <i class="bi bi-cpu-fill text-warning me-1"></i> AI Matcher Ready
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead>
                    <tr style="font-size: 0.8rem; color: #1e293b; border-bottom: 2px solid #f1f5f9;">
                        <th class="py-3 px-3">Ref No.</th>
                        <th class="py-3 px-3">Item Details</th>
                        <th class="py-3 px-3">Category</th>
                        <th class="py-3 px-3">Date Lost</th>
                        <th class="py-3 px-3">Location</th>
                        <th class="py-3 px-3">Status & AI Scan</th>
                        <th class="py-3 px-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lostReports as $report)
                        <tr style="font-size: 0.82rem; border-bottom: 1px solid #f8fafc;">
                            <td class="py-3 px-3 fw-bold" style="color: #691220;">
                                LST-{{ $report->created_at ? $report->created_at->format('Ymd') : '20260914' }}-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="py-3 px-3">
                                <div class="d-flex align-items-center gap-2">
                                    @if ($report->image_path)
                                        <img src="{{ $report->image_path }}" class="rounded border" width="42"
                                            height="42" style="object-fit: cover; cursor: pointer;"
                                            alt="{{ $report->title }}"
                                            onclick="openImagePreviewModal('{{ $report->image_path }}', '{{ addslashes($report->title) }}')"
                                            onerror="this.onerror=null; this.outerHTML='<div class=\'rounded border bg-light text-muted d-flex align-items-center justify-content-center text-center p-1\' style=\'width:42px;height:42px;font-size:8px;line-height:1.1;\'>No image</div>';">
                                    @else
                                        <div class="rounded border bg-light text-muted d-flex align-items-center justify-content-center text-center p-1"
                                            style="width: 42px; height: 42px; font-size: 8px; line-height: 1.1;">
                                            <i class="bi bi-image fs-5 opacity-50"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark">{{ $report->title }}</div>
                                        <small class="text-muted">{{ Str::limit($report->description, 50) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <span class="badge bg-light text-secondary border px-2 py-1">
                                    {{ $report->category->name ?? 'General' }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-muted">
                                {{ $report->date_lost->format('M d, Y') }}
                            </td>
                            <td class="py-3 px-3">
                                <small class="text-muted"><i class="bi bi-geo-alt text-danger me-1"></i>
                                    {{ $report->location }}</small>
                            </td>
                            <td class="py-3 px-3">
                                @if ($report->status === 'open')
                                    <button type="button"
                                        class="badge rounded-pill bg-danger text-white border-0 shadow-sm p-2 px-3 badge-open-search"
                                        onclick="startCnnScan('{{ $report->id }}', '{{ addslashes($report->title) }}', '{{ $report->image_path }}')"
                                        title="Click to run live CNN AI Visual Matcher">
                                        <i class="bi bi-cpu-fill me-1 text-warning"></i> Open Search &bull; AI Scan
                                    </button>
                                @elseif($report->status === 'claim_pending')
                                    <span
                                        class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 fw-semibold">
                                        <i class="bi bi-clock-history me-1"></i> Claim Pending
                                    </span>
                                @elseif($report->status === 'resolved')
                                    <span
                                        class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-1 fw-semibold">
                                        <i class="bi bi-check-circle me-1"></i> Resolved
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center">
                                <form action="{{ route('student.lost-reports.destroy', $report->id) }}" method="POST"
                                    data-confirm="Are you sure you want to delete this lost report? This action cannot be undone."
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn btn-sm btn-light border text-danger shadow-sm px-2 py-1"
                                        title="Delete Report">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                <h6 class="fw-bold text-dark mb-1">No Lost Reports Yet</h6>
                                <p class="fs-7 text-muted mb-3">You have not submitted any lost item reports. Click below
                                    to submit one.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal: Live CNN AI Visual Scanner -->
    <div class="modal fade" id="cnnScanModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header text-white"
                    style="background: linear-gradient(135deg, #691220 0%, #4d0a15 100%);">
                    <h5 class="modal-header-title fw-bold m-0 text-warning">
                        <i class="bi bi-cpu-fill me-2"></i> CNN AI Visual Similarity Scan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <!-- Scanning Animation View -->
                    <div id="cnnScanningState">
                        <div class="scan-container mb-3">
                            <img id="scanItemImage"
                                src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80"
                                class="w-100 h-100" style="object-fit: cover; cursor: pointer;" alt="Scanning Item"
                                onclick="openImagePreviewModal(this.src, document.getElementById('scanItemTitle').innerText.replace('Scanning: ', ''))">
                            <div class="scan-laser"></div>
                        </div>

                        <h5 class="fw-bold text-dark mb-1" id="scanItemTitle">Analyzing Item Features...</h5>
                        <p class="text-muted fs-7 mb-3">MobileNetV2 CNN Feature Vector Extraction</p>

                        <div class="progress mb-3 shadow-sm" style="height: 8px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
                                role="progressbar" style="width: 100%;"></div>
                        </div>

                        <div class="alert alert-dark py-2 fs-7 m-0 text-warning border-warning">
                            <i class="bi bi-arrow-repeat spin me-1"></i> <span id="scanStatusText">Extracting
                                1024-dimensional visual feature vectors...</span>
                        </div>
                    </div>

                    <!-- Results View (Hidden initially) -->
                    <div id="cnnResultsState" class="d-none text-start">
                        <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom">
                            <h6 class="fw-bold m-0 text-dark"><i class="bi bi-magic text-warning me-1"></i> Potential
                                Visual Matches</h6>
                            <span class="badge bg-success" id="matchesFoundBadge">0 Matches</span>
                        </div>

                        <div id="cnnMatchContainer">
                            <!-- Dynamic Match Cards Injected Here -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Close
                        Scanner</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Image Preview -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true" style="z-index: 1065;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold" id="imagePreviewTitle"><i class="bi bi-image me-2 text-warning"></i>
                        Item Image Preview</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0 text-center bg-dark">
                    <img id="previewModalImage" src="" class="img-fluid rounded-bottom"
                        style="max-height: 75vh; object-fit: contain;" alt="Image Preview">
                </div>
            </div>
        </div>
    </div>

    <script>
        async function startCnnScan(lostId, title, imagePath) {
            const scanModal = new bootstrap.Modal(document.getElementById('cnnScanModal'));

            // Reset UI to Scanning State
            document.getElementById('cnnScanningState').classList.remove('d-none');
            document.getElementById('cnnResultsState').classList.add('d-none');

            document.getElementById('scanItemTitle').innerText = 'Scanning: ' + title;
            const imgEl = document.getElementById('scanItemImage');
            const noImgSvg =
                "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='130' height='130' viewBox='0 0 130 130'><rect width='130' height='130' fill='%231e1e2d'/><text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' fill='%23fec452' font-family='sans-serif' font-size='14' font-weight='bold'>No image</text></svg>";
            if (imagePath && imagePath !== 'null' && imagePath !== 'undefined' && imagePath !== '') {
                imgEl.src = imagePath;
                imgEl.onerror = function() {
                    this.onerror = null;
                    this.src = noImgSvg;
                };
            } else {
                imgEl.src = noImgSvg;
            }

            const statusText = document.getElementById('scanStatusText');
            statusText.innerText = 'Extracting MobileNetV2 feature vector...';

            scanModal.show();

            setTimeout(() => {
                statusText.innerText = 'Comparing vector embeddings against SAO Found Inventory...';
            }, 600);

            try {
                const res = await fetch(`/student/lost-reports/${lostId}/cnn-scan`);
                const data = await res.json();

                setTimeout(() => {
                    document.getElementById('cnnScanningState').classList.add('d-none');
                    document.getElementById('cnnResultsState').classList.remove('d-none');

                    const container = document.getElementById('cnnMatchContainer');
                    const badge = document.getElementById('matchesFoundBadge');

                    if (data.status === 'success' && data.matches && data.matches.length > 0) {
                        badge.innerText = `${data.matches.length} Match(es) Found`;
                        badge.className = 'badge bg-success';

                        container.innerHTML = data.matches.map(m => `
                    <div class="card p-3 mb-3 border-warning shadow-sm" style="border-radius: 10px;">
                        <div class="row align-items-center g-3">
                            <div class="col-auto">
                                ${m.image_path ? `
                                                    <img src="${m.image_path}" class="rounded border" width="65" height="65" style="object-fit: cover; cursor: pointer;" alt="${m.title}" onclick="openImagePreviewModal('${m.image_path}', '${m.title.replace(/'/g, "\\'")}')" onerror="this.onerror=null; this.outerHTML='<div class=\\'rounded border bg-light text-muted d-flex align-items-center justify-content-center text-center p-1\\' style=\\'width:65px;height:65px;font-size:9px;line-height:1.1;\\'>No image</div>';">
                                                ` : `
                                                    <div class="rounded border bg-light text-muted d-flex align-items-center justify-content-center text-center p-1" style="width:65px;height:65px;font-size:9px;line-height:1.1;">No image</div>
                                                `}
                            </div>
                            <div class="col">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge ${m.score >= 85 ? 'bg-success' : 'bg-warning text-dark'} fw-bold"><i class="bi bi-cpu-fill me-1"></i> ${m.score}% Match</span>
                                    <span class="badge bg-info text-dark"><i class="bi bi-building me-1"></i> ${m.storage_location}</span>
                                </div>
                                <h6 class="fw-bold m-0" style="color: var(--primary-color);">${m.title}</h6>
                                <small class="text-muted"><i class="bi bi-geo-alt text-danger me-1"></i> Found at: ${m.location} • ${m.date_found}</small>
                            </div>
                            <div class="col-auto">
                                ${m.status === 'available' ? `
                                                        <button class="btn btn-sm btn-primary-custom fw-bold px-3" onclick="openClaimModal('${m.id}', '${m.title.replace(/'/g, "\\'")}', '${m.storage_location.replace(/'/g, "\\'")}', '${lostId}')">
                                                            <i class="bi bi-shield-check me-1"></i> Claim Match
                                                        </button>
                                                    ` : `
                                                        <button class="btn btn-sm btn-outline-secondary disabled fw-bold" disabled>Under Claim</button>
                                                    `}
                            </div>
                        </div>
                    </div>
                `).join('');
                    } else {
                        badge.innerText = '0 Matches';
                        badge.className = 'badge bg-secondary';

                        container.innerHTML = `
                    <div class="text-center py-4 bg-light rounded border">
                        <i class="bi bi-search fs-1 text-muted d-block mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">No High-Confidence CNN Matches Found</h6>
                        <small class="text-muted">No found items currently match this report above 45% similarity. Our system will keep scanning when new items arrive at SAO!</small>
                    </div>
                `;
                    }
                }, 1200);
            } catch (err) {
                document.getElementById('cnnScanningState').classList.add('d-none');
                document.getElementById('cnnResultsState').classList.remove('d-none');
                document.getElementById('cnnMatchContainer').innerHTML = `
            <div class="alert alert-danger py-3">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> Unable to complete CNN scan at the moment. Please try again.
            </div>
        `;
            }
        }

        function openImagePreviewModal(src, title) {
            if (!src || src.startsWith('data:image/svg+xml')) return;
            document.getElementById('imagePreviewTitle').innerHTML =
                `<i class="bi bi-image me-2 text-warning"></i> ${title || 'Item Image Preview'}`;
            document.getElementById('previewModalImage').src = src;
            const modal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
            modal.show();
        }
    </script>
@endsection

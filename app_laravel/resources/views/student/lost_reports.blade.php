@extends('layouts.app')

@section('title', 'My Reported Lost Items | Barako Track')

@section('content')
    <style>
        .scan-container {
            position: relative;
            width: 130px;
            height: 130px;
            margin: 0 auto;
            border-radius: 16px;
            overflow: hidden;
            border: 2px solid var(--primary-color);
            background-color: #1e1e2d;
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

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold m-0" style="color: var(--primary-color);"><i class="bi bi-card-checklist me-2"></i> My Reported
                Lost Items</h4>
            <span class="text-muted">Track lost reports, run live CNN visual matcher scans, and monitor claims.</span>
        </div>
        <button class="btn btn-primary-custom px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#reportLostModal">
            <i class="bi bi-plus-circle-fill me-1"></i> Report New Lost Item
        </button>
    </div>

    <div class="card card-custom p-4 mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead class="table-light">
                    <tr>
                        <th>Report ID</th>
                        <th>Item Title & Description</th>
                        <th>Category</th>
                        <th>Date Lost</th>
                        <th>Location</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lostReports as $report)
                        <tr>
                            <td><strong>#LST-{{ $report->id }}</strong></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if ($report->image_path)
                                        <img src="{{ $report->image_path }}" class="rounded border" width="40"
                                            height="40" style="object-fit: cover; cursor: pointer;" alt="{{ $report->title }}"
                                            onclick="openImagePreviewModal('{{ $report->image_path }}', '{{ addslashes($report->title) }}')"
                                            onerror="this.onerror=null; this.outerHTML='<div class=\'rounded border bg-light text-muted d-flex align-items-center justify-content-center text-center p-1\' style=\'width:40px;height:40px;font-size:8px;line-height:1.1;\'>Invalid image try again</div>';">
                                    @else
                                        <div class="rounded border bg-light text-muted d-flex align-items-center justify-content-center text-center p-1"
                                            style="width: 40px; height: 40px; font-size: 8px; line-height: 1.1;">
                                            No image
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark">{{ $report->title }}</div>
                                        <small class="text-muted">{{ Str::limit($report->description, 50) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $report->category->name }}</span></td>
                            <td>{{ $report->date_lost->format('M d, Y') }}</td>
                            <td><i class="bi bi-geo-alt text-danger me-1"></i> {{ $report->location }}</td>
                            <td>
                                @if ($report->status === 'open')
                                    <span class="badge bg-danger p-2 px-3 badge-open-search"
                                        onclick="startCnnScan('{{ $report->id }}', '{{ addslashes($report->title) }}', '{{ $report->image_path }}')"
                                        title="Click to run live CNN AI Visual Matcher">
                                        <i class="bi bi-cpu-fill me-1 text-warning"></i> Open Search
                                    </span>
                                @elseif($report->status === 'claim_pending')
                                    <span class="badge bg-warning text-dark p-2 px-3">
                                        <i class="bi bi-clock-history me-1"></i> Claim Pending
                                    </span>
                                @elseif($report->status === 'resolved')
                                    <span class="badge bg-success p-2 px-3">
                                        <i class="bi bi-check-circle me-1"></i> Resolved
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                You have not submitted any lost item reports yet. Click "Report New Lost Item" above to
                                report one.
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
            <div class="modal-content">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #1e1e2d 0%, #3a151f 100%);">
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

                        <div class="progress mb-3 shadow-sm" style="height: 10px;">
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
                            <h6 class="fw-bold m-0 text-dark"><i class="bi bi-magic text-warning me-1"></i> AI Finder</h6>
                            <span class="badge bg-success" id="matchesFoundBadge">0 Matches</span>
                        </div>

                        <div id="cnnMatchContainer">
                            <!-- Dynamic Match Cards Injected Here -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close Scanner</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Image Preview -->
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
        async function startCnnScan(lostId, title, imagePath) {
            const scanModal = new bootstrap.Modal(document.getElementById('cnnScanModal'));

            // Reset UI to Scanning State
            document.getElementById('cnnScanningState').classList.remove('d-none');
            document.getElementById('cnnResultsState').classList.add('d-none');

            document.getElementById('scanItemTitle').innerText = 'Scanning: ' + title;
            const imgEl = document.getElementById('scanItemImage');
            const noImgSvg = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='130' height='130' viewBox='0 0 130 130'><rect width='130' height='130' fill='%231e1e2d'/><text x='50%' y='50%' dominant-baseline='middle' text-anchor='middle' fill='%23fec452' font-family='sans-serif' font-size='14' font-weight='bold'>No image</text></svg>";
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
                    <div class="card p-3 mb-3 border-warning shadow-sm">
                        <div class="row align-items-center g-3">
                            <div class="col-auto">
                                ${m.image_path ? `
                                    <img src="${m.image_path}" class="rounded border" width="65" height="65" style="object-fit: cover; cursor: pointer;" alt="${m.title}" onclick="openImagePreviewModal('${m.image_path}', '${m.title.replace(/'/g, "\\'")}')" onerror="this.onerror=null; this.outerHTML='<div class=\\'rounded border bg-light text-muted d-flex align-items-center justify-content-center text-center p-1\\' style=\\'width:65px;height:65px;font-size:9px;line-height:1.1;\\'>Invalid image try again</div>';">
                                ` : `
                                    <div class="rounded border bg-light text-muted d-flex align-items-center justify-content-center text-center p-1" style="width:65px;height:65px;font-size:9px;line-height:1.1;">No image</div>
                                `}
                            </div>
                            <div class="col">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge ${m.score >= 85 ? 'bg-success' : 'bg-warning text-dark'} fw-bold"><i class="bi bi-cpu-fill me-1"></i> ${m.score}% Match</span>
                                    <span class="badge bg-info text-dark"><i class="bi bi-building me-1"></i> ${m.storage_location}</span>
                                </div>
                                <h6 class="fw-bold m-0 text-primary">${m.title}</h6>
                                <small class="text-muted"><i class="bi bi-geo-alt text-danger me-1"></i> Found at: ${m.location} • ${m.date_found}</small>
                            </div>
                            <div class="col-auto">
                                ${m.status === 'available' ? `
                                        <button class="btn btn-sm btn-secondary-custom fw-bold px-3" onclick="openClaimModal('${m.id}', '${m.title.replace(/'/g, "\\'")}', '${m.storage_location.replace(/'/g, "\\'")}', '${lostId}')">
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
            document.getElementById('imagePreviewTitle').innerHTML = `<i class="bi bi-image me-2 text-warning"></i> ${title || 'Item Image Preview'}`;
            document.getElementById('previewModalImage').src = src;
            const modal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
            modal.show();
        }
    </script>
@endsection

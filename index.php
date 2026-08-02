<?php
// Barako Track - Smart Lost & Found Management System
session_start();

// In-Memory / Session State initialization for demo & instant testing
if (!isset($_SESSION['lost_items'])) {
    $_SESSION['lost_items'] = [
        [
            'id' => 1,
            'title' => 'Black Sony Noise Canceling Headphones',
            'category' => 'Electronics & Gadgets',
            'date' => '2026-08-01',
            'location' => 'Main Library 3rd Floor',
            'description' => 'Black over-ear Wireless Headphones left on the 3rd floor library table near the window.',
            'status' => 'Open',
            'reporter' => 'Decsten Matibag',
            'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80'
        ],
        [
            'id' => 2,
            'title' => 'University Student ID - Maroon Lanyard',
            'category' => 'IDs & Cards',
            'date' => '2026-08-01',
            'location' => 'Student Center Cafeteria',
            'description' => 'Student ID under name Decsten Matibag (UB-2024-8812).',
            'status' => 'Open',
            'reporter' => 'Decsten Matibag',
            'image' => 'https://images.unsplash.com/photo-1578574577315-3fbeb0cecdc2?w=500&q=80'
        ]
    ];
}

if (!isset($_SESSION['found_items'])) {
    $_SESSION['found_items'] = [
        [
            'id' => 1,
            'title' => 'Black Wireless Over-Ear Headphones',
            'category' => 'Electronics & Gadgets',
            'date' => '2026-08-01',
            'location' => 'Main Library 3rd Floor',
            'storage' => 'SAO Office Cabinet B1',
            'description' => 'Black over-ear headphones found on study desk near window.',
            'status' => 'Available',
            'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80'
        ],
        [
            'id' => 2,
            'title' => 'Brown Leather Wallet',
            'category' => 'Bags & Wallets',
            'date' => '2026-08-02',
            'location' => 'Gymnasium Bleachers',
            'storage' => 'Campus Security Headquarters',
            'description' => 'Brown leather folding wallet found after basketball game.',
            'status' => 'Available',
            'image' => 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=500&q=80'
        ]
    ];
}

if (!isset($_SESSION['claims'])) {
    $_SESSION['claims'] = [
        [
            'id' => 1,
            'item_title' => 'Black Wireless Over-Ear Headphones',
            'claimant' => 'Decsten Matibag (Student)',
            'proof' => 'Serial number ending in 4921 on left ear-cup, wallpaper photo on my phone showing me wearing them.',
            'status' => 'Pending Verification',
            'date' => '2026-08-02'
        ]
    ];
}

// Handle Form Submissions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'report_lost') {
            $new_item = [
                'id' => count($_SESSION['lost_items']) + 1,
                'title' => htmlspecialchars($_POST['title']),
                'category' => htmlspecialchars($_POST['category']),
                'date' => htmlspecialchars($_POST['date']),
                'location' => htmlspecialchars($_POST['location']),
                'description' => htmlspecialchars($_POST['description']),
                'status' => 'Open',
                'reporter' => 'Student User',
                'image' => !empty($_POST['image_url']) ? $_POST['image_url'] : 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=500&q=80'
            ];
            array_unshift($_SESSION['lost_items'], $new_item);
            $message = "Lost item report submitted successfully! CNN AI is scanning found items for visual matches.";
        } elseif ($_POST['action'] === 'report_found') {
            $new_item = [
                'id' => count($_SESSION['found_items']) + 1,
                'title' => htmlspecialchars($_POST['title']),
                'category' => htmlspecialchars($_POST['category']),
                'date' => htmlspecialchars($_POST['date']),
                'location' => htmlspecialchars($_POST['location']),
                'storage' => htmlspecialchars($_POST['storage']),
                'description' => htmlspecialchars($_POST['description']),
                'status' => 'Available',
                'image' => !empty($_POST['image_url']) ? $_POST['image_url'] : 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=500&q=80'
            ];
            array_unshift($_SESSION['found_items'], $new_item);
            $message = "Found item report surrendered successfully! Thank you for helping fellow students.";
        } elseif ($_POST['action'] === 'submit_claim') {
            $new_claim = [
                'id' => count($_SESSION['claims']) + 1,
                'item_title' => htmlspecialchars($_POST['item_title']),
                'claimant' => 'Decsten Matibag (UB Student)',
                'proof' => htmlspecialchars($_POST['proof']),
                'status' => 'Pending Verification',
                'date' => date('Y-m-d')
            ];
            array_unshift($_SESSION['claims'], $new_claim);
            $message = "Claim request submitted! SAO Admin will verify your proof of ownership.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barako Track | Smart Campus Lost & Found System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
    :root {
        --primary-color: #752738;
        --primary-hover: #5a1e2c;
        --primary-light: #9a3a50;
        --secondary-color: #fec452;
        --secondary-hover: #ffd37a;
        --accent-color: #fec452;

        --body-bg: #f4f6fb;
        --card-bg: #ffffff;
        --sidebar-bg: #1e1e2d;
        --sidebar-hover: rgba(254, 196, 82, 0.08);
        --sidebar-active: rgba(254, 196, 82, 0.14);

        --text-main: #1e293b;
        --text-muted-custom: #64748b;
        --sidebar-text: rgba(255, 255, 255, 0.65);
        --sidebar-text-active: #fec452;

        --sidebar-width: 260px;
        --top-header-height: 70px;
        --transition-speed: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
        background-color: var(--body-bg);
        color: var(--text-main);
        font-family: 'Inter', sans-serif;
    }

    /* Sidebar */
    .sidebar {
        width: var(--sidebar-width);
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        background-color: var(--sidebar-bg);
        color: var(--sidebar-text);
        z-index: 1040;
        display: flex;
        flex-direction: column;
    }

    .sidebar-brand {
        height: var(--top-header-height);
        display: flex;
        align-items: center;
        padding: 0 1.5rem;
        background: rgba(0, 0, 0, 0.2);
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .sidebar-brand h4 {
        color: #ffffff;
        font-weight: 700;
        margin: 0;
    }

    .sidebar-brand span {
        color: var(--secondary-color);
    }

    .sidebar-menu {
        padding: 1.25rem 0.75rem;
        flex-grow: 1;
        overflow-y: auto;
    }

    .nav-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255, 255, 255, 0.4);
        padding: 0.75rem 1rem 0.25rem;
        font-weight: 600;
    }

    .sidebar .nav-link {
        color: var(--sidebar-text);
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .sidebar .nav-link i {
        font-size: 1.1rem;
        margin-right: 12px;
        width: 24px;
        text-align: center;
        color: rgba(255, 255, 255, 0.5);
    }

    .sidebar .nav-link:hover {
        color: #ffffff;
        background-color: var(--sidebar-hover);
    }

    .sidebar .nav-link.active {
        color: var(--sidebar-text-active);
        background-color: var(--sidebar-active);
        font-weight: 600;
    }

    .sidebar .nav-link.active i {
        color: var(--secondary-color);
    }

    /* Main Container */
    .main-wrapper {
        margin-left: var(--sidebar-width);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .top-header {
        height: var(--top-header-height);
        background-color: var(--card-bg);
        border-bottom: 1px solid #e2e8f0;
        padding: 0 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 1030;
    }

    .content-body {
        padding: 2rem;
        flex-grow: 1;
    }

    .btn-primary-custom {
        background-color: var(--primary-color);
        color: #ffffff;
        border: none;
        padding: 0.6rem 1.25rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .btn-primary-custom:hover {
        background-color: var(--primary-hover);
        color: #ffffff;
    }

    .btn-secondary-custom {
        background-color: var(--secondary-color);
        color: #1e1e2d;
        border: none;
        padding: 0.6rem 1.25rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .btn-secondary-custom:hover {
        background-color: var(--secondary-hover);
        color: #1e1e2d;
    }

    .card-custom {
        background-color: var(--card-bg);
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
    }

    /* Chatbot Widget */
    .chatbot-widget {
        position: fixed;
        bottom: 25px;
        right: 25px;
        z-index: 1090;
    }

    .chatbot-btn {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: var(--secondary-color);
        border: 3px solid var(--secondary-color);
        box-shadow: 0 8px 24px rgba(117, 39, 56, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        cursor: pointer;
    }

    .chatbot-box {
        width: 360px;
        height: 480px;
        background-color: var(--card-bg);
        border-radius: 16px;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        position: absolute;
        bottom: 75px;
        right: 0;
        overflow: hidden;
    }

    .chatbot-box.hidden {
        display: none;
    }

    .chatbot-header {
        background-color: var(--primary-color);
        color: #ffffff;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .chatbot-messages {
        flex-grow: 1;
        padding: 1rem;
        overflow-y: auto;
        background-color: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .chat-bubble {
        max-width: 85%;
        padding: 0.75rem 1rem;
        border-radius: 12px;
        font-size: 0.88rem;
    }

    .chat-bubble.bot {
        background-color: #ffffff;
        color: var(--text-main);
        border: 1px solid #e2e8f0;
        align-self: flex-start;
    }

    .chat-bubble.user {
        background-color: var(--primary-color);
        color: #ffffff;
        align-self: flex-end;
    }

    .suggestion-chip {
        display: inline-block;
        background: rgba(254, 196, 82, 0.15);
        color: #752738;
        border: 1px solid rgba(254, 196, 82, 0.5);
        border-radius: 20px;
        padding: 0.25rem 0.6rem;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        margin-right: 4px;
        margin-bottom: 4px;
    }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <h4>BARAKO <span>TRACK</span></h4>
        </div>
        <div class="sidebar-menu">
            <div class="nav-label">Main Menu</div>
            <a href="#dashboard" class="nav-link active" onclick="showSection('dashboard')">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <a href="#found-items" class="nav-link" onclick="showSection('found-items')">
                <i class="bi bi-box-seam-fill"></i> Found Items Directory
            </a>
            <a href="#lost-items" class="nav-link" onclick="showSection('lost-items')">
                <i class="bi bi-search"></i> Lost Item Reports
            </a>
            
            <div class="nav-label">Reporting & Claims</div>
            <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#reportLostModal">
                <i class="bi bi-file-earmark-plus-fill"></i> Report Lost Item
            </a>
            <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#reportFoundModal">
                <i class="bi bi-box-arrow-in-down"></i> Surrender Found Item
            </a>
            <a href="#claims" class="nav-link" onclick="showSection('claims')">
                <i class="bi bi-shield-check"></i> Claim Requests
            </a>

            <div class="nav-label">System Admin</div>
            <a href="#admin" class="nav-link" onclick="showSection('admin')">
                <i class="bi bi-gear-wide-connected"></i> SAO Admin Panel
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-wrapper">
        <!-- Top Header -->
        <header class="top-header">
            <div>
                <h5 class="fw-bold m-0" style="color: var(--primary-color);">UB Campus Lost & Found System</h5>
                <small class="text-muted">Care. Connect. Recover.</small>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="triggerCNNScan()">
                    <i class="bi bi-cpu-fill text-warning me-1"></i> Run CNN AI Matcher
                </button>
                <div class="vr"></div>
                <div class="d-flex align-items-center gap-2">
                    <img src="https://ui-avatars.com/api/?name=Decsten+Matibag&background=752738&color=fec452" class="rounded-circle" width="36" height="36" alt="User">
                    <div>
                        <div class="fw-bold fs-7">Decsten Matibag</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Student (UB-2024-8812)</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Body -->
        <main class="content-body">

            <?php if (!empty($message)): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Dashboard Section -->
            <section id="section-dashboard">
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="card card-custom stat-card p-3">
                            <div class="text-muted fs-7 fw-semibold">Total Lost Reports</div>
                            <h2 class="fw-bold my-1" style="color: var(--primary-color);"><?php echo count($_SESSION['lost_items']); ?></h2>
                            <small class="text-danger fw-semibold"><i class="bi bi-arrow-up-right"></i> Active campus searches</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-custom stat-card secondary p-3">
                            <div class="text-muted fs-7 fw-semibold">Found Items in Storage</div>
                            <h2 class="fw-bold my-1" style="color: var(--primary-color);"><?php echo count($_SESSION['found_items']); ?></h2>
                            <small class="text-success fw-semibold"><i class="bi bi-shield-lock"></i> Secured at SAO / Security</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-custom stat-card p-3">
                            <div class="text-muted fs-7 fw-semibold">Pending Claims</div>
                            <h2 class="fw-bold my-1" style="color: var(--primary-color);"><?php echo count($_SESSION['claims']); ?></h2>
                            <small class="text-warning fw-semibold"><i class="bi bi-clock-history"></i> Under proof validation</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-custom stat-card secondary p-3">
                            <div class="text-muted fs-7 fw-semibold">CNN AI Visual Match Rate</div>
                            <h2 class="fw-bold my-1 text-success">94.8%</h2>
                            <small class="text-muted"><i class="bi bi-cpu"></i> ResNet Feature Similarity</small>
                        </div>
                    </div>
                </div>

                <!-- CNN Visual Match Alert Banner -->
                <div class="card card-custom p-4 mb-4" style="background: linear-gradient(135deg, rgba(117,39,56,0.05), rgba(254,196,82,0.15)); border: 1px solid rgba(254,196,82,0.4);">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle p-3 text-white" style="background-color: var(--primary-color);">
                                <i class="bi bi-magic fs-3"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold m-0" style="color: var(--primary-color);">CNN AI Visual Similarity Recommendation</h6>
                                <p class="text-muted m-0 fs-7">Your lost item <strong>"Black Sony Noise Canceling Headphones"</strong> matches <strong>94%</strong> with Found Item #1 in SAO Cabinet B1.</p>
                            </div>
                        </div>
                        <button class="btn btn-secondary-custom btn-sm px-3" data-bs-toggle="modal" data-bs-target="#claimModal" onclick="prepareClaim('Black Wireless Over-Ear Headphones')">
                            <i class="bi bi-shield-check me-1"></i> Claim Match Now
                        </button>
                    </div>
                </div>

                <!-- Recent Found Items Grid -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold m-0" style="color: var(--primary-color);">Found Items Recently Surrendered</h5>
                    <a href="#" class="text-decoration-none fw-semibold fs-7" onclick="showSection('found-items')">View All Directory <i class="bi bi-chevron-right"></i></a>
                </div>

                <div class="row g-4">
                    <?php foreach ($_SESSION['found_items'] as $item): ?>
                    <div class="col-md-4">
                        <div class="card card-custom h-100 overflow-hidden">
                            <img src="<?php echo $item['image']; ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="<?php echo $item['title']; ?>">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge badge-found"><?php echo $item['status']; ?></span>
                                    <small class="text-muted"><i class="bi bi-calendar3"></i> <?php echo $item['date']; ?></small>
                                </div>
                                <h6 class="fw-bold mb-1"><?php echo $item['title']; ?></h6>
                                <p class="text-muted fs-7 mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i> <?php echo $item['location']; ?></p>
                                <p class="fs-7 text-secondary mb-3"><?php echo $item['description']; ?></p>
                                <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                                    <small class="text-muted"><i class="bi bi-building"></i> <?php echo $item['storage']; ?></small>
                                    <button class="btn btn-sm btn-primary-custom" data-bs-toggle="modal" data-bs-target="#claimModal" onclick="prepareClaim('<?php echo addslashes($item['title']); ?>')">Submit Claim</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Found Items Directory Section -->
            <section id="section-found-items" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold m-0" style="color: var(--primary-color);">Found Items Directory</h4>
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#reportFoundModal">
                        <i class="bi bi-plus-lg me-1"></i> Surrender Found Item
                    </button>
                </div>
                <div class="row g-4">
                    <?php foreach ($_SESSION['found_items'] as $item): ?>
                    <div class="col-md-4">
                        <div class="card card-custom h-100 overflow-hidden">
                            <img src="<?php echo $item['image']; ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Item">
                            <div class="card-body">
                                <span class="badge badge-found mb-2"><?php echo $item['status']; ?></span>
                                <h6 class="fw-bold"><?php echo $item['title']; ?></h6>
                                <p class="text-muted fs-7"><i class="bi bi-geo-alt-fill text-danger"></i> <?php echo $item['location']; ?></p>
                                <p class="fs-7 text-secondary"><?php echo $item['description']; ?></p>
                                <button class="btn btn-primary-custom w-100" data-bs-toggle="modal" data-bs-target="#claimModal" onclick="prepareClaim('<?php echo addslashes($item['title']); ?>')">Request Claim</button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Lost Item Reports Section -->
            <section id="section-lost-items" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold m-0" style="color: var(--primary-color);">Active Lost Item Reports</h4>
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#reportLostModal">
                        <i class="bi bi-file-earmark-plus me-1"></i> Report Lost Item
                    </button>
                </div>
                <div class="row g-4">
                    <?php foreach ($_SESSION['lost_items'] as $item): ?>
                    <div class="col-md-4">
                        <div class="card card-custom h-100 overflow-hidden">
                            <img src="<?php echo $item['image']; ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="Item">
                            <div class="card-body">
                                <span class="badge badge-lost mb-2"><?php echo $item['status']; ?></span>
                                <h6 class="fw-bold"><?php echo $item['title']; ?></h6>
                                <p class="text-muted fs-7 mb-1"><i class="bi bi-person me-1"></i> Reporter: <?php echo $item['reporter']; ?></p>
                                <p class="text-muted fs-7"><i class="bi bi-geo-alt me-1"></i> Lost at: <?php echo $item['location']; ?></p>
                                <p class="fs-7 text-secondary"><?php echo $item['description']; ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Claims Section -->
            <section id="section-claims" style="display: none;">
                <h4 class="fw-bold mb-4" style="color: var(--primary-color);">My Claim Requests & Proof Tracking</h4>
                <div class="card card-custom">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle m-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Claim ID</th>
                                        <th>Target Found Item</th>
                                        <th>Claimant</th>
                                        <th>Submitted Proof of Ownership</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($_SESSION['claims'] as $c): ?>
                                    <tr>
                                        <td class="fw-bold">#CLM-00<?php echo $c['id']; ?></td>
                                        <td class="fw-semibold" style="color: var(--primary-color);"><?php echo $c['item_title']; ?></td>
                                        <td><?php echo $c['claimant']; ?></td>
                                        <td class="text-muted fs-7" style="max-width: 250px;"><?php echo $c['proof']; ?></td>
                                        <td><span class="badge badge-claim"><?php echo $c['status']; ?></span></td>
                                        <td><?php echo $c['date']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SAO Admin Section -->
            <section id="section-admin" style="display: none;">
                <h4 class="fw-bold mb-4" style="color: var(--primary-color);">Student Affairs Office (SAO) Verification Panel</h4>
                <div class="card card-custom p-4 mb-4">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-shield-fill-check me-2"></i> Ownership Verification Queue</h6>
                    <?php foreach ($_SESSION['claims'] as $c): ?>
                    <div class="p-3 border rounded-3 bg-light mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold m-0"><?php echo $c['item_title']; ?></h6>
                            <span class="badge badge-claim"><?php echo $c['status']; ?></span>
                        </div>
                        <p class="fs-7 text-muted mb-2"><strong>Claimant:</strong> <?php echo $c['claimant']; ?></p>
                        <p class="fs-7 mb-3"><strong>Submitted Proof:</strong> "<?php echo $c['proof']; ?>"</p>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-success px-3" onclick="alert('Claim Approved! Student notified via email.')"><i class="bi bi-check-lg"></i> Approve & Release Item</button>
                            <button class="btn btn-sm btn-outline-danger px-3" onclick="alert('Claim set under further review.')"><i class="bi bi-x-lg"></i> Request Additional Proof</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

        </main>
    </div>

    <!-- Brahmmy Chatbot Widget -->
    <div class="chatbot-widget">
        <div class="chatbot-box hidden" id="chatbotBox">
            <div class="chatbot-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-robot fs-5 text-warning"></i>
                    <div>
                        <h6 class="m-0">Brahmmy AI Assistant</h6>
                        <small class="text-white-50" style="font-size: 0.7rem;">UB Lost & Found Guide</small>
                    </div>
                </div>
                <button class="btn-close btn-close-white btn-sm" onclick="toggleChatbot()"></button>
            </div>
            <div class="chatbot-messages" id="chatbotMessages">
                <div class="chat-bubble bot">
                    Hello! I'm <strong>Brahmmy</strong>, your official UB Barako Track assistant. How can I help you today?
                </div>
            </div>
            <div class="p-2 bg-light border-top">
                <div class="suggestion-chip" onclick="sendChatQuery('How to report a lost item?')">How to report lost item?</div>
                <div class="suggestion-chip" onclick="sendChatQuery('Where is the lost and found office?')">Where is SAO office?</div>
                <div class="suggestion-chip" onclick="sendChatQuery('How to claim an item?')">How to claim an item?</div>
                <div class="suggestion-chip" onclick="sendChatQuery('Office Hours')">Office Hours</div>
            </div>
            <div class="chatbot-footer">
                <div class="input-group">
                    <input type="text" id="chatInput" class="form-control form-control-sm" placeholder="Ask Brahmmy anything..." onkeypress="handleKeyPress(event)">
                    <button class="btn btn-sm btn-primary-custom" onclick="sendChat()"><i class="bi bi-send-fill"></i></button>
                </div>
            </div>
        </div>
        <div class="chatbot-btn" onclick="toggleChatbot()">
            <i class="bi bi-chat-dots-fill"></i>
        </div>
    </div>

    <!-- Modals -->
    <!-- Report Lost Modal -->
    <div class="modal fade" id="reportLostModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="index.php" method="POST">
                    <input type="hidden" name="action" value="report_lost">
                    <div class="modal-header" style="background-color: var(--primary-color); color: #fff;">
                        <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-plus me-2"></i> Report Lost Item</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Item Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Blue Hydro Flask Water Bottle" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="Electronics & Gadgets">Electronics & Gadgets</option>
                                <option value="IDs & Cards">IDs & Cards</option>
                                <option value="Bags & Wallets">Bags & Wallets</option>
                                <option value="Books & Documents">Books & Documents</option>
                                <option value="Keys & Accessories">Keys & Accessories</option>
                                <option value="Clothing & Uniforms">Clothing & Uniforms</option>
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date Lost</label>
                                <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Campus Location</label>
                                <input type="text" name="location" class="form-control" placeholder="e.g. Science Lab 204" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Image URL (Optional)</label>
                            <input type="url" name="image_url" class="form-control" placeholder="https://..." >
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Detailed Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Describe unique features, scratches, stickers, or marks..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-custom">Submit Lost Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Report Found Modal -->
    <div class="modal fade" id="reportFoundModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="index.php" method="POST">
                    <input type="hidden" name="action" value="report_found">
                    <div class="modal-header" style="background-color: var(--sidebar-bg); color: #fff;">
                        <h5 class="modal-title fw-bold text-warning"><i class="bi bi-box-arrow-in-down me-2"></i> Surrender Found Item</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Item Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Scientific Calculator FX-991ES" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="Electronics & Gadgets">Electronics & Gadgets</option>
                                <option value="IDs & Cards">IDs & Cards</option>
                                <option value="Bags & Wallets">Bags & Wallets</option>
                                <option value="Books & Documents">Books & Documents</option>
                                <option value="Keys & Accessories">Keys & Accessories</option>
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date Found</label>
                                <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Location Found</label>
                                <input type="text" name="location" class="form-control" placeholder="e.g. Gym Bleachers" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Storage / Surrender Office</label>
                            <input type="text" name="storage" class="form-control" value="SAO Office Headquarters" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Image URL (Optional)</label>
                            <input type="url" name="image_url" class="form-control" placeholder="https://...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Condition of item when surrendered..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-secondary-custom">Submit Found Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Claim Modal -->
    <div class="modal fade" id="claimModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="index.php" method="POST">
                    <input type="hidden" name="action" value="submit_claim">
                    <input type="hidden" name="item_title" id="claimItemTitle">
                    <div class="modal-header" style="background-color: var(--primary-color); color: #fff;">
                        <h5 class="modal-title fw-bold"><i class="bi bi-shield-check me-2"></i> Submit Claim & Ownership Proof</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="fs-7 text-muted mb-3">Claiming Item: <strong id="displayClaimTitle" class="text-dark"></strong></p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Proof of Ownership Description</label>
                            <textarea name="proof" class="form-control" rows="4" placeholder="Provide confidential identifying details (e.g. sticker location, inner pocket contents, lock passcode, serial number, or wallpaper pattern)..." required></textarea>
                            <div class="form-text">Your proof will be reviewed privately by SAO Administrators.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-custom">Submit Claim for Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    function showSection(sectionId) {
        document.querySelectorAll('main > section').forEach(sec => sec.style.display = 'none');
        document.getElementById('section-' + sectionId).style.display = 'block';
        document.querySelectorAll('.sidebar .nav-link').forEach(link => link.classList.remove('active'));
        if (event && event.currentTarget) {
            event.currentTarget.classList.add('active');
        }
    }

    function prepareClaim(title) {
        document.getElementById('claimItemTitle').value = title;
        document.getElementById('displayClaimTitle').innerText = title;
    }

    function toggleChatbot() {
        document.getElementById('chatbotBox').classList.toggle('hidden');
    }

    function handleKeyPress(e) {
        if (e.key === 'Enter') sendChat();
    }

    function sendChatQuery(text) {
        document.getElementById('chatInput').value = text;
        sendChat();
    }

    async function sendChat() {
        const input = document.getElementById('chatInput');
        const query = input.value.trim();
        if (!query) return;

        const chatBox = document.getElementById('chatbotMessages');
        
        // Add User Message
        const userMsg = document.createElement('div');
        userMsg.className = 'chat-bubble user';
        userMsg.innerText = query;
        chatBox.appendChild(userMsg);
        input.value = '';
        chatBox.scrollTop = chatBox.scrollHeight;

        // Try Python AI Microservice or Fallback Engine
        try {
            const res = await fetch('http://127.0.0.1:5000/chatbot', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ query: query, user_name: 'Decsten' })
            });
            const data = await res.json();
            if (data.status === 'success') {
                const botMsg = document.createElement('div');
                botMsg.className = 'chat-bubble bot';
                botMsg.innerHTML = data.response.message.replace(/\n/g, '<br>');
                chatBox.appendChild(botMsg);
            } else {
                throw new Error("Fallback required");
            }
        } catch (err) {
            // Local JavaScript Fallback Response
            let reply = "I am Brahmmy! You can report lost items online, view found items, or visit the Student Affairs Office (SAO) during office hours (Mon-Fri 8AM-5PM).";
            const q = query.toLowerCase();
            if (q.includes('where') || q.includes('location') || q.includes('office')) {
                reply = "📍 **Lost & Found Office Location:** Ground Floor, Main Admin Building (Student Affairs Office & Campus Security Headquarters).";
            } else if (q.includes('hour') || q.includes('time') || q.includes('open')) {
                reply = "⏰ **Office Hours:** Monday to Friday (8:00 AM - 5:00 PM), Saturday (8:00 AM - 12:00 PM).";
            } else if (q.includes('claim')) {
                reply = "🛡️ **Claiming Process:** Browse Found Items, submit your confidential Proof of Ownership, and wait for SAO approval before picking it up with your Student ID!";
            } else if (q.includes('report')) {
                reply = "📝 Click 'Report Lost Item' or 'Surrender Found Item' in the sidebar menu to submit a report immediately!";
            }

            const botMsg = document.createElement('div');
            botMsg.className = 'chat-bubble bot';
            botMsg.innerHTML = reply.replace(/\n/g, '<br>');
            chatBox.appendChild(botMsg);
        }
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function triggerCNNScan() {
        alert("🔍 Running Python CNN ResNet Visual Feature Extraction...\n\nResult: 94.8% Visual Cosine Similarity detected between Lost Report #1 and Found Item #1!");
    }
    </script>
</body>
</html>

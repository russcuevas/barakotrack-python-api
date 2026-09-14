<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Barako Track | UB Campus Lost & Found')</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo/favicon.png') }}">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom UB BarakoTrack CSS (Cache-Busting for Deployment) -->
    <link rel="stylesheet"
        href="{{ asset('css/barako_track.css') }}?v={{ file_exists(public_path('css/barako_track.css')) ? filemtime(public_path('css/barako_track.css')) : time() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    @php
        $authUser = auth()->user() ?? \App\Models\User::first();
        $userRole = $authUser->role ?? 'student';
        $categories = \App\Models\Category::all();
    @endphp

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <!-- Brand Header -->
        <div class="sidebar-brand">
            <div class="d-flex align-items-center gap-2">
                <div>
                    <img src="{{ asset('logo/favicon.png') }}" width="40" height="40"
                        style="object-fit: contain;" alt="UB">
                </div>
                <div style="line-height: 1.15;">
                    <div class="d-flex align-items-center gap-1">
                        <span
                            style="font-weight: 800; font-size: 1.05rem; letter-spacing: 0.5px; color: #ffffff;">BARAKO</span>
                        <span
                            style="font-weight: 800; font-size: 1.05rem; letter-spacing: 0.5px; color: #fec452;">TRACK</span>
                        <span class="badge"
                            style="background-color: #fec452; color: #691220; font-size: 0.62rem; font-weight: 800; padding: 2px 4px; border-radius: 4px;">UB</span>
                    </div>
                    <small
                        style="color: rgba(255,255,255,0.65); font-size: 0.68rem; font-weight: 500; display: block;">Smart
                        Lost & Found System</small>
                </div>
            </div>
        </div>

        <!-- Sidebar User Profile Box (Image 2 style) -->
        <div class="sidebar-user-box">
            <div class="sidebar-user-avatar">
                {{ strtoupper(substr($authUser->name ?? 'U', 0, 1)) }}
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name" title="{{ $authUser->name }}">{{ $authUser->name }}</div>
                <div class="sidebar-user-role">
                    @if ($userRole === 'admin')
                        ADMIN • SAO MANAGEMENT
                    @else
                        STUDENT • #{{ $authUser->student_id_number ?: 'CAMPUS USER' }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <div class="sidebar-menu">
            <div class="nav-label">MAIN NAVIGATION</div>
            @if ($userRole === 'admin')
                <!-- SAO Admin Menu -->
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
                <a href="{{ route('admin.claims') }}"
                    class="nav-link {{ request()->routeIs('admin.claims') ? 'active' : '' }}">
                    <i class="bi bi-shield-exclamation"></i> Pending Claims
                </a>
                <a href="{{ route('admin.inventory') }}"
                    class="nav-link {{ request()->routeIs('admin.inventory') ? 'active' : '' }}">
                    <i class="bi bi-archive-fill"></i> Found Inventory
                </a>
                <a href="{{ route('admin.lost-reports') }}"
                    class="nav-link {{ request()->routeIs('admin.lost-reports') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i> Lost Reports
                </a>

                <div class="nav-label mt-3">QUICK ACTIONS</div>
                <a href="#" class="nav-link" style="color: #fec452;" data-bs-toggle="modal"
                    data-bs-target="#reportFoundModal">
                    <i class="bi bi-plus-circle-fill text-warning"></i> Add Found Item
                </a>
            @else
                <!-- Student Menu -->
                <a href="{{ route('student.dashboard') }}"
                    class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
                <a href="{{ route('student.found-items') }}"
                    class="nav-link {{ request()->routeIs('student.found-items') ? 'active' : '' }}">
                    <i class="bi bi-box-seam-fill"></i> Found Items
                </a>
                <a href="{{ route('student.lost-reports') }}"
                    class="nav-link {{ request()->routeIs('student.lost-reports') ? 'active' : '' }}">
                    <i class="bi bi-card-checklist"></i> My Lost Reports
                </a>
                <a href="{{ route('student.matcher') }}"
                    class="nav-link {{ request()->routeIs('student.matcher') ? 'active' : '' }}">
                    <i class="bi bi-cpu-fill"></i> AI Matcher
                </a>
                <a href="{{ route('student.claims') }}"
                    class="nav-link {{ request()->routeIs('student.claims') ? 'active' : '' }}">
                    <i class="bi bi-shield-check"></i> My Claims
                </a>

                <div class="nav-label mt-3">QUICK ACTIONS</div>
                <a href="#" class="nav-link" style="color: #fec452;" data-bs-toggle="modal"
                    data-bs-target="#reportLostModal">
                    <i class="bi bi-file-earmark-plus-fill text-warning"></i> Report Lost Item
                </a>
            @endif
        </div>

    </div>

    <!-- Main Content Area -->
    <div class="main-wrapper">
        <!-- Top Header (Image 2 style) -->
        <header class="top-header">
            <div class="d-flex align-items-center gap-2">
                <!-- Mobile Sidebar Toggle Button -->
                <button class="btn btn-light border shadow-sm d-lg-none py-1 px-2 me-1" id="sidebarToggle"
                    type="button" aria-label="Toggle Navigation Sidebar">
                    <i class="bi bi-list fs-4 text-dark"></i>
                </button>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="fw-bold" style="color: var(--primary-color); font-size: 0.92rem;">University of
                        Batangas</span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 gap-sm-3">
                <!-- Real-time Date Badge -->
                <div class="header-date-badge d-none d-md-flex">
                    <i class="bi bi-calendar3 text-muted"></i>
                    <span>{{ now()->format('l, F d, Y') }}</span>
                </div>

                <!-- Red Power Icon Quick Logout -->
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="header-power-btn" title="Sign Out">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </header>

        <!-- Content Body -->
        <main class="content-body">
            <!-- Top-Right SweetAlert Notifications -->
            @include('partials.sweetalert')

            @yield('content')
        </main>

        <!-- App Footer (Image 2 style) -->
        <footer class="app-footer py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>University of Batangas • Lost and Found Management System (BarakoTrack)</div>
            <div>&copy; {{ date('Y') }} UB. All rights reserved.</div>
        </footer>
    </div>

    <!-- Modal: Report Lost Item (Student) -->
    <div class="modal fade" id="reportLostModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form action="{{ route('student.lost-reports.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header text-white" style="background-color: var(--primary-color);">
                        <h5 class="modal-header-title fw-bold m-0"><i
                                class="bi bi-file-earmark-plus-fill me-2 text-warning"></i> Report Lost Item</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Item Title</label>
                            <input type="text" name="title" class="form-control"
                                placeholder="e.g. Black Sony Noise Canceling Headphones" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Category</label>
                            <select name="category_id" class="form-select" id="reportCategorySelect"
                                onchange="handleReportCategoryChange(this)" required>
                                <option value="">Select Category...</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                                <option value="others">Others...</option>
                            </select>
                            <div id="otherCategoryWrapper" class="mt-2 d-none">
                                <input type="text" name="other_category" id="otherCategoryInput"
                                    class="form-control" placeholder="Please specify category name...">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Date Lost</label>
                                <input type="date" name="date_lost" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Last Known Location</label>
                                <input type="text" name="location" class="form-control"
                                    placeholder="e.g. Main Library 3rd Floor" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Detailed Description & Unique Marks</label>
                            <textarea name="description" class="form-control" rows="3"
                                placeholder="Describe color, brand, scratches, wallpaper, contents..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Item Photo (Used for CNN AI Matcher)</label>
                            <input type="file" name="image" class="form-control" accept="image/*"
                                onchange="previewReportModalImage(this)">
                            <div id="reportImagePreviewBox"
                                class="mt-2 text-center p-2 rounded border bg-light d-none">
                                <small class="text-muted d-block mb-1 fw-bold"><i class="bi bi-image me-1"></i>
                                    Uploaded Photo Preview</small>
                                <img id="reportModalImagePreview" src="#" alt="Uploaded Photo Preview"
                                    class="rounded border shadow-sm img-fluid"
                                    style="max-height: 180px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-custom px-4"><i
                                class="bi bi-send-fill me-1"></i> Submit Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Add Found Item (Admin) -->
    <div class="modal fade" id="reportFoundModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.inventory.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header text-white" style="background-color: #1e1e2d;">
                        <h5 class="modal-header-title fw-bold m-0"><i
                                class="bi bi-box-arrow-in-down me-2 text-warning"></i> Register Surrendered Found Item
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Item Title</label>
                            <input type="text" name="title" class="form-control"
                                placeholder="e.g. Brown Leather Wallet" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Category</label>
                            <select name="category_id" class="form-select" id="foundCategorySelect"
                                onchange="handleFoundCategoryChange(this)" required>
                                <option value="">Select Category...</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                                <option value="others">Others...</option>
                            </select>
                            <div id="otherFoundCategoryWrapper" class="mt-2 d-none">
                                <input type="text" name="other_category" id="otherFoundCategoryInput"
                                    class="form-control" placeholder="Please specify category name...">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Date Found</label>
                                <input type="date" name="date_found" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Location Found</label>
                                <input type="text" name="location" class="form-control"
                                    placeholder="e.g. Student Center Cafeteria" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">SAO Storage Location / Cabinet #</label>
                            <input type="text" name="storage_location" class="form-control"
                                placeholder="e.g. SAO Office Cabinet B1 / Safe #2" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Item Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Provide visible details..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Item Photo</label>
                            <input type="file" name="image" class="form-control" accept="image/*"
                                onchange="previewFoundModalImage(this)">
                            <div id="foundImagePreviewBox"
                                class="mt-2 text-center p-2 rounded border bg-light d-none">
                                <small class="text-muted d-block mb-1 fw-bold"><i class="bi bi-image me-1"></i>
                                    Uploaded Photo Preview</small>
                                <img id="foundModalImagePreview" src="#" alt="Uploaded Photo Preview"
                                    class="rounded border shadow-sm img-fluid"
                                    style="max-height: 180px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-custom px-4"><i
                                class="bi bi-box-seam me-1"></i> Register into Inventory</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Submit Claim Request (Student) -->
    <div class="modal fade" id="claimModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('student.claims.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="found_item_id" id="claimFoundItemId">
                    <input type="hidden" name="lost_item_id" id="claimLostItemId">
                    <div class="modal-header text-white" style="background-color: var(--primary-color);">
                        <h5 class="modal-header-title fw-bold m-0"><i
                                class="bi bi-shield-check me-2 text-warning"></i> Submit Claim Request</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning py-2 mb-3">
                            <div class="fw-bold" id="claimFoundItemTitle">Selected Found Item</div>
                            <small class="text-muted" id="claimFoundItemLocation">Storage Location: SAO Office</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Proof of Ownership & Specific Identifiers</label>
                            <textarea name="proof_description" class="form-control" rows="4"
                                placeholder="Describe unique features non-publicly known (e.g., lock code, wallpaper, name sticker, receipt number, internal contents)..."
                                required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Proof Document / Photo (Optional)</label>
                            <input type="file" name="proof_image" class="form-control" accept="image/*">
                            <small class="text-muted">Upload receipt, old photo of item, or warranty card.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-secondary-custom px-4 fw-bold"><i
                                class="bi bi-check-circle-fill me-1"></i> Submit Claim to SAO</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Brahmmy Chatbot Widget (Displayed for Students only) -->
    @if ($userRole !== 'admin')
        <div class="chatbot-widget">
            <div class="chatbot-box hidden" id="chatbotBox">
                <div class="chatbot-header">
                    <div class="chatbot-header-info">
                        <div class="chatbot-avatar-header">
                            <i class="bi bi-robot"></i>
                        </div>
                        <div>
                            <h6 class="m-0">Brahmmy AI Assistant</h6>
                            <small class="text-white-50" style="font-size: 0.72rem;">
                                <span class="online-dot"></span> Online • UB Campus Guide
                            </small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <button type="button" class="btn btn-sm text-white-50 p-1 me-1" title="Clear Chat"
                            onclick="clearChat()">
                            <i class="bi bi-arrow-counterclockwise fs-6"></i>
                        </button>
                        <button type="button" class="btn-close btn-close-white btn-sm"
                            onclick="toggleChatbot()"></button>
                    </div>
                </div>

                <div class="chatbot-messages" id="chatbotMessages">
                    <div class="chat-message-row bot">
                        <div class="bot-avatar-small"><i class="bi bi-robot"></i></div>
                        <div class="chat-bubble bot">
                            Hello! I'm <strong>Brahmmy</strong>, your official UB Barako Track AI Assistant. How can I
                            help you recover or report an item today?
                        </div>
                    </div>
                </div>

                <div class="chatbot-suggestions-wrapper">
                    <div class="chatbot-suggestions-header">
                        <span><i class="bi bi-stars text-warning me-1"></i> Quick Question Prompts:</span>
                    </div>
                    <div class="chatbot-suggestions" id="chatbotSuggestions">
                        <div class="suggestion-chip" onclick="sendChatQuery('How to report a found item?')">
                            <i class="bi bi-box-arrow-in-down text-warning"></i>
                            <span>Report Found Item</span>
                        </div>
                        <div class="suggestion-chip" onclick="sendChatQuery('How to report a lost item?')">
                            <i class="bi bi-file-earmark-plus text-warning"></i>
                            <span>Report Lost Item</span>
                        </div>
                        <div class="suggestion-chip" onclick="sendChatQuery('How to claim an item?')">
                            <i class="bi bi-shield-check text-warning"></i>
                            <span>How to Claim Item</span>
                        </div>
                        <div class="suggestion-chip" onclick="sendChatQuery('Where is the lost and found office?')">
                            <i class="bi bi-geo-alt-fill text-warning"></i>
                            <span>SAO Office & Hours</span>
                        </div>
                    </div>
                </div>

                <div class="chatbot-footer">
                    <div class="chatbot-input-group">
                        <input type="text" id="chatInput" placeholder="Ask Brahmmy anything..."
                            onkeypress="handleKeyPress(event)">
                        <button type="button" class="chatbot-send-btn" onclick="sendChat()" title="Send Message">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="chatbot-btn" id="chatbotToggleBtn" onclick="toggleChatbot()" title="Chat with Brahmmy AI">
                <i class="bi bi-chat-dots-fill" id="chatBtnIcon"></i>
            </div>
        </div>
    @endif

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function openClaimModal(itemId, itemTitle, storageLoc, lostItemId = '') {
            document.getElementById('claimFoundItemId').value = itemId;
            document.getElementById('claimFoundItemTitle').innerText = itemTitle;
            document.getElementById('claimFoundItemLocation').innerText = "Storage: " + storageLoc;
            const lostInput = document.getElementById('claimLostItemId');
            if (lostInput) lostInput.value = lostItemId;
            var claimModal = new bootstrap.Modal(document.getElementById('claimModal'));
            claimModal.show();
        }

        function toggleChatbot() {
            const box = document.getElementById('chatbotBox');
            const icon = document.getElementById('chatBtnIcon');
            box.classList.toggle('hidden');
            if (icon) {
                if (box.classList.contains('hidden')) {
                    icon.className = 'bi bi-chat-dots-fill';
                } else {
                    icon.className = 'bi bi-x-lg';
                    setTimeout(() => {
                        const input = document.getElementById('chatInput');
                        if (input) input.focus();
                    }, 150);
                }
            }
        }

        function formatMarkdown(text) {
            if (!text) return '';
            let html = text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;");

            // Convert **bold** to <strong>bold</strong>
            html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

            // Convert *italic* to <em>italic</em>
            html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');

            // Convert newlines to <br>
            html = html.replace(/\n/g, '<br>');

            return html;
        }

        function clearChat() {
            const chatBox = document.getElementById('chatbotMessages');
            chatBox.innerHTML = `
                <div class="chat-message-row bot">
                    <div class="bot-avatar-small"><i class="bi bi-robot"></i></div>
                    <div class="chat-bubble bot">
                        Chat history cleared! How can I assist you, student?
                    </div>
                </div>
            `;
            const sugBox = document.getElementById('chatbotSuggestions');
            if (sugBox) {
                sugBox.innerHTML = `
                    <div class="suggestion-chip" onclick="sendChatQuery('How to report a found item?')">
                        <i class="bi bi-box-arrow-in-down text-warning"></i>
                        <span>Report Found Item</span>
                    </div>
                    <div class="suggestion-chip" onclick="sendChatQuery('How to report a lost item?')">
                        <i class="bi bi-file-earmark-plus text-warning"></i>
                        <span>Report Lost Item</span>
                    </div>
                    <div class="suggestion-chip" onclick="sendChatQuery('How to claim an item?')">
                        <i class="bi bi-shield-check text-warning"></i>
                        <span>How to Claim Item</span>
                    </div>
                    <div class="suggestion-chip" onclick="sendChatQuery('Where is the lost and found office?')">
                        <i class="bi bi-geo-alt-fill text-warning"></i>
                        <span>SAO Office & Hours</span>
                    </div>
                `;
            }
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

            // User Message
            const userRow = document.createElement('div');
            userRow.className = 'chat-message-row user';
            userRow.innerHTML = `<div class="chat-bubble user">${formatMarkdown(query)}</div>`;
            chatBox.appendChild(userRow);
            input.value = '';
            chatBox.scrollTop = chatBox.scrollHeight;

            // Typing Indicator
            const typingRow = document.createElement('div');
            typingRow.className = 'chat-message-row bot';
            typingRow.id = 'typingIndicatorRow';
            typingRow.innerHTML = `
                <div class="bot-avatar-small"><i class="bi bi-robot"></i></div>
                <div class="chat-bubble bot py-2">
                    <div class="typing-indicator">
                        <span class="typing-dot"></span>
                        <span class="typing-dot"></span>
                        <span class="typing-dot"></span>
                    </div>
                </div>
            `;
            chatBox.appendChild(typingRow);
            chatBox.scrollTop = chatBox.scrollHeight;

            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch('/api/chatbot', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        query: query
                    })
                });
                const data = await res.json();

                const typingEl = document.getElementById('typingIndicatorRow');
                if (typingEl) typingEl.remove();

                if (data.status === 'success') {
                    const botRow = document.createElement('div');
                    botRow.className = 'chat-message-row bot';
                    botRow.innerHTML = `
                        <div class="bot-avatar-small"><i class="bi bi-robot"></i></div>
                        <div class="chat-bubble bot">${formatMarkdown(data.response.message)}</div>
                    `;
                    chatBox.appendChild(botRow);

                    // Dynamic Suggestion Chips (2x2 Clean Grid - 100% visible, no scrolling needed)
                    if (data.response.suggestions && data.response.suggestions.length > 0) {
                        const sugBox = document.getElementById('chatbotSuggestions');
                        if (sugBox) {
                            const topSuggestions = data.response.suggestions.slice(0, 4);
                            sugBox.innerHTML = topSuggestions.map(s => {
                                let icon = 'bi-chat-dots-fill';
                                let label = s;
                                const lower = s.toLowerCase();
                                if (lower.includes('found') && (lower.includes('report') || lower.includes(
                                        'how') || lower.includes('surrender'))) {
                                    icon = 'bi-box-arrow-in-down';
                                    label = 'Report Found Item';
                                } else if (lower.includes('lost') && lower.includes('report')) {
                                    icon = 'bi-file-earmark-plus';
                                    label = 'Report Lost Item';
                                } else if (lower.includes('claim') || lower.includes('proof')) {
                                    icon = 'bi-shield-check';
                                    label = 'How to Claim Item';
                                } else if (lower.includes('where') || lower.includes('office') || lower
                                    .includes('location')) {
                                    icon = 'bi-geo-alt-fill';
                                    label = 'SAO Office Location';
                                } else if (lower.includes('hour') || lower.includes('time') || lower.includes(
                                        'schedule') || lower.includes('open')) {
                                    icon = 'bi-clock-fill';
                                    label = 'Office Hours';
                                } else if (lower.includes('search') && lower.includes('found')) {
                                    icon = 'bi-search';
                                    label = 'Search Found Items';
                                } else if (lower.includes('search') && lower.includes('lost')) {
                                    icon = 'bi-search';
                                    label = 'Search Lost Items';
                                } else if (lower.includes('contact') || lower.includes('support')) {
                                    icon = 'bi-telephone-fill';
                                    label = 'Contact SAO Office';
                                }

                                return `
                                    <div class="suggestion-chip" onclick="sendChatQuery('${s.replace(/'/g, "\\'")}')" title="${s}">
                                        <i class="bi ${icon} text-warning"></i>
                                        <span>${label}</span>
                                    </div>
                                `;
                            }).join('');
                        }
                    }
                }
            } catch (err) {
                const typingEl = document.getElementById('typingIndicatorRow');
                if (typingEl) typingEl.remove();

                const botRow = document.createElement('div');
                botRow.className = 'chat-message-row bot';
                botRow.innerHTML = `
                    <div class="bot-avatar-small"><i class="bi bi-robot"></i></div>
                    <div class="chat-bubble bot">
                        I am <strong>Brahmmy</strong>! You can report lost items online, view found items, or visit SAO (Mon-Fri 8AM-5PM).
                    </div>
                `;
                chatBox.appendChild(botRow);
            }
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function handleReportCategoryChange(selectEl) {
            const wrapper = document.getElementById('otherCategoryWrapper');
            const input = document.getElementById('otherCategoryInput');
            if (selectEl.value === 'others') {
                wrapper.classList.remove('d-none');
                input.required = true;
                input.focus();
            } else {
                wrapper.classList.add('d-none');
                input.required = false;
                input.value = '';
            }
        }

        function previewReportModalImage(inputEl) {
            const previewBox = document.getElementById('reportImagePreviewBox');
            const previewImg = document.getElementById('reportModalImagePreview');
            if (inputEl.files && inputEl.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewBox.classList.remove('d-none');
                };
                reader.readAsDataURL(inputEl.files[0]);
            } else {
                previewBox.classList.add('d-none');
                previewImg.src = '#';
            }
        }

        function handleFoundCategoryChange(selectEl) {
            const wrapper = document.getElementById('otherFoundCategoryWrapper');
            const input = document.getElementById('otherFoundCategoryInput');
            if (selectEl.value === 'others') {
                wrapper.classList.remove('d-none');
                input.required = true;
                input.focus();
            } else {
                wrapper.classList.add('d-none');
                input.required = false;
                input.value = '';
            }
        }

        function previewFoundModalImage(inputEl) {
            const previewBox = document.getElementById('foundImagePreviewBox');
            const previewImg = document.getElementById('foundModalImagePreview');
            if (inputEl.files && inputEl.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewBox.classList.remove('d-none');
                };
                reader.readAsDataURL(inputEl.files[0]);
            } else {
                previewBox.classList.add('d-none');
                previewImg.src = '#';
            }
        }

        // Mobile Sidebar Responsiveness Handler
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');

            if (sidebarToggle && sidebar && backdrop) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    backdrop.classList.toggle('show');
                });

                backdrop.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    backdrop.classList.remove('show');
                });

                document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth < 992) {
                            sidebar.classList.remove('show');
                            backdrop.classList.remove('show');
                        }
                    });
                });
            }
        });
    </script>
</body>

</html>

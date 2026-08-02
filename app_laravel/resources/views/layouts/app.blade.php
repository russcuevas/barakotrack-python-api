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
    <!-- Custom UB BarakoTrack CSS -->
    <link rel="stylesheet" href="{{ asset('css/barako_track.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    @php
        $authUser = auth()->user() ?? \App\Models\User::first();
        $userRole = $authUser->role ?? 'student';
        $categories = \App\Models\Category::all();
    @endphp

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="sidebar-brand d-flex align-items-center gap-2">
            <img src="{{ asset('logo/favicon.png') }}" width="32" height="32" style="object-fit: contain;"
                alt="Barako Track Logo">
            <h5 class="m-0">BARAKO <span>TRACK</span></h5>
        </div>
        <div class="sidebar-menu">
            @if ($userRole === 'admin')
                <!-- SAO Admin Menu -->
                <div class="nav-label">SAO Command Center</div>
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Admin Dashboard
                </a>
                <a href="{{ route('admin.claims') }}"
                    class="nav-link {{ request()->routeIs('admin.claims') ? 'active' : '' }}">
                    <i class="bi bi-shield-exclamation text-warning"></i> Pending Verification Claims
                </a>
                <a href="{{ route('admin.inventory') }}"
                    class="nav-link {{ request()->routeIs('admin.inventory') ? 'active' : '' }}">
                    <i class="bi bi-archive-fill"></i> SAO Storage Inventory
                </a>
                <a href="{{ route('admin.lost-reports') }}"
                    class="nav-link {{ request()->routeIs('admin.lost-reports') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i> All Lost Item Reports
                </a>

                <div class="nav-label">Actions</div>
                <a href="#" class="nav-link text-warning" data-bs-toggle="modal"
                    data-bs-target="#reportFoundModal">
                    <i class="bi bi-plus-circle-fill"></i> Register Found Item
                </a>
            @else
                <!-- Student Menu -->
                <div class="nav-label">Student Main Menu</div>
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

                <div class="nav-label">Actions</div>
                <a href="#" class="nav-link text-warning" data-bs-toggle="modal"
                    data-bs-target="#reportLostModal">
                    <i class="bi bi-file-earmark-plus-fill text-warning"></i> Report Lost Item
                </a>
            @endif
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-wrapper">
        <!-- Top Header -->
        <header class="top-header d-flex justify-content-between align-items-center px-4">
            <div>
                <h5 class="fw-bold m-0" style="color: var(--primary-color);">UB Campus</h5>
                <small class="text-muted">University of Batangas • Care. Connect. Recover.</small>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- User Info Display -->
                <div class="d-flex align-items-center gap-2">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($authUser->name) }}&background={{ $userRole === 'admin' ? '1e1e2d' : '752738' }}&color=fec452"
                        class="rounded-circle" width="38" height="38" alt="User">
                    <div>
                        <div class="fw-bold fs-7">{{ $authUser->name }}</div>
                        <div class="text-muted fs-7" style="font-size: 0.75rem;">
                            @if ($userRole === 'admin')
                                <span class="badge bg-danger">SAO Administrator</span>
                            @else
                                <span class="badge bg-secondary">Student
                                    ({{ $authUser->student_id_number ?? 'UB-2024' }})</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="vr"></div>

                <!-- Logout Button -->
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger px-3">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Content Body -->
        <main class="content-body">
            <!-- Flash Notifications -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2"
                    role="alert">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2"
                    role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>{{ session('warning') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Modal: Report Lost Item (Student) -->
    <div class="modal fade" id="reportLostModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
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
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category...</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
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
                            <input type="file" name="image" class="form-control" accept="image/*">
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

    <!-- Modal: Register Found Item (Admin) -->
    <div class="modal fade" id="reportFoundModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
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
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category...</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
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
                            <label class="form-label fw-bold">Item Photo (Optional)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
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

                <div class="chatbot-suggestions" id="chatbotSuggestions">
                    <div class="suggestion-chip" onclick="sendChatQuery('How to report a lost item?')">
                        <i class="bi bi-file-earmark-plus me-1"></i> Report Lost
                    </div>
                    <div class="suggestion-chip" onclick="sendChatQuery('Where is the lost and found office?')">
                        <i class="bi bi-geo-alt me-1"></i> SAO Office
                    </div>
                    <div class="suggestion-chip" onclick="sendChatQuery('How to claim an item?')">
                        <i class="bi bi-shield-check me-1"></i> Claim Info
                    </div>
                    <div class="suggestion-chip" onclick="sendChatQuery('Office Hours')">
                        <i class="bi bi-clock me-1"></i> Office Hours
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

                    // Dynamic Suggestion Chips (2x2 Grid)
                    if (data.response.suggestions && data.response.suggestions.length > 0) {
                        const sugBox = document.getElementById('chatbotSuggestions');
                        if (sugBox) {
                            const topSuggestions = data.response.suggestions.slice(0, 4);
                            sugBox.innerHTML = topSuggestions.map(s => `
                                <div class="suggestion-chip" onclick="sendChatQuery('${s.replace(/'/g, "\\'")}')">
                                    <i class="bi bi-chat-text me-1"></i> ${s}
                                </div>
                            `).join('');
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
    </script>
</body>

</html>

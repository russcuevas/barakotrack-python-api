<!-- Global Full-Screen Loading Overlay Partial -->
<style>
    .global-loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 999999 !important;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.25s ease, visibility 0.25s ease;
        pointer-events: none;
    }

    .global-loading-overlay.active {
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: all !important;
    }

    .loading-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 32px 40px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
        text-align: center;
        max-width: 380px;
        width: 90%;
        animation: loadingPulse 1.6s infinite ease-in-out;
    }

    @keyframes loadingPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.02); }
    }

    .custom-spinner {
        width: 3.8rem;
        height: 3.8rem;
        border: 4px solid #e5e7eb;
        border-top: 4px solid #800000; /* UB Barako Maroon */
        border-radius: 50%;
        animation: spinOverlay 0.85s linear infinite;
        margin: 0 auto 16px auto;
    }

    @keyframes spinOverlay {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<div id="globalLoadingOverlay" class="global-loading-overlay">
    <div class="loading-card">
        <div class="custom-spinner"></div>
        <h6 class="fw-bold mb-1 text-dark" id="loadingTitle">Processing Request...</h6>
        <p class="text-muted fs-7 m-0" id="loadingMessage">Please wait while your request is being submitted.</p>
    </div>
</div>

<script>
    if (typeof window.initLoadingOverlay === 'undefined') {
        window.initLoadingOverlay = true;

        document.addEventListener('DOMContentLoaded', function () {
            const overlay = document.getElementById('globalLoadingOverlay');

            // Attach submit listener to all forms on the page
            document.addEventListener('submit', function (e) {
                const form = e.target;

                // Native browser HTML5 validation check
                if (form.checkValidity && !form.checkValidity()) {
                    return;
                }

                // Show full-screen overlay to block any further user clicks
                if (overlay) {
                    overlay.classList.add('active');
                }

                // Disable submit buttons to prevent double-submissions
                const submitBtns = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                submitBtns.forEach(btn => {
                    btn.disabled = true;
                    btn.classList.add('disabled');
                });
            }, true);
        });

        window.showGlobalLoading = function(title, message) {
            const overlay = document.getElementById('globalLoadingOverlay');
            const tEl = document.getElementById('loadingTitle');
            const mEl = document.getElementById('loadingMessage');
            if (tEl && title) tEl.innerText = title;
            if (mEl && message) mEl.innerText = message;
            if (overlay) overlay.classList.add('active');
        };

        window.hideGlobalLoading = function() {
            const overlay = document.getElementById('globalLoadingOverlay');
            if (overlay) overlay.classList.remove('active');
        };
    }
</script>

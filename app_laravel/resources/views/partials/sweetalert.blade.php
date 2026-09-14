<!-- SweetAlert2 CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* UB Barako Track SweetAlert2 Top-Right Toast Custom Styling */
    .swal2-container.swal2-top-end {
        top: 24px !important;
        right: 24px !important;
        z-index: 10000000 !important;
    }

    .swal2-popup.swal2-toast {
        background: #ffffff !important;
        color: #1e293b !important;
        border: 1px solid rgba(117, 39, 56, 0.12) !important;
        border-left: 5px solid #752738 !important; /* UB Barako Maroon accent line */
        border-radius: 12px !important;
        box-shadow: 0 12px 32px -4px rgba(15, 23, 42, 0.18), 0 4px 12px -2px rgba(15, 23, 42, 0.08) !important;
        padding: 14px 18px !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.2s ease !important;
    }

    .swal2-popup.swal2-toast.swal2-icon-success {
        border-left-color: #10b981 !important;
    }

    .swal2-popup.swal2-toast.swal2-icon-error {
        border-left-color: #ef4444 !important;
    }

    .swal2-popup.swal2-toast.swal2-icon-warning {
        border-left-color: #f59e0b !important;
    }

    .swal2-popup.swal2-toast.swal2-icon-info {
        border-left-color: #3b82f6 !important;
    }

    .swal2-toast .swal2-title {
        font-size: 0.92rem !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        margin: 0 !important;
        line-height: 1.4 !important;
    }

    .swal2-toast .swal2-html-container {
        font-size: 0.83rem !important;
        color: #475569 !important;
        margin: 4px 0 0 0 !important;
        line-height: 1.4 !important;
    }

    .swal2-toast .swal2-icon {
        width: 1.85em !important;
        height: 1.85em !important;
        margin: 0 10px 0 0 !important;
    }

    /* Custom Timer Progress Bar Colors */
    .swal2-toast.swal2-icon-success .swal2-timer-progress-bar {
        background: #10b981 !important;
    }

    .swal2-toast.swal2-icon-error .swal2-timer-progress-bar {
        background: #ef4444 !important;
    }

    .swal2-toast.swal2-icon-warning .swal2-timer-progress-bar {
        background: #f59e0b !important;
    }

    .swal2-toast.swal2-icon-info .swal2-timer-progress-bar {
        background: #752738 !important; /* UB Barako Maroon */
    }

    /* SweetAlert Modal (for confirmation dialogs) */
    .barako-swal-popup {
        border-radius: 18px !important;
        font-family: 'Inter', sans-serif !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
    }
    .barako-swal-confirm-btn {
        background-color: #752738 !important;
        color: #ffffff !important;
        border-radius: 8px !important;
        padding: 9px 22px !important;
        font-weight: 600 !important;
        font-size: 0.9rem !important;
        box-shadow: 0 4px 12px rgba(117, 39, 56, 0.25) !important;
    }
    .barako-swal-confirm-btn:hover {
        background-color: #5a1e2c !important;
    }
    .barako-swal-cancel-btn {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        border-radius: 8px !important;
        padding: 9px 20px !important;
        font-weight: 600 !important;
        font-size: 0.9rem !important;
        margin-right: 10px !important;
    }
    .barako-swal-cancel-btn:hover {
        background-color: #e2e8f0 !important;
    }
</style>

<script>
    (function () {
        // Define SweetAlert2 Top-Right Toast Mixin
        const BarakoToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        // Global helper references
        window.Toast = BarakoToast;
        window.showToast = function(icon, title, text = '') {
            BarakoToast.fire({
                icon: icon,
                title: title,
                html: text
            });
        };

        // Classy confirmation dialog helper using SweetAlert
        window.confirmAction = function(options) {
            return Swal.fire({
                title: options.title || 'Are you sure?',
                text: options.text || 'This action cannot be undone.',
                icon: options.icon || 'warning',
                showCancelButton: true,
                confirmButtonText: options.confirmText || 'Yes, proceed',
                cancelButtonText: options.cancelText || 'Cancel',
                customClass: {
                    popup: 'barako-swal-popup',
                    confirmButton: 'barako-swal-confirm-btn',
                    cancelButton: 'barako-swal-cancel-btn'
                },
                buttonsStyling: false
            });
        };

        // Auto-fire Laravel Flash Notifications upon page load
        document.addEventListener('DOMContentLoaded', function () {
            @if (session('success'))
                BarakoToast.fire({
                    icon: 'success',
                    title: {!! json_encode(session('success')) !!}
                });
            @endif

            @if (session('warning'))
                BarakoToast.fire({
                    icon: 'warning',
                    title: {!! json_encode(session('warning')) !!}
                });
            @endif

            @if (session('error'))
                BarakoToast.fire({
                    icon: 'error',
                    title: {!! json_encode(session('error')) !!}
                });
            @endif

            @if (session('status'))
                BarakoToast.fire({
                    icon: 'info',
                    title: {!! json_encode(session('status')) !!}
                });
            @endif

            @if (session('info'))
                BarakoToast.fire({
                    icon: 'info',
                    title: {!! json_encode(session('info')) !!}
                });
            @endif

            @if ($errors->any())
                @php
                    $allErrors = $errors->all();
                @endphp
                @if (count($allErrors) === 1)
                    BarakoToast.fire({
                        icon: 'error',
                        title: {!! json_encode($allErrors[0]) !!}
                    });
                @else
                    BarakoToast.fire({
                        icon: 'error',
                        title: 'Form Validation Error',
                        html: {!! json_encode('<div class="text-start"><ul class="mb-0 ps-3 mt-1" style="font-size: 0.8rem; line-height: 1.4;">' . implode('', array_map(fn($e) => '<li>' . e($e) . '</li>', $allErrors)) . '</ul></div>') !!}
                    });
                @endif
            @endif

            // Enhance forms with data-confirm attribute to use SweetAlert modal confirmation
            document.addEventListener('submit', function (e) {
                const form = e.target;
                const confirmMsg = form.getAttribute('data-confirm');
                if (confirmMsg && !form.dataset.confirmed) {
                    e.preventDefault();
                    window.confirmAction({
                        title: 'Are you sure?',
                        text: confirmMsg,
                        icon: 'warning',
                        confirmText: 'Yes, proceed',
                        cancelText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.dataset.confirmed = 'true';
                            if (typeof form.requestSubmit === 'function') {
                                form.requestSubmit();
                            } else {
                                form.submit();
                            }
                        }
                    });
                }
            });
        });
    })();
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="color-scheme" content="only light">
    <meta name="theme-color" content="#b84e00">
    <meta name="description" content="GauSeva Connect — Register and connect with Krishan Balram Gaushala, Pakhowal Road, Ludhiana. Receive blessings on your birthday and anniversary via WhatsApp.">
    <meta property="og:title" content="GauSeva Connect — Krishan Balram Gaushala">
    <meta property="og:description" content="Join the Gau Seva family and receive divine blessings on your birthday & anniversary.">
    <meta property="og:image" content="/icons/og-image.png">
    <meta property="og:url" content="https://krishanbalramgaushala.com">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <!-- Preload critical logo — WebP only (widely supported, avoids type mismatch) -->
    <link rel="preload" href="/logo.webp" as="image">
    <!-- PWA support — both standards -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="GauSeva">
    <title inertia>GauSeva Connect — Krishan Balram Gaushala</title>
    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
    <style>
        /* PWA Install Banner & Overlay Styles */
        .pwa-banner {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(120px);
            width: calc(100% - 32px);
            max-width: 440px;
            background: var(--bg1);
            border: 1px solid rgba(184, 78, 0, 0.18);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(140, 110, 80, 0.22);
            z-index: 99999;
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.4s ease;
            opacity: 0;
            pointer-events: none;
        }
        .pwa-banner.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
            pointer-events: auto;
        }
        .pwa-banner.hidden {
            display: none !important;
        }
        .pwa-banner-content {
            display: flex;
            align-items: center;
            padding: 14px 16px;
            gap: 12px;
        }
        .pwa-logo-container {
            width: 44px;
            height: 44px;
            flex-shrink: 0;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
            border: 1px solid rgba(180, 128, 40, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .pwa-logo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .pwa-text-container {
            flex-grow: 1;
            min-width: 0;
        }
        .pwa-title {
            font-family: var(--font-sans);
            font-size: 13.5px;
            font-weight: 700;
            color: var(--pr);
            margin-bottom: 2px;
        }
        .pwa-desc {
            font-family: var(--font-sans);
            font-size: 11px;
            color: var(--tl);
            line-height: 1.35;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .pwa-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }
        .pwa-btn-install {
            background: linear-gradient(145deg, var(--pr2), var(--pr1) 45%, var(--pr) 75%);
            color: #fff5e0;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-family: var(--font-sans);
            font-size: 11.5px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(184, 78, 0, 0.2);
            transition: transform 0.15s, opacity 0.15s;
        }
        .pwa-btn-install:active {
            transform: scale(0.95);
        }
        .pwa-btn-close {
            background: transparent;
            border: none;
            color: var(--td);
            font-size: 14px;
            padding: 6px;
            cursor: pointer;
            transition: color 0.15s;
        }
        .pwa-btn-close:hover {
            color: var(--pr);
        }

        /* PWA iOS Modal Styles */
        .pwa-ios-modal {
            position: fixed;
            inset: 0;
            background: rgba(43, 18, 2, 0.35);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 100000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .pwa-ios-modal.show {
            opacity: 1;
            pointer-events: auto;
        }
        .pwa-ios-modal.hidden {
            display: none !important;
        }
        .pwa-ios-content {
            background: var(--bg1);
            border: 1px solid rgba(184, 78, 0, 0.2);
            border-radius: 20px;
            width: 100%;
            max-width: 360px;
            padding: 24px 20px;
            box-shadow: 0 20px 45px rgba(43, 18, 2, 0.18);
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            transform: scale(0.92);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .pwa-ios-modal.show .pwa-ios-content {
            transform: scale(1);
        }
        .pwa-ios-close {
            position: absolute;
            top: 12px;
            right: 12px;
            background: transparent;
            border: none;
            color: var(--td);
            font-size: 16px;
            padding: 6px;
            cursor: pointer;
        }
        .pwa-logo-container-large {
            width: 64px;
            height: 64px;
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
            border: 1px solid rgba(180, 128, 40, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            box-shadow: 0 5px 12px rgba(184, 78, 0, 0.08);
        }
        .pwa-logo-large {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .pwa-ios-title {
            font-family: var(--font-serif);
            font-size: 18px;
            font-weight: 800;
            color: var(--pr);
            margin-bottom: 6px;
            text-align: center;
        }
        .pwa-ios-desc {
            font-family: var(--font-sans);
            font-size: 11px;
            color: var(--tl);
            margin-bottom: 16px;
            text-align: center;
            line-height: 1.45;
        }
        .pwa-ios-steps {
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
            margin-bottom: 20px;
        }
        .pwa-ios-step {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--bg2);
            border-radius: 10px;
            padding: 8px 12px;
            border: 1px solid rgba(184, 78, 0, 0.05);
        }
        .pwa-step-num {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--pr);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            flex-shrink: 0;
        }
        .pwa-step-text {
            font-family: var(--font-sans);
            font-size: 10.5px;
            color: var(--tx);
            flex-grow: 1;
            line-height: 1.4;
        }
        .pwa-share-icon, .pwa-add-icon {
            font-size: 16px;
            flex-shrink: 0;
        }
        .pwa-btn-ok {
            background: linear-gradient(145deg, var(--pr2), var(--pr1) 45%, var(--pr) 75%);
            color: #fff5e0;
            border: none;
            border-radius: 10px;
            padding: 10px 24px;
            font-family: var(--font-sans);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            box-shadow: 0 4px 10px rgba(184, 78, 0, 0.2);
            transition: transform 0.15s;
        }
        .pwa-btn-ok:active {
            transform: scale(0.97);
        }

        /* Social Floating Container */
        .social-floater-container {
            position: fixed;
            bottom: 12px;
            right: 16px;
            display: flex;
            flex-direction: row;
            gap: 12px;
            z-index: 99990;
            transition: opacity 0.3s ease;
            align-items: center;
        }
        .social-floater-container.hidden {
            display: none !important;
        }

        /* Floating Icon Buttons (Icon Only) */
        .social-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--td); /* Soft brown default color */
            opacity: 0.75;
            cursor: pointer;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), color 0.25s ease, opacity 0.25s ease;
            text-decoration: none;
            background: transparent;
            border: none;
            padding: 0; /* Keeps icon only, no wrapper/padding */
        }
        .social-icon-btn svg {
            width: 16px;
            height: 16px;
            display: block;
        }
        .social-icon-btn:hover {
            transform: scale(1.15);
            opacity: 1;
        }
        .social-icon-btn:active {
            transform: scale(0.95);
        }

        /* Accent Colors on Hover */
        .social-icon-btn.insta:hover {
            color: #e1306c;
        }
        .social-icon-btn.fb:hover {
            color: var(--fb);
        }
        .social-icon-btn.whatsapp:hover {
            color: var(--wa);
        }
    </style>
</head>
<body>
    @inertia

    <!-- PWA Install Banner Container -->
    <div id="pwa-install-banner" class="pwa-banner hidden">
        <div class="pwa-banner-content">
            <div class="pwa-logo-container">
                <img src="/logo.webp" alt="GauSeva Logo" class="pwa-logo" />
            </div>
            <div class="pwa-text-container">
                <div class="pwa-title">Install GauSeva App</div>
                <div class="pwa-desc">Access GauSeva instantly from your home screen offline!</div>
            </div>
            <div class="pwa-actions">
                <button id="pwa-install-btn" class="pwa-btn-install">Install</button>
                <button id="pwa-close-btn" class="pwa-btn-close">✕</button>
            </div>
        </div>
    </div>

    <!-- iOS Share Sheet Guide Modal (Floating Overlay) -->
    <div id="pwa-ios-modal" class="pwa-ios-modal hidden">
        <div class="pwa-ios-content">
            <button id="pwa-ios-close-btn" class="pwa-ios-close">✕</button>
            <div class="pwa-logo-container-large">
                <img src="/logo.webp" alt="GauSeva Logo" class="pwa-logo-large" />
            </div>
            <div class="pwa-ios-title">Install GauSeva on iOS</div>
            <div class="pwa-ios-desc">Follow these quick steps to add the app to your Home Screen:</div>
            
            <div class="pwa-ios-steps">
                <div class="pwa-ios-step">
                    <span class="pwa-step-num">1</span>
                    <span class="pwa-step-text">Tap the <strong>Share</strong> button at the bottom of Safari:</span>
                    <span class="pwa-share-icon">📤</span>
                </div>
                <div class="pwa-ios-step">
                    <span class="pwa-step-num">2</span>
                    <span class="pwa-step-text">Scroll down and tap <strong>Add to Home Screen</strong>:</span>
                    <span class="pwa-add-icon">➕</span>
                </div>
            </div>
            
            <button id="pwa-ios-ok-btn" class="pwa-btn-ok">Got it!</button>
        </div>
    </div>

    <!-- Social Link Floating Container -->
    <div id="social-floater" class="social-floater-container hidden">
        <!-- Instagram -->
        <a href="https://www.instagram.com/krishanbalramgaushala" target="_blank" rel="noopener noreferrer" class="social-icon-btn insta" title="Follow us on Instagram">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
            </svg>
        </a>

        <!-- Facebook -->
        <a href="https://www.facebook.com/profile.php?id=100067613164128" target="_blank" rel="noopener noreferrer" class="social-icon-btn fb" title="Follow us on Facebook">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
        </a>

        <!-- WhatsApp Chat -->
        <a href="https://wa.me/919216451217?text=Jai%20Shri%20Krishna%21%20I%20have%20a%20query%20regarding%20Krishan%20Balram%20Gaushala." target="_blank" rel="noopener noreferrer" class="social-icon-btn whatsapp" title="Chat with us on WhatsApp">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.458 5.704 1.459h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Detect if already running in standalone mode (installed)
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches || 
                                 window.navigator.standalone === true;
            
            const banner = document.getElementById('pwa-install-banner');
            const installBtn = document.getElementById('pwa-install-btn');
            const closeBtn = document.getElementById('pwa-close-btn');
            const iosModal = document.getElementById('pwa-ios-modal');
            const iosCloseBtn = document.getElementById('pwa-ios-close-btn');
            const iosOkBtn = document.getElementById('pwa-ios-ok-btn');

            let deferredPrompt = null;

            if (!isStandalone) {
                // 2. Check if user dismissed the banner recently
                if (localStorage.getItem('gauseva_pwa_dismissed') !== 'true') {
                    // 3. Detect Platform
                    const userAgent = window.navigator.userAgent.toLowerCase();
                    const isIOS = /iphone|ipad|ipod/.test(userAgent);
                    const isSafari = /safari/.test(userAgent) && !/crios|fxios|chrome|firefox/.test(userAgent);

                    // A. Catch the Chrome / Android install trigger
                    window.addEventListener('beforeinstallprompt', (e) => {
                        e.preventDefault();
                        deferredPrompt = e;
                        
                        if (banner) {
                            banner.classList.remove('hidden');
                            // Add smooth transition trigger
                            setTimeout(() => banner.classList.add('show'), 1500);
                        }
                    });

                    // B. Install button click handler
                    if (installBtn) {
                        installBtn.addEventListener('click', async () => {
                            if (isIOS) {
                                if (iosModal) {
                                    iosModal.classList.remove('hidden');
                                    setTimeout(() => iosModal.classList.add('show'), 50);
                                }
                            } else if (deferredPrompt) {
                                banner.classList.remove('show');
                                setTimeout(() => banner.classList.add('hidden'), 400);

                                deferredPrompt.prompt();
                                const { outcome } = await deferredPrompt.userChoice;
                                console.log(`[PWA] Install dialog outcome: ${outcome}`);
                                deferredPrompt = null;
                            } else {
                                alert("To install, tap your browser's menu option (e.g. three dots or share) and choose 'Add to Home screen' or 'Install app'.");
                            }
                        });
                    }

                    // C. Close banner handler
                    if (closeBtn) {
                        closeBtn.addEventListener('click', () => {
                            if (banner) {
                                banner.classList.remove('show');
                                setTimeout(() => banner.classList.add('hidden'), 400);
                            }
                            // Prevent showing it again for 7 days
                            localStorage.setItem('gauseva_pwa_dismissed', 'true');
                        });
                    }

                    // D. iOS modal controls
                    if (iosCloseBtn) {
                        iosCloseBtn.addEventListener('click', () => {
                            if (iosModal) {
                                iosModal.classList.remove('show');
                                setTimeout(() => iosModal.classList.add('hidden'), 300);
                            }
                        });
                    }
                    if (iosOkBtn) {
                        iosOkBtn.addEventListener('click', () => {
                            if (iosModal) {
                                iosModal.classList.remove('show');
                                setTimeout(() => iosModal.classList.add('hidden'), 300);
                            }
                            localStorage.setItem('gauseva_pwa_dismissed', 'true');
                        });
                    }

                    // E. Show iOS specific banner when using Safari on Apple devices
                    if (isIOS && isSafari) {
                        if (banner) {
                            banner.classList.remove('hidden');
                            setTimeout(() => banner.classList.add('show'), 2000);
                        }
                    }
                }
            } else {
                console.log('[PWA] Running in standalone mode. Install prompt skipped.');
            }

            // F. Show or Hide Social Floater depending on path (handles client-side Inertia transitions)
            const socialFloater = document.getElementById('social-floater');
            function updateFloaterVisibility() {
                const path = window.location.pathname;
                if (socialFloater) {
                    if (path.startsWith('/admin')) {
                        socialFloater.classList.add('hidden');
                    } else {
                        socialFloater.classList.remove('hidden');
                    }
                }
            }
            
            updateFloaterVisibility();
            document.addEventListener('inertia:navigate', updateFloaterVisibility);
            document.addEventListener('inertia:finish', updateFloaterVisibility);
        });
    </script>
</body>
</html>

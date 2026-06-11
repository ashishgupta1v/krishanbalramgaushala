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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Detect if already running in standalone mode (installed)
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches || 
                                 window.navigator.standalone === true;
            
            if (isStandalone) {
                console.log('[PWA] Running in standalone mode. Install prompt skipped.');
                return;
            }

            // 2. Check if user dismissed the banner recently
            if (localStorage.getItem('gauseva_pwa_dismissed') === 'true') {
                console.log('[PWA] User dismissed the install banner previously.');
                return;
            }

            // 3. Detect Platform
            const userAgent = window.navigator.userAgent.toLowerCase();
            const isIOS = /iphone|ipad|ipod/.test(userAgent);
            const isSafari = /safari/.test(userAgent) && !/crios|fxios|chrome|firefox/.test(userAgent);

            const banner = document.getElementById('pwa-install-banner');
            const installBtn = document.getElementById('pwa-install-btn');
            const closeBtn = document.getElementById('pwa-close-btn');
            const iosModal = document.getElementById('pwa-ios-modal');
            const iosCloseBtn = document.getElementById('pwa-ios-close-btn');
            const iosOkBtn = document.getElementById('pwa-ios-ok-btn');

            let deferredPrompt = null;

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
                        // General fallback instruction
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
        });
    </script>
</body>
</html>

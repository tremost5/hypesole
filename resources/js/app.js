import './bootstrap';

const manifestLink = document.querySelector('link[rel="manifest"]');

if ('serviceWorker' in navigator && window.isSecureContext && manifestLink?.href) {
    const serviceWorkerUrl = new URL('service-worker.js', manifestLink.href);
    const serviceWorkerScope = new URL('./', manifestLink.href);

    window.addEventListener('load', () => {
        navigator.serviceWorker.register(serviceWorkerUrl.href, { scope: serviceWorkerScope.pathname })
            .catch((error) => console.warn('HYPESOLE service worker registration failed:', error));
    });
}

let deferredInstallPrompt = null;
const installBanner = document.getElementById('pwa-install-banner');
const installButton = document.getElementById('pwa-install-button');
const closeInstall = document.getElementById('pwa-install-close');
const installTitle = document.getElementById('pwa-install-title');
const installDescription = document.getElementById('pwa-install-description');
const iosInstructions = document.getElementById('pwa-ios-instructions');
const installHelp = document.getElementById('pwa-install-help');
const dismissedUntilKey = 'hypesole-install-dismissed-until';
const installedKey = 'hypesole-installed';
const dismissalCooldown = 7 * 24 * 60 * 60 * 1000;
const userAgent = navigator.userAgent;
const isIos = /iphone|ipad|ipod/i.test(userAgent)
    || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
const isAndroid = /android/i.test(userAgent);
const isChromeOrEdge = /(?:Chrome|CriOS|Edg|EdgiOS|EdgA)\//i.test(userAgent);

function hideInstallBanner() {
    installBanner?.classList.add('hidden');
}

function readStorage(key) {
    try {
        return window.localStorage.getItem(key);
    } catch {
        return null;
    }
}

function writeStorage(key, value) {
    try {
        window.localStorage.setItem(key, value);
    } catch {
        // Keep install UI usable when browser storage is disabled.
    }
}

function isStandalone() {
    return window.matchMedia('(display-mode: standalone)').matches || navigator.standalone === true;
}

function isInstalled() {
    return isStandalone() || readStorage(installedKey) === '1';
}

function isDismissed() {
    const dismissedUntil = Number(readStorage(dismissedUntilKey) || 0);

    if (dismissedUntil && dismissedUntil <= Date.now()) {
        try {
            window.localStorage.removeItem(dismissedUntilKey);
        } catch {
            // Ignore unavailable storage.
        }

        return false;
    }

    return dismissedUntil > Date.now();
}

function setManualInstructions() {
    if (!installButton) return;

    if (installDescription) {
        installDescription.textContent = 'Ikuti petunjuk browser untuk menambahkan HYPESOLE ke perangkat.';
    }
    installButton.textContent = isIos ? 'CARA PASANG DI IPHONE' : 'CARA INSTALL MANUAL';

    if (isIos) {
        iosInstructions?.classList.remove('hidden');
        return;
    }

    if (installHelp) {
        installHelp.textContent = isAndroid && isChromeOrEdge
            ? 'Di Chrome Android, buka menu ⋮ lalu pilih Install app atau Tambahkan ke layar utama.'
            : !isAndroid && isChromeOrEdge
                ? 'Di Chrome/Edge desktop, pilih ikon Install di bilah alamat atau Install HYPESOLE dari menu browser.'
                : 'Buka menu browser dan pilih Install app atau Add to Home Screen jika tersedia.';
        installHelp.classList.remove('hidden');
    }
}

if (installBanner && !isInstalled() && !isDismissed() && isIos) {
    if (installTitle) installTitle.textContent = 'Tambahkan HYPESOLE ke Home Screen';
    if (installDescription) installDescription.textContent = 'Pasang aplikasi langsung dari Safari, tanpa APK.';
    if (installButton) installButton.textContent = 'CARA PASANG DI IPHONE';
    installBanner.classList.remove('hidden');
}

if (installBanner && !isInstalled() && !isIos && !isDismissed()) {
    window.setTimeout(() => {
        if (isInstalled() || isDismissed()) return;
        if (!deferredInstallPrompt && installBanner.classList.contains('hidden')) {
            if (installTitle) installTitle.textContent = 'Pasang HYPESOLE';
            setManualInstructions();
            installBanner.classList.remove('hidden');
        }
    }, 3500);
}

window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    if (!installBanner || isInstalled() || isDismissed()) return;

    deferredInstallPrompt = event;
    if (!isIos) {
        if (installTitle) installTitle.textContent = 'Install HYPESOLE';
        if (installDescription) installDescription.textContent = 'Akses cepat dari layar utama atau desktop.';
        if (installButton) installButton.textContent = 'INSTALL HYPESOLE';
        installBanner.classList.remove('hidden');
    }
});

installButton?.addEventListener('click', async () => {
    if (isIos) {
        iosInstructions?.classList.toggle('hidden');
        return;
    }
    if (!deferredInstallPrompt) {
        if (installHelp?.classList.contains('hidden')) setManualInstructions();
        else installHelp?.classList.add('hidden');
        return;
    }
    const installPrompt = deferredInstallPrompt;
    deferredInstallPrompt = null;

    try {
        await installPrompt.prompt();
        const choice = await installPrompt.userChoice;

        if (choice?.outcome === 'accepted') {
            writeStorage(installedKey, '1');
            hideInstallBanner();
        } else {
            setManualInstructions();
        }
    } catch {
        setManualInstructions();
    }
});

closeInstall?.addEventListener('click', () => {
    writeStorage(dismissedUntilKey, String(Date.now() + dismissalCooldown));
    hideInstallBanner();
});

window.addEventListener('appinstalled', () => {
    writeStorage(installedKey, '1');
    hideInstallBanner();
});

window.matchMedia('(display-mode: standalone)').addEventListener?.('change', (event) => {
    if (event.matches) hideInstallBanner();
});

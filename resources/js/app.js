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
const isStandalone = window.matchMedia('(display-mode: standalone)').matches || navigator.standalone === true;
const isIos = /iphone|ipad|ipod/i.test(navigator.userAgent)
    || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

function hideInstallBanner() {
    installBanner?.classList.add('hidden');
}

if (installBanner && !isStandalone && !sessionStorage.getItem('hypesole-install-dismissed') && isIos) {
    installTitle.textContent = 'Tambahkan HYPESOLE ke Home Screen';
    installDescription.textContent = 'Pasang aplikasi langsung dari Safari, tanpa APK.';
    installButton.textContent = 'CARA PASANG DI IPHONE';
    installBanner.classList.remove('hidden');
}

window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    deferredInstallPrompt = event;
    if (installBanner && !isStandalone && !isIos) {
        installTitle.textContent = 'Install HYPESOLE';
        installDescription.textContent = 'Akses cepat dari layar utama atau desktop.';
        installButton.textContent = 'INSTALL HYPESOLE';
        installBanner.classList.remove('hidden');
    }
});

installButton?.addEventListener('click', async () => {
    if (isIos) {
        iosInstructions?.classList.toggle('hidden');
        return;
    }
    if (!deferredInstallPrompt) return;
    await deferredInstallPrompt.prompt();
    const choice = await deferredInstallPrompt.userChoice;
    deferredInstallPrompt = null;
    if (choice?.outcome === 'accepted') hideInstallBanner();
});

closeInstall?.addEventListener('click', () => {
    sessionStorage.setItem('hypesole-install-dismissed', '1');
    hideInstallBanner();
});

window.addEventListener('appinstalled', hideInstallBanner);

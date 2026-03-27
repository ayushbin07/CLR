// Lightweight haptic helper; no-op if unsupported or reduced motion preferred
window.haptic = function (pattern = 10) {
  try {
    if (!('vibrate' in navigator)) return;
    if (
      window.matchMedia &&
      window.matchMedia('(prefers-reduced-motion: reduce)').matches
    )
      return;
    navigator.vibrate(pattern);
  } catch (e) {
    // ignore
  }
};

let deferredPrompt = null;
let installBanner = null;
let installBtn = null;
let installDismiss = null;
let manualInstallBtn = null;
let manualRequestPending = false;
let manualWaitTimer = null;
let installStatusEl = null;

function setInstallStatus(message, tone = 'info') {
  if (!installStatusEl) {
    installStatusEl = document.getElementById('pwa-install-status');
  }
  if (!installStatusEl) return;
  installStatusEl.textContent = message;
  installStatusEl.classList.remove('hidden');
  installStatusEl.style.color =
    tone === 'error'
      ? '#f87171'
      : tone === 'success'
      ? '#4ade80'
      : 'var(--text-muted)';
}

function showInstallBanner() {
  if (!deferredPrompt || !installBanner) return;
  installBanner.classList.remove('hidden');
  sessionStorage.removeItem('pwaPrompt');
}

document.addEventListener('DOMContentLoaded', () => {
  // Checkbox toggles
  const checkboxes = document.querySelectorAll('.custom-checkbox');
  checkboxes.forEach((cb) => {
    // Habits page manages its own checkbox state; skip there to avoid double toggles
    if (cb.dataset.habitId) return;
    cb.addEventListener('click', () => {
      cb.classList.toggle('checked');
      if (cb.classList.contains('checked')) {
        cb.innerHTML =
          '<span class="material-symbols-outlined text-xs font-bold text-[#0F0F12]">check</span>';
      } else {
        cb.innerHTML = '';
      }
    });
  });

  // Segmented control
  const segmentedItems = document.querySelectorAll('.segmented-item');
  segmentedItems.forEach((item) => {
    item.addEventListener('click', () => {
      segmentedItems.forEach((i) => {
        i.classList.remove('active', 'text-[var(--accent-purple)]');
        i.classList.add('text-[var(--text-muted)]');
      });
      item.classList.add('active', 'text-[var(--accent-purple)]');
      item.classList.remove('text-[var(--text-muted)]');
    });
  });

  // Reaction buttons
  const reactionBtns = document.querySelectorAll('.reaction-btn');
  reactionBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      reactionBtns.forEach((b) => {
        b.classList.remove('selected');
        b.classList.add('unselected');
      });
      btn.classList.add('selected');
      btn.classList.remove('unselected');
      window.haptic?.(12);
    });
  });

  installBanner = document.getElementById('pwa-install-banner');
  installBtn = document.getElementById('pwa-install-btn');
  installDismiss = document.getElementById('pwa-install-dismiss');
  manualInstallBtn = document.getElementById('pwa-install-trigger');
  installStatusEl = document.getElementById('pwa-install-status');

  const isStandalone =
    (window.matchMedia &&
      (window.matchMedia('(display-mode: standalone)').matches ||
        window.matchMedia('(display-mode: minimal-ui)').matches)) ||
    window.navigator.standalone;
  if (isStandalone) {
    installBanner?.classList.add('hidden');
    manualInstallBtn?.setAttribute('disabled', 'true');
    setInstallStatus('Sanctuary is already installed on this device.', 'success');
  }

  installBtn?.addEventListener('click', async () => {
    if (!deferredPrompt) return;
    installBanner?.classList.add('hidden');
    deferredPrompt.prompt();
    await deferredPrompt.userChoice;
    deferredPrompt = null;
    sessionStorage.removeItem('pwaPrompt');
  });

  installDismiss?.addEventListener('click', () => {
    installBanner?.classList.add('hidden');
    deferredPrompt = null;
    sessionStorage.removeItem('pwaPrompt');
  });

  manualInstallBtn?.addEventListener('click', async () => {
    if (!window.isSecureContext && window.location.hostname !== 'localhost') {
      setInstallStatus('Install requires HTTPS hosting. Please use https://', 'error');
      return;
    }
    if (!deferredPrompt) {
      manualRequestPending = true; // queue until the prompt event arrives
      setInstallStatus('Preparing the install prompt…', 'info');
      if (manualWaitTimer) clearTimeout(manualWaitTimer);
      manualWaitTimer = setTimeout(() => {
        setInstallStatus(
          'Install prompt not ready yet. Reload if it does not appear.',
          'error'
        );
      }, 3500);
      return;
    }
    try {
      installBanner?.classList.add('hidden');
      deferredPrompt.prompt();
      const choice = await deferredPrompt.userChoice;
      if (choice?.outcome === 'accepted') {
        sessionStorage.removeItem('pwaPrompt');
        setInstallStatus('Install dialog opened. Follow your browser prompt.', 'success');
      }
      deferredPrompt = null;
    } catch (err) {
      setInstallStatus('Install failed. Please try again.', 'error');
    }
  });

  showInstallBanner();
});

// PWA: register service worker and capture install prompt
function resolveServiceWorkerUrl() {
  const manifestEl = document.querySelector('link[rel="manifest"]');
  if (manifestEl?.getAttribute('href')) {
    const manifestUrl = new URL(manifestEl.getAttribute('href'), window.location.href);
    return new URL('service-worker.js', manifestUrl.href).href;
  }
  const pageUrl = new URL(window.location.href);
  pageUrl.pathname = pageUrl.pathname.replace(/\/[^/]*$/, '/service-worker.js');
  return pageUrl.href;
}

window.addEventListener('load', () => {
  if ('serviceWorker' in navigator) {
    const swUrl = resolveServiceWorkerUrl();
    const scopePath = new URL(swUrl).pathname.replace(/\/service-worker\.js$/, '/');
    navigator.serviceWorker
      .register(swUrl, { scope: scopePath })
      .catch((err) => {
        console.error('SW registration failed', err);
        setInstallStatus('Service worker registration failed; install not available.', 'error');
      });
  }
});
window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  deferredPrompt = e;
  setInstallStatus('Install prompt is ready.', 'info');
  if (manualWaitTimer) {
    clearTimeout(manualWaitTimer);
    manualWaitTimer = null;
  }
  if (manualRequestPending) {
    manualRequestPending = false;
    installBanner?.classList.add('hidden');
    deferredPrompt.prompt();
    setInstallStatus('Install dialog opened. Follow your browser prompt.', 'success');
  } else {
    showInstallBanner();
  }
});

window.addEventListener('appinstalled', () => {
  deferredPrompt = null;
  installBanner?.classList.add('hidden');
  setInstallStatus('Sanctuary installed! You can launch it from your home screen.', 'success');
});

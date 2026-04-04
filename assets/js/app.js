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
let manualInstallBtn = null;
let manualRequestPending = false;
let manualWaitTimer = null;

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

  manualInstallBtn = document.getElementById('pwa-install-trigger');
  manualInstallBtn?.addEventListener('click', async () => {
    if (!deferredPrompt) {
      manualRequestPending = true; // queue until the prompt event arrives
      if (manualWaitTimer) clearTimeout(manualWaitTimer);
      manualWaitTimer = setTimeout(() => {
        alert(
          'Still waiting for the install prompt. If it does not appear, reload and try again.'
        );
      }, 3000);
      return;
    }
    try {
      deferredPrompt.prompt();
      const choice = await deferredPrompt.userChoice;
      if (choice?.outcome === 'accepted') {
        sessionStorage.removeItem('pwaPrompt');
      }
      deferredPrompt = null;
    } catch (err) {
      alert('Install failed. Please try again.');
    }
  });

});

// PWA: register service worker and capture install prompt
window.addEventListener('load', () => {
  if ('serviceWorker' in navigator) {
    // Extract base path from current URL, accounting for nested project directories
    const url = new URL(window.location.href);
    const pathname = url.pathname;
    // Remove the current page filename to get the directory path
    const dirPath = pathname.substring(0, pathname.lastIndexOf('/') + 1);
    // Service worker must be at the root of the app scope
    const swUrl = `${dirPath}service-worker.js`;

    navigator.serviceWorker.register(swUrl, {
      scope: dirPath
    }).then((registration) => {
      console.log('Service Worker registered successfully:', registration.scope);
    }).catch((err) => {
      console.error('Service Worker registration failed:', err);
    });
  }
});
window.addEventListener('beforeinstallprompt', (e) => {
  console.log('beforeinstallprompt event fired');
  e.preventDefault();
  deferredPrompt = e;

  if (manualWaitTimer) {
    clearTimeout(manualWaitTimer);
    manualWaitTimer = null;
  }

  if (manualRequestPending) {
    console.log('Manual install request pending, triggering prompt');
    manualRequestPending = false;
    deferredPrompt.prompt();
  }
});

window.addEventListener('appinstalled', () => {
  console.log('App installed successfully');
  deferredPrompt = null;
});

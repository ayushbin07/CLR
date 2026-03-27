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

function showInstallBanner() {
  if (!deferredPrompt || !installBanner) return;
  installBanner.classList.remove('hidden');
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

  installBtn?.addEventListener('click', async () => {
    if (!deferredPrompt) return;
    installBanner?.classList.add('hidden');
    deferredPrompt.prompt();
    await deferredPrompt.userChoice;
    deferredPrompt = null;
  });

  installDismiss?.addEventListener('click', () => {
    installBanner?.classList.add('hidden');
    deferredPrompt = null;
  });

  showInstallBanner();
});

// PWA: register service worker and capture install prompt
window.addEventListener('load', () => {
  if ('serviceWorker' in navigator) {
    const swUrl = new URL('service-worker.js', document.baseURI).href;
    navigator.serviceWorker.register(swUrl).catch((err) => {
      console.error('SW registration failed', err);
    });
  }
});
window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  deferredPrompt = e;
  showInstallBanner();
});

window.addEventListener('appinstalled', () => {
  deferredPrompt = null;
  installBanner?.classList.add('hidden');
});

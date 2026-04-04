<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/layout.php';
requireAuth();
$user    = auth();
$csrf    = csrfToken();
$classes = db()->query('SELECT * FROM classes ORDER BY name')->fetchAll();
$avatarStyle = $user['avatar_style'] ?? 'avataaars';
$avatarText  = $user['avatar_text'] ?? $user['avatar_seed'] ?? $user['name'];
$dicebear    = 'https://api.dicebear.com/7.x/' . rawurlencode($avatarStyle) . '/svg?seed=' . urlencode($avatarText);
// Prefer real uploaded photo; fallback to DiceBear
$avatarPath  = $user['avatar_path'] ?? null;
$avatarSrc   = $avatarPath ? BASE_URL . '/' . $avatarPath . '?v=' . time() : $dicebear;

pageHead('Settings');
topNav('settings');
sidebar('settings');
bottomNav('settings');
?>
<main class="main-content">
    <div class="max-w-2xl mx-auto">
        <header class="px-6 pt-16 pb-10 lg:pt-20 lg:px-10 text-center lg:text-left">
            <h1 class="text-3xl lg:text-4xl font-bold tracking-tight">Settings</h1>
        </header>

        <!-- Success/Error flash -->
        <div id="settings-msg" class="hidden mx-6 lg:mx-10 mb-6 px-4 py-3 rounded-xl text-sm font-medium"></div>

        <div class="px-6 lg:px-10 space-y-12">


            <!-- Profile section border card -->
            <section>
                <h3 class="text-[11px] uppercase tracking-[0.2em] text-[var(--text-muted)] font-semibold mb-3 ml-1">Profile</h3>
                <div class="bg-[var(--card-dark)] rounded-[24px] border border-white/5 p-4 sm:p-5">
                    <div class="flex items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4 sm:gap-5">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full overflow-hidden shrink-0 avatar-glow relative">
                                <img id="avatar-hero"
                                    src="<?= htmlspecialchars($avatarSrc) ?>"
                                    alt="Profile"
                                    class="w-full h-full object-cover"
                                    onerror="this.onerror=null;this.src='<?= htmlspecialchars($dicebear) ?>'">
                            </div>
                            <input type="file" id="avatar-file-input" accept="image/jpeg,image/png,image/webp,image/gif" class="hidden">
                            <div>
                                <div class="flex items-center gap-2 mb-0.5">
                                    <h2 class="text-xl font-medium tracking-tight leading-tight"><?= htmlspecialchars($user['name']) ?></h2>
                                    <button type="button" id="avatar-upload-trigger"
                                        title="Upload profile photo"
                                        class="hidden inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/5 hover:bg-[var(--accent-purple)]/15 border border-white/10 hover:border-[var(--accent-purple)]/30 text-[var(--text-muted)] hover:text-[var(--accent-purple)] text-[11px] font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--accent-purple)]/50">
                                        <span class="material-symbols-outlined text-[14px]">photo_camera</span>
                                        Upload Photo
                                    </button>
                                </div>
                                <p class="text-[var(--text-muted)] text-sm mb-2"><?= htmlspecialchars($user['email']) ?></p>
                                <div class="flex flex-wrap items-center gap-2">
                                    <?php
                                    $className = '';
                                    foreach ($classes as $c) {
                                        if ($c['id'] == $user['class_id']) { $className = $c['name']; break; }
                                    }
                                    ?>
                                    <span class="px-2 py-0.5 rounded bg-white/5 border border-white/10 text-[10px] uppercase font-bold tracking-widest text-[var(--text-muted)] mt-0.5">
                                        <?= htmlspecialchars($className ?: 'No class') ?>
                                    </span>
                                    <?php if (($user['role'] ?? '') === 'admin'): ?>
                                    <a href="<?= BASE_URL ?>/admin.php" class="px-2 py-0.5 rounded bg-[rgba(168,162,255,0.15)] text-[10px] uppercase font-bold tracking-widest text-[var(--accent-purple)] hover:bg-[rgba(168,162,255,0.25)] transition-colors mt-0.5">
                                        <?= htmlspecialchars($user['role']) ?>
                                    </a>
                                    <?php else: ?>
                                    <span class="px-2 py-0.5 rounded bg-[rgba(168,162,255,0.15)] text-[10px] uppercase font-bold tracking-widest text-[var(--accent-purple)] mt-0.5">
                                        <?= htmlspecialchars($user['role']) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="edit-profile-btn" class="w-11 h-11 shrink-0 rounded-full bg-white/5 hover:bg-white/10 flex items-center justify-center transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--accent-purple)]/50">
                            <span id="edit-profile-icon" class="material-symbols-outlined text-[var(--text-soft)] text-xl">edit</span>
                        </button>
                    </div>

                    <form id="profile-form" class="hidden mt-6 pt-5 border-t border-white/5 space-y-4">
                        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                        <div>
                            <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Display Name</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required
                                class="w-full bg-[var(--bg-dark)] border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none focus:border-[var(--accent-purple)]/50"/>
                        </div>
                        <div>
                            <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Class</label>
                            <select name="class_id" class="w-full bg-[var(--bg-dark)] border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none focus:border-[var(--accent-purple)]/50">
                                <option value="">— No class —</option>
                                <?php foreach ($classes as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= $c['id'] == $user['class_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['name']) ?>
                                </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div id="avatar-controls" class="space-y-3 pt-2">
                            <h4 class="text-[11px] uppercase tracking-[0.2em] text-[var(--text-muted)] font-semibold mb-1">Avatar Settings</h4>
                            <div>
                                <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Avatar Text</label>
                                <div class="flex gap-3 items-center flex-wrap">
                                    <input type="text" name="avatar_text" id="avatar-text" value="<?= htmlspecialchars($avatarText) ?>" maxlength="100"
                                        class="flex-1 min-w-[180px] bg-[var(--bg-dark)] border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none mt-1 focus-visible:ring-2 focus-visible:ring-[var(--accent-purple)]/50 focus:border-[var(--accent-purple)]/50"/>
                                    <button type="button" class="px-3 py-3 rounded-xl bg-[var(--bg-dark)] border border-white/10 text-white text-sm hover:bg-white/5 transition-all mt-1 min-h-[44px] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--accent-purple)]/50" id="avatar-random">
                                        Shuffle
                                    </button>
                                </div>
                                <p class="text-[var(--text-muted)] text-[11px] mt-2">Use any short text to set your avatar. Shuffle gives you a quick random seed.</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 pt-1">
                                <label class="text-[var(--text-muted)] text-[11px] uppercase tracking-[0.2em] font-semibold">Style</label>
                                <select name="avatar_style" id="avatar-style" class="bg-[var(--bg-dark)] border border-white/10 rounded-xl px-3 py-2 text-sm text-white outline-none focus:border-[var(--accent-purple)]/50">
                                    <?php $styles = ['avataaars' => 'Avatars', 'bottts-neutral' => 'Bottts', 'pixel-art' => 'Pixel Art', 'thumbs' => 'Thumbs', 'identicon' => 'Identicon', 'fun-emoji' => 'Fun Emoji'];
                                    foreach ($styles as $value => $label): ?>
                                        <option value="<?= $value ?>" <?= ($avatarStyle === $value) ? 'selected' : '' ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mt-3 w-16 h-16 rounded-full overflow-hidden avatar-glow border border-white/5">
                                <img id="avatar-preview" src="<?= htmlspecialchars($avatarSrc) ?>" alt="Avatar preview" class="w-full h-full object-cover" />
                            </div>
                        </div>

                        <button type="submit" class="glassy-cta text-sm w-full py-3 mt-4 min-h-[44px] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--accent-purple)]/50">
                            Save Profile
                        </button>
                    </form>
                </div>
            </section>

            <!-- Change password -->
            <section>
                <h3 class="text-[11px] uppercase tracking-[0.2em] text-[var(--text-muted)] font-semibold mb-3 ml-1">Security</h3>
                <div class="bg-[var(--card-dark)] rounded-[24px] border border-white/5 p-2 sm:p-3">
                    <button type="button" id="toggle-password-btn" class="w-full flex items-center justify-between text-left p-3 sm:p-4 rounded-[18px] hover:bg-white/5 transition-colors group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--accent-purple)]/50">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-white/10 transition-colors">
                                <span class="material-symbols-outlined text-[var(--text-soft)]">lock</span>
                            </div>
                            <div>
                                <span class="block text-base font-semibold">Change Password</span>
                                <span class="block text-xs text-[var(--text-muted)] mt-0.5">Update your account security</span>
                            </div>
                        </div>
                        <span id="password-toggle-icon" class="material-symbols-outlined text-[var(--text-muted)] transition-transform duration-300">expand_more</span>
                    </button>

                    <form id="password-form" class="hidden px-3 sm:px-4 pb-4 pt-2 space-y-4 mt-2 border-t border-white/5">
                        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                        <div class="pt-2">
                            <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Current Password</label>
                            <input type="password" name="current_password" required
                                class="w-full bg-[var(--bg-dark)] border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none focus:border-[var(--accent-purple)]/50"
                                placeholder="••••••••"/>
                        </div>
                        <div>
                            <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">New Password</label>
                            <input type="password" name="new_password" required minlength="6"
                                class="w-full bg-[var(--bg-dark)] border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none focus:border-[var(--accent-purple)]/50"
                                placeholder="Min. 6 characters"/>
                        </div>
                        <div>
                            <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Confirm Password</label>
                            <input type="password" name="confirm_password" required
                                class="w-full bg-[var(--bg-dark)] border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none focus:border-[var(--accent-purple)]/50"
                                placeholder="Repeat new password"/>
                        </div>
                        <button type="submit" class="glassy-cta text-sm w-full py-3 mt-2 min-h-[44px] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--accent-purple)]/50">
                            Update Password
                        </button>
                    </form>
                </div>
            </section>


            <!-- Show Your Presence -->
            <section id="presence-section">
                <div class="flex items-center justify-between mb-3 ml-1 mr-1">
                    <h3 class="text-[11px] uppercase tracking-[0.2em] text-[var(--text-muted)] font-semibold">Show Your Presence</h3>
                    <button id="write-review-btn" type="button"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[var(--accent-purple)]/10 hover:bg-[var(--accent-purple)]/20 border border-[var(--accent-purple)]/20 text-[var(--accent-purple)] text-[11px] font-semibold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--accent-purple)]/50">
                        <span class="material-symbols-outlined text-[14px]">rate_review</span>
                        Write a Review
                    </button>
                </div>

                <!-- Write Review inline form (hidden by default) -->
                <div id="review-form-wrapper" class="hidden mb-4 bg-[var(--card-dark)] rounded-[20px] border border-white/5 p-4">
                    <textarea id="review-comment" maxlength="300" rows="3"
                        class="w-full bg-[var(--bg-dark)] border border-white/10 rounded-xl px-4 py-3 text-sm text-[var(--text-soft)] outline-none resize-none focus:border-[var(--accent-purple)]/50 focus-visible:ring-2 focus-visible:ring-[var(--accent-purple)]/40 placeholder:text-[var(--text-muted)]"
                        placeholder="Share your experience with the community…"></textarea>
                    <div class="flex items-center justify-between mt-3">
                        <span id="review-char-count" class="text-[10px] text-[var(--text-muted)]">0 / 300</span>
                        <div class="flex gap-2">
                            <button id="review-cancel-btn" type="button"
                                class="px-4 py-2 text-[var(--text-muted)] text-sm hover:text-white transition-colors rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/20">
                                Cancel
                            </button>
                            <button id="review-submit-btn" type="button"
                                class="glassy-cta px-5 py-2 text-sm min-h-[38px] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--accent-purple)]/50">
                                Post
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Wrapper with edge-fade via ::before/::after pseudo-elements -->
                <div class="reviews-fade-wrap">
                    <div id="reviews-outer">
                        <div id="reviews-track" class="flex gap-4 w-max py-8" style="animation: reviews-scroll 12s linear infinite;">
                            <div class="neo-card shrink-0 w-56 p-4 text-[var(--text-muted)] text-sm">
                                Loading reviews…
                            </div>
                        </div>
                    </div>
                </div>


            </section>

            <!-- Danger zone -->
            <section class="pt-4 text-center lg:text-left pb-8">
                <a href="<?= BASE_URL ?>/api/auth.php?action=logout"
                   class="inline-block text-[#FF453A] font-semibold text-base px-8 py-3 min-h-[44px] hover:opacity-80 active:scale-95 transition-all bg-[#FF453A]/10 rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#FF453A]/50">
                    Log Out
                </a>
            </section>
        </div>
    </div>
</main>

<script>
const CSRF = <?= json_encode($csrf) ?>;
const avatarInput = document.getElementById('avatar-text');
const avatarPreview = document.getElementById('avatar-preview');
const avatarHero = document.getElementById('avatar-hero');
const shuffleBtn = document.getElementById('avatar-random');
const avatarStyle = document.getElementById('avatar-style');

function avatarUrl(seed, style) {
    const safeSeed = seed || '<?= addslashes($user['name']) ?>';
    const chosenStyle = style || avatarStyle.value || 'avataaars';
    return `https://api.dicebear.com/7.x/${encodeURIComponent(chosenStyle)}/svg?seed=${encodeURIComponent(safeSeed)}`;
}

function updateAvatar(seed = avatarInput.value.trim(), style = avatarStyle.value) {
    const url = avatarUrl(seed, style);
    avatarPreview.src = url;
    avatarHero.src = url;
}

avatarInput.addEventListener('input', () => updateAvatar());
avatarStyle.addEventListener('change', () => updateAvatar());
shuffleBtn.addEventListener('click', () => {
    const seed = 'user-' + Math.random().toString(36).slice(2, 8);
    avatarInput.value = seed;
    updateAvatar(seed, avatarStyle.value);
});

function closeProfileForm() {
    const form = document.getElementById('profile-form');
    const icon = document.getElementById('edit-profile-icon');
    const uploadBtn = document.getElementById('avatar-upload-trigger');
    form.classList.add('hidden');
    uploadBtn.classList.add('hidden');
    icon.textContent = 'edit';
}

document.getElementById('edit-profile-btn').addEventListener('click', () => {
    const form = document.getElementById('profile-form');
    const icon = document.getElementById('edit-profile-icon');
    const uploadBtn = document.getElementById('avatar-upload-trigger');
    const isHidden = form.classList.toggle('hidden');
    // Toggle upload button with form
    uploadBtn.classList.toggle('hidden', isHidden);
    icon.textContent = isHidden ? 'edit' : 'close';
});

function showMsg(msg, isError = true) {
    const box = document.getElementById('settings-msg');
    box.textContent = msg;
    box.className = isError
        ? 'mx-6 lg:mx-10 mb-6 px-4 py-3 rounded-xl text-sm font-medium bg-red-500/10 text-red-400 border border-red-500/20'
        : 'mx-6 lg:mx-10 mb-6 px-4 py-3 rounded-xl text-sm font-medium bg-green-500/10 text-green-400 border border-green-500/20';
    setTimeout(() => box.classList.add('hidden'), 4000);
}

document.getElementById('profile-form').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    const res = await fetch('<?= BASE_URL ?>/api/settings.php?action=update_profile', { method:'POST', body:fd });
    const d   = await res.json();
    if (d.success) {
        showMsg('Profile updated!', false);
        closeProfileForm(); // Auto-close form on success
    } else {
        showMsg(d.error || 'Error');
    }
});

document.getElementById('password-form').addEventListener('submit', async e => {
    e.preventDefault();
    const popup = e.target;
    const btn = popup.querySelector('button[type="submit"]');
    const fd = new FormData(popup);
    const res = await fetch('<?= BASE_URL ?>/api/settings.php?action=change_password', { method:'POST', body:fd });
    const d   = await res.json();
    if (d.success) {
        showMsg('Password changed!', false);
        popup.reset();
        // optionally fold it back up
        document.getElementById('toggle-password-btn').click();
    } else {
        showMsg(d.error || 'Error');
    }
});

document.getElementById('toggle-password-btn').addEventListener('click', () => {
    const form = document.getElementById('password-form');
    const icon = document.getElementById('password-toggle-icon');
    form.classList.toggle('hidden');
    icon.textContent = form.classList.contains('hidden') ? 'expand_more' : 'expand_less';
});

// ── Avatar Photo Upload ─────────────────────────────────────────────────────
const uploadTrigger = document.getElementById('avatar-upload-trigger');
const fileInput     = document.getElementById('avatar-file-input');

uploadTrigger.addEventListener('click', () => fileInput.click());

fileInput.addEventListener('change', async () => {
    const file = fileInput.files[0];
    if (!file) return;

    // Client-side size guard (5MB) with the same funny message
    const MAX = 5 * 1024 * 1024;
    if (file.size > MAX) {
        const mb = (file.size / 1024 / 1024).toFixed(1);
        showMsg(`Bro, ${mb}MB?? 💀 We said 5MB max. Do you compress photos or just YOLO them straight from the camera? Try again!`);
        fileInput.value = '';
        return;
    }

    // Optimistic preview while uploading
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('avatar-hero').src = e.target.result;
        if (avatarPreview) avatarPreview.src = e.target.result;
    };
    reader.readAsDataURL(file);

    const fd = new FormData();
    fd.append('avatar', file);
    fd.append('csrf_token', '<?= $csrf ?>');

    try {
        const res = await fetch('<?= BASE_URL ?>/api/upload_avatar.php', { method: 'POST', body: fd });
        const d   = await res.json();
        if (d.success) {
            showMsg('Photo uploaded! Looking fresh 🔥', false);
            // Update with server URL so it has cache-buster
            document.getElementById('avatar-hero').src = d.avatar_url;
            if (avatarPreview) avatarPreview.src = d.avatar_url;
        } else {
            showMsg(d.error || 'Upload failed.');
            // Revert preview on error
            document.getElementById('avatar-hero').src = '<?= htmlspecialchars($avatarSrc) ?>';
        }
    } catch {
        showMsg('Upload failed — check your connection.');
    }

    fileInput.value = ''; // Reset so the same file can be re-selected
});
</script>

<style>
/* Reviews marquee */
#presence-section {
    box-shadow: none;
}
/* Only the outer section shell gets no shadow — neo-card children keep theirs */

/* Marquee: continuous linear scroll */
@keyframes reviews-scroll {
    from { transform: translateX(0); }
    to   { transform: translateX(-50%); }
}

#reviews-track {
    will-change: transform;
}

/* overflow-x: clip — clips the scroll overflow but does NOT clip Y axis,
   so card box-shadows are free to bleed upward/downward */
#reviews-outer {
    overflow-x: clip;
    overflow-y: visible;
}

/* Edge fades: pseudo-elements sit on top, colour matches --bg (#f8fafc) */
.reviews-fade-wrap {
    position: relative;
}
.reviews-fade-wrap::before,
.reviews-fade-wrap::after {
    content: '';
    position: absolute;
    top: 0; bottom: 0;
    width: 80px;
    z-index: 10;
    pointer-events: none;
}
.reviews-fade-wrap::before {
    left: 0;
    background: linear-gradient(to right, #f2f4fb 0%, transparent 100%);
}
.reviews-fade-wrap::after {
    right: 0;
    background: linear-gradient(to left, #f2f4fb 0%, transparent 100%);
}

/* Pause on hover (animation-guide.md §7 — touch alternative: pause) */
#reviews-outer:hover #reviews-track {
    animation-play-state: paused;
}

/* Respect reduced-motion (animation-guide.md §9) */
@media (prefers-reduced-motion: reduce) {
    #reviews-track {
        animation: none !important;
    }
    #reviews-outer {
        overflow-x: auto;
    }
}

/* Write Review form: standard enter transition (200-300ms ease-out, animation-guide.md §1) */
#review-form-wrapper {
    transition: opacity 200ms ease-out, transform 200ms ease-out;
}
#review-form-wrapper.hidden {
    opacity: 0;
    transform: translateY(-6px);
    pointer-events: none;
}
</style>

<script>
// ── Reviews: Show Your Presence ──────────────────────────────────────────────
const BASE_REV      = <?= json_encode(BASE_URL) ?>;
const CSRF_REV      = <?= json_encode($csrf) ?>;
const IS_ADMIN_REV  = <?= json_encode(($user['role'] ?? '') === 'admin') ?>;

function reviewAvatarUrl(r) {
    if (r.avatar_path) return BASE_REV + '/' + r.avatar_path;
    const seed  = r.avatar_text || r.avatar_seed || r.user_name || 'user';
    const style = r.avatar_style || 'avataaars';
    return `https://api.dicebear.com/7.x/${encodeURIComponent(style)}/svg?seed=${encodeURIComponent(seed)}`;
}

function renderReviewCard(r) {
    const avatarSrc = reviewAvatarUrl(r);
    const dicebear  = `https://api.dicebear.com/7.x/${encodeURIComponent(r.avatar_style||'avataaars')}/svg?seed=${encodeURIComponent(r.avatar_text||r.user_name||'user')}`;
    const deleteBtn = IS_ADMIN_REV
        ? `<button class="rev-delete mt-2 text-[10px] text-red-400/60 hover:text-red-400 transition-colors focus-visible:outline-none" data-id="${r.id}">Delete</button>`
        : '';
    return `
        <div class="neo-card shrink-0 w-64 p-4 flex flex-col gap-3" data-review-id="${r.id}">
            <div class="flex items-center gap-3">
                <img src="${avatarSrc}" onerror="this.onerror=null;this.src='${dicebear}'"
                     alt="${r.user_name}" class="w-9 h-9 rounded-full object-cover border border-white/10 shrink-0">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-[var(--text-soft)] truncate">${r.user_name}</p>
                    <p class="text-[10px] text-[var(--text-muted)] truncate">${r.class_name || 'No class'}</p>
                </div>
            </div>
            <p class="text-[var(--text-muted)] text-[13px] leading-relaxed line-clamp-3">${escReview(r.comment)}</p>
            ${deleteBtn}
        </div>`;
}

function escReview(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

async function loadReviews() {
    const track = document.getElementById('reviews-track');
    try {
        const res  = await fetch(`${BASE_REV}/api/reviews.php?action=list`);
        const data = await res.json();
        if (!data.length) {
            track.innerHTML = `<div class="shrink-0 w-64 p-4 rounded-2xl bg-[var(--card-dark)] border border-white/5 text-[var(--text-muted)] text-sm flex items-center justify-center">No reviews yet — be the first! 🌟</div>`;
            track.style.animation = 'none';
            return;
        }
        // Render cards + duplicate for seamless infinite loop
        const cardsHtml = data.map(renderReviewCard).join('');
        track.innerHTML = cardsHtml + cardsHtml; // Duplicate = seamless loop at -50%

        // Admin: bind delete buttons
        if (IS_ADMIN_REV) bindDeleteReviews();
    } catch {
        track.innerHTML = `<div class="shrink-0 w-64 p-4 rounded-2xl bg-[var(--card-dark)] text-red-400 text-sm">Failed to load reviews.</div>`;
        track.style.animation = 'none';
    }
}

function bindDeleteReviews() {
    document.querySelectorAll('.rev-delete').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            const fd = new FormData();
            fd.append('id', id);
            fd.append('csrf_token', CSRF_REV);
            const res = await fetch(`${BASE_REV}/api/reviews.php?action=delete`, { method: 'POST', body: fd });
            const d   = await res.json();
            if (d.success) loadReviews();
            else showMsg(d.error || 'Delete failed.');
        });
    });
}

// Write Review toggle
const writeBtn      = document.getElementById('write-review-btn');
const revFormWrap   = document.getElementById('review-form-wrapper');
const revComment    = document.getElementById('review-comment');
const revCharCount  = document.getElementById('review-char-count');
const revCancelBtn  = document.getElementById('review-cancel-btn');
const revSubmitBtn  = document.getElementById('review-submit-btn');

// Micro-interaction: smooth reveal (200ms ease-out per animation-guide §1)
function showReviewForm() {
    revFormWrap.classList.remove('hidden');
    revComment.focus();
}
function hideReviewForm() {
    revFormWrap.classList.add('hidden');
    revComment.value = '';
    revCharCount.textContent = '0 / 300';
}

writeBtn.addEventListener('click', showReviewForm);
revCancelBtn.addEventListener('click', hideReviewForm);

revComment.addEventListener('input', () => {
    revCharCount.textContent = `${revComment.value.length} / 300`;
});

revSubmitBtn.addEventListener('click', async () => {
    const comment = revComment.value.trim();
    if (!comment) { showMsg('Write something first!'); return; }

    revSubmitBtn.disabled = true;
    revSubmitBtn.textContent = 'Posting…';

    const fd = new FormData();
    fd.append('comment', comment);
    fd.append('csrf_token', CSRF_REV);
    const res = await fetch(`${BASE_REV}/api/reviews.php?action=add`, { method: 'POST', body: fd });
    const d   = await res.json();

    revSubmitBtn.disabled = false;
    revSubmitBtn.textContent = 'Post';

    if (d.success) {
        showMsg('Review posted! Thanks for sharing 🙌', false);
        hideReviewForm();
        loadReviews(); // Refresh marquee
    } else {
        showMsg(d.error || 'Failed to post.');
    }
});

// Initial load
loadReviews();
</script>
<?php pageFooter(); ?>

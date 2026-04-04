<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/layout.php';
requireAuth();
$user    = auth();
$csrf    = csrfToken();
$classes = db()->query('SELECT * FROM classes ORDER BY name')->fetchAll();
$avatarStyle = $user['avatar_style'] ?? 'avataaars';
$avatarText  = $user['avatar_text'] ?? $user['avatar_seed'] ?? $user['name'];
$dicebear  = 'https://api.dicebear.com/7.x/' . rawurlencode($avatarStyle) . '/svg?seed=' . urlencode($avatarText);
$avatarSrc = $dicebear;

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
                                <img id="avatar-hero" src="<?= htmlspecialchars($avatarSrc) ?>" alt="Profile" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h2 class="text-xl font-medium tracking-tight leading-tight"><?= htmlspecialchars($user['name']) ?></h2>
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

document.getElementById('edit-profile-btn').addEventListener('click', () => {
    const form = document.getElementById('profile-form');
    const icon = document.getElementById('edit-profile-icon');
    form.classList.toggle('hidden');
    icon.textContent = form.classList.contains('hidden') ? 'edit' : 'close';
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
    d.success ? showMsg('Profile updated!', false) : showMsg(d.error || 'Error');
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
</script>
<?php pageFooter(); ?>

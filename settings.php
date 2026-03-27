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


            <!-- Profile section -->
            <section id="avatar" class="flex items-center gap-5">
                <div class="w-20 h-20 rounded-full overflow-hidden shrink-0 avatar-glow relative group">
                    <img id="avatar-hero" src="<?= htmlspecialchars($avatarSrc) ?>"
                         alt="Profile" class="w-full h-full object-cover">
                    <button type="button" id="avatar-pencil" class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition">
                        <span class="material-symbols-outlined text-white text-xl">edit</span>
                    </button>
                </div>
                <div>
                    <h2 class="text-xl font-medium tracking-tight"><?= htmlspecialchars($user['name']) ?></h2>
                    <p class="text-[var(--text-muted)] text-sm mb-1"><?= htmlspecialchars($user['email']) ?></p>
                    <div class="flex items-center gap-2">
                        <?php
                        $className = '';
                        foreach ($classes as $c) {
                            if ($c['id'] == $user['class_id']) { $className = $c['name']; break; }
                        }
                        ?>
                        <span class="px-2 py-0.5 rounded bg-white/5 border border-white/10 text-[10px] uppercase font-bold tracking-widest text-[var(--text-muted)]">
                            <?= htmlspecialchars($className ?: 'No class') ?>
                        </span>
                        <?php if (($user['role'] ?? '') === 'admin'): ?>
                        <a href="<?= BASE_URL ?>/admin.php"
                           class="px-2 py-0.5 rounded bg-[rgba(168,162,255,0.15)] text-[10px] uppercase font-bold tracking-widest text-[var(--accent-purple)] hover:bg-[rgba(168,162,255,0.25)] transition-colors">
                            <?= htmlspecialchars($user['role']) ?>
                        </a>
                        <?php else: ?>
                        <span class="px-2 py-0.5 rounded bg-[rgba(168,162,255,0.15)] text-[10px] uppercase font-bold tracking-widest text-[var(--accent-purple)]">
                            <?= htmlspecialchars($user['role']) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- Update profile -->
            <section>
                <h3 class="text-[11px] uppercase tracking-[0.2em] text-[var(--text-muted)] font-semibold mb-3 ml-1">Profile</h3>
                <form id="profile-form" class="bg-[var(--card-dark)] rounded-[24px] border border-white/5 p-5 space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                    <div id="avatar-controls" class="space-y-3 hidden">
                        <div>
                            <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Avatar Text</label>
                            <div class="flex gap-3 items-center flex-wrap">
                                <input type="text" name="avatar_text" id="avatar-text" value="<?= htmlspecialchars($avatarText) ?>" maxlength="100"
                                    class="flex-1 min-w-[180px] bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none focus:border-[var(--accent-purple)]/50" disabled/>
                                <button type="button" class="px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-white text-sm hover:bg-white/10 transition-all" id="avatar-random" disabled>
                                    Shuffle
                                </button>
                            </div>
                            <p class="text-[var(--text-muted)] text-[11px] mt-1">Use any short text to set your avatar. Shuffle gives you a quick random seed.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <label class="text-[var(--text-muted)] text-[11px] uppercase tracking-[0.2em] font-semibold">Style</label>
                            <select name="avatar_style" id="avatar-style" class="bg-[var(--bg-dark)] border border-white/10 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-[var(--accent-purple)]/50" disabled>
                                <?php $styles = ['avataaars' => 'Avatars', 'bottts-neutral' => 'Bottts', 'pixel-art' => 'Pixel Art', 'thumbs' => 'Thumbs', 'identicon' => 'Identicon', 'fun-emoji' => 'Fun Emoji'];
                                foreach ($styles as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= ($avatarStyle === $value) ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mt-1 w-16 h-16 rounded-full overflow-hidden avatar-glow border border-white/5">
                            <img id="avatar-preview" src="<?= htmlspecialchars($avatarSrc) ?>" alt="Avatar preview" class="w-full h-full object-cover" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Display Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none focus:border-[var(--accent-purple)]/50"/>
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
                    <button type="submit" class="glassy-cta text-sm">
                        Save Profile
                    </button>
                </form>
            </section>

            <!-- Change password -->
            <section>
                <h3 class="text-[11px] uppercase tracking-[0.2em] text-[var(--text-muted)] font-semibold mb-3 ml-1">Security</h3>
                <form id="password-form" class="bg-[var(--card-dark)] rounded-[24px] border border-white/5 p-5 space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                    <div>
                        <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Current Password</label>
                        <input type="password" name="current_password" required
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none focus:border-[var(--accent-purple)]/50"
                            placeholder="••••••••"/>
                    </div>
                    <div>
                        <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">New Password</label>
                        <input type="password" name="new_password" required minlength="6"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none focus:border-[var(--accent-purple)]/50"
                            placeholder="Min. 6 characters"/>
                    </div>
                    <div>
                        <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Confirm Password</label>
                        <input type="password" name="confirm_password" required
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none focus:border-[var(--accent-purple)]/50"
                            placeholder="Repeat new password"/>
                    </div>
                    <button type="submit"
                        class="w-full py-3 bg-white/5 border border-white/10 text-white rounded-xl font-bold text-sm hover:bg-white/10 transition-all">
                        Change Password
                    </button>
                </form>
            </section>

                        <!-- PWA install -->
            <section>
                <h3 class="text-[11px] uppercase tracking-[0.2em] text-[var(--text-muted)] font-semibold mb-3 ml-1">App</h3>
                <div class="bg-[var(--card-dark)] rounded-[24px] border border-white/5 p-5 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white font-semibold">Install Sanctuary</p>
                        <p class="text-[var(--text-muted)] text-xs">Add the app to your device for a fullscreen experience.</p>
                    </div>
                    <button type="button" id="pwa-install-trigger" class="glassy-cta text-sm">Install</button>
                </div>
            </section>

            <!-- Danger zone -->
            <section class="pt-4 text-center lg:text-left pb-8">
                <a href="<?= BASE_URL ?>/api/auth.php?action=logout"
                   class="inline-block text-[#FF453A] font-semibold text-base px-8 py-2 hover:opacity-80 active:scale-95 transition-all bg-[#FF453A]/10 rounded-xl">
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
const avatarPencil = document.getElementById('avatar-pencil');
const avatarControls = document.getElementById('avatar-controls');
const avatarFields = [avatarInput, avatarStyle, shuffleBtn];

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
function unlockAvatar() {
    avatarControls.classList.remove('hidden');
    avatarFields.forEach(el => { el.disabled = false; });
    avatarInput.focus();
}

avatarPencil.addEventListener('click', unlockAvatar);

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
    const fd = new FormData(e.target);
    const res = await fetch('<?= BASE_URL ?>/api/settings.php?action=change_password', { method:'POST', body:fd });
    const d   = await res.json();
    if (d.success) {
        showMsg('Password changed!', false);
        e.target.reset();
    } else {
        showMsg(d.error || 'Error');
    }
});
</script>
<?php pageFooter(); ?>

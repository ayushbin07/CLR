<?php
require_once __DIR__ . '/includes/config.php';

// Already logged in
if (auth()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

// Fetch classes for register form
$classes = db()->query('SELECT * FROM classes ORDER BY name ASC')->fetchAll();
$csrf    = csrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Sanctuary | Login</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link rel="manifest" href="<?= BASE_URL ?>/manifest.json">
    <link rel="apple-touch-icon" href="<?= BASE_URL ?>/assets/icons/app-icon.png">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
    <style>
        .auth-glow { box-shadow: 0 0 80px rgba(168,162,255,0.08); }
        .input-field {
            width: 100%;
            background: #eef1f6;
            border: 1px solid rgba(0,0,0,0.06);
            border-radius: 12px;
            padding: 12px 16px;
            color: var(--text-soft);
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            box-shadow: 0 10px 22px rgba(17, 24, 39, 0.08), inset 0 1px 0 rgba(255,255,255,0.8);
        }
        .input-field:focus {
            border-color: rgba(46,193,106,0.35);
            box-shadow: 0 12px 26px rgba(17, 24, 39, 0.12), 0 0 0 3px rgba(46,193,106,0.08);
        }
        .input-field::placeholder { color: var(--text-muted); }
        select.input-field option { background: #eef1f6; color: var(--text-soft); }
        .tab-btn { transition: all 0.2s; }
        .tab-btn.active { color: var(--text-soft); border-bottom: 2px solid var(--accent-purple); }
        .tab-btn:not(.active) { color: var(--text-muted); border-bottom: 2px solid transparent; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold tracking-tighter text-[var(--text-soft)] mb-1">Sanctuary</h1>
            <p class="text-[var(--text-muted)] text-sm">Your student focus system</p>
        </div>

        <div class="bg-[var(--card-dark)] rounded-[28px] border border-white/5 p-8 auth-glow">
            <!-- Tabs -->
            <div class="flex gap-6 mb-8 border-b border-white/5 pb-1">
                <button class="tab-btn active text-sm font-semibold pb-3" data-tab="login">Sign In</button>
                <button class="tab-btn text-sm font-semibold pb-3" data-tab="register">Create Account</button>
            </div>

            <!-- Error/Success banner -->
            <div id="auth-msg" class="hidden mb-5 px-4 py-3 rounded-xl text-sm font-medium"></div>

            <!-- LOGIN FORM -->
            <form id="login-form" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <input type="hidden" name="action" value="login">
                <div>
                    <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Email</label>
                    <input type="email" name="email" class="input-field" placeholder="you@university.edu" required autocomplete="email"/>
                </div>
                <div>
                    <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Password</label>
                    <input type="password" name="password" class="input-field" placeholder="••••••••" required autocomplete="current-password"/>
                </div>
                <button type="submit" class="glassy-cta text-sm mt-2">
                    Sign In
                </button>
                <p class="text-center text-[var(--text-muted)] text-xs pt-1">
                    Demo: <span class="text-[var(--text-soft)]">ayush@sanctuary.dev</span> / <span class="text-[var(--text-soft)]">student123</span>
                </p>
            </form>

            <!-- REGISTER FORM -->
            <form id="register-form" class="space-y-4 hidden">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <input type="hidden" name="action" value="register">
                <div>
                    <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Full Name</label>
                    <input type="text" name="name" class="input-field" placeholder="Your name" required/>
                </div>
                <div>
                    <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Email</label>
                    <input type="email" name="email" class="input-field" placeholder="you@university.edu" required/>
                </div>
                <div>
                    <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Class</label>
                    <select name="class_id" class="input-field">
                        <option value="">— Select class —</option>
                        <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Password</label>
                    <input type="password" name="password" class="input-field" placeholder="Min. 6 characters" required/>
                </div>
                <div>
                    <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Avatar Text (optional)</label>
                    <input type="text" name="avatar_text" class="input-field" placeholder="e.g. chill-fox" maxlength="100"/>
                    <p class="text-[var(--text-muted)] text-[11px] mt-1">We use Dicebear avatars. Leave empty to use your name, or enter a short word to change the look.</p>
                </div>
                <div>
                    <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Avatar Style</label>
                    <select name="avatar_style" class="input-field">
                        <option value="avataaars">Avatars</option>
                        <option value="bottts-neutral">Bottts</option>
                        <option value="pixel-art">Pixel Art</option>
                        <option value="thumbs">Thumbs</option>
                        <option value="identicon">Identicon</option>
                        <option value="fun-emoji">Fun Emoji</option>
                    </select>
                </div>
                <button type="submit" class="glassy-cta text-sm mt-2">
                    Create Account
                </button>
            </form>
        </div>
    </div>

    <script>
    const tabs   = document.querySelectorAll('.tab-btn');
    const forms  = { login: document.getElementById('login-form'), register: document.getElementById('register-form') };
    const msgBox = document.getElementById('auth-msg');

    tabs.forEach(btn => btn.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        btn.classList.add('active');
        const tab = btn.dataset.tab;
        forms.login.classList.toggle('hidden', tab !== 'login');
        forms.register.classList.toggle('hidden', tab !== 'register');
        msgBox.classList.add('hidden');
    }));

    function showMsg(msg, isError = true) {
        msgBox.textContent = msg;
        msgBox.className = isError
            ? 'mb-5 px-4 py-3 rounded-xl text-sm font-medium bg-red-500/10 text-red-400 border border-red-500/20'
            : 'mb-5 px-4 py-3 rounded-xl text-sm font-medium bg-green-500/10 text-green-400 border border-green-500/20';
    }

    async function submitForm(form, url) {
        const btn = form.querySelector('button[type=submit]');
        btn.disabled = true;
        btn.textContent = 'Please wait…';
        try {
            const res  = await fetch(url, { method: 'POST', body: new FormData(form) });
            const data = await res.json();
            if (data.success) {
                showMsg('Success! Redirecting…', false);
                sessionStorage.setItem('pwaPrompt', '1');
                window.location.href = data.redirect;
            } else {
                showMsg(data.error || 'Something went wrong');
                btn.disabled = false;
                btn.textContent = form.id === 'login-form' ? 'Sign In' : 'Create Account';
            }
        } catch {
            showMsg('Network error. Try again.');
            btn.disabled = false;
        }
    }

    forms.login.addEventListener('submit', e => { e.preventDefault(); submitForm(forms.login, '<?= BASE_URL ?>/api/auth.php?action=login'); });
    forms.register.addEventListener('submit', e => { e.preventDefault(); submitForm(forms.register, '<?= BASE_URL ?>/api/auth.php?action=register'); });
    </script>
    <script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>

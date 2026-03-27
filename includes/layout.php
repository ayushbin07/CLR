<?php
// includes/layout.php
// Shared header/footer/nav partials

function pageHead(string $title): void {
    $appName = APP_NAME;
    $base    = BASE_URL;
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{$appName} | {$title}</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="{$base}/assets/css/styles.css">
    <link rel="manifest" href="{$base}/manifest.json">
    <link rel="apple-touch-icon" href="{$base}/assets/icons/app-icon.png">
    <meta name="theme-color" content="#0F1F1A" />
</head>
<body class="min-h-screen">
HTML;
}

function topNav(string $activePage): void {
    // Desktop top nav intentionally omitted (sidebar handles navigation)
    return;
}

function sidebar(string $activePage): void {
    $user = auth();
    $seed = htmlspecialchars($user['avatar_text'] ?? $user['avatar_seed'] ?? ($user['name'] ?? 'User'));
    $style = htmlspecialchars($user['avatar_style'] ?? 'avataaars');
    $base = BASE_URL;
    $pages = [
        'index'      => ['home',        'Home',        'index.php'],
        'assignment' => ['assignment',  'Assignments', 'assignment.php'],
        'habits'     => ['auto_awesome','Habits',      'habits.php'],
        'mess'       => ['restaurant',  'Mess',        'mess.php'],
    ];
    $links = '';
    foreach ($pages as $key => [$icon, $label, $href]) {
        $active = $key === $activePage
            ? 'flex items-center space-x-3 px-4 py-3 bg-[rgba(168,162,255,0.15)] text-[var(--accent-purple)] rounded-xl shadow-lg shadow-[#8E5CF6]/10'
            : 'flex items-center space-x-3 px-4 py-3 text-[var(--text-muted)] hover:bg-[rgba(255,255,255,0.05)] hover:translate-x-1 rounded-xl transition-all duration-300';
        $links .= "<a href=\"" . BASE_URL . "/{$href}\" class=\"{$active}\"><span class=\"material-symbols-outlined\">{$icon}</span><span class=\"font-medium text-sm\">{$label}</span></a>\n";
    }
    echo <<<HTML
<aside class="sidebar w-64 fixed left-0 top-0 h-screen bg-[var(--card-dark)] border-r border-[var(--border-subtle)] p-6 flex-col hidden lg:flex z-40">
    <div class="mb-8 px-2 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full overflow-hidden border border-white/10 relative group">
            <img src="https://api.dicebear.com/7.x/{$style}/svg?seed={$seed}" alt="Avatar" class="w-full h-full object-cover">
            <a href="{$base}/settings.php#avatar" class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition bg-black/40 text-white text-xs">
                <span class="material-symbols-outlined text-[16px]">edit</span>
            </a>
        </div>
        <div>
            <h2 class="text-sm font-semibold text-white leading-tight">{$user['name']}</h2>
            <p class="text-[11px] text-[var(--text-muted)] leading-tight">{$user['email']}</p>
        </div>
    </div>
    <nav class="flex-1 space-y-2">{$links}</nav>
    <div class="mt-auto space-y-3 pt-6">
        <a href="{$base}/settings.php" class="neo-nav-pill">
            <span class="material-symbols-outlined text-sm">settings</span>
            <span class="text-xs font-semibold">Settings</span>
        </a>
        <a href="{$base}/api/auth.php?action=logout" class="neo-nav-pill neo-nav-pill--danger">
            <span class="material-symbols-outlined text-sm">logout</span>
            <span class="text-xs font-semibold">Logout</span>
        </a>
    </div>
</aside>
HTML;
}

function bottomNav(string $activePage): void {
    $base = BASE_URL;
    $pages = [
        'index'      => ['home',        'Home',        'index.php'],
        'assignment' => ['assignment',  'Assignments', 'assignment.php'],
        'habits'     => ['auto_awesome','Habits',      'habits.php'],
        'mess'       => ['restaurant',  'Mess',        'mess.php'],
        'settings'   => ['settings',    'Settings',    'settings.php'],
    ];
    $items = '';
    foreach ($pages as $key => [$icon, $label, $href]) {
        if ($key === $activePage) {
            $items .= <<<HTML
<a href="{$base}/{$href}" class="flex flex-col items-center gap-0.5">
    <span class="material-symbols-outlined text-[var(--accent-purple)] active-glow">{$icon}</span>
    <span class="text-[10px] font-medium text-[var(--accent-purple)]">{$label}</span>
    <div class="w-6 h-[2px] bg-[var(--accent-purple)] rounded-full mt-0.5 shadow-[0_0_8px_var(--accent-purple)]"></div>
</a>
HTML;
        } else {
            $items .= <<<HTML
<a href="{$base}/{$href}" class="flex flex-col items-center gap-0.5 text-[var(--text-muted)]">
    <span class="material-symbols-outlined">{$icon}</span>
    <span class="text-[10px] font-medium">{$label}</span>
</a>
HTML;
        }
    }
    echo "<nav class=\"bottom-nav lg:hidden\">{$items}</nav>";
}

function pageFooter(): void {
    $base = BASE_URL;
    echo <<<HTML
<div id="pwa-install-banner" class="fixed bottom-4 right-4 z-50 hidden">
    <div class="rounded-2xl bg-[var(--card-dark)] border border-[var(--border-subtle)] shadow-2xl shadow-black/40 px-4 py-3 flex items-center gap-3 max-w-xs">
        <div class="text-sm text-white font-semibold">Install Sanctuary?</div>
        <div class="flex items-center gap-2 ml-auto">
            <button id="pwa-install-dismiss" class="text-[var(--text-muted)] text-xs hover:text-white transition">Later</button>
            <button id="pwa-install-btn" class="px-3 py-1.5 rounded-lg bg-[var(--accent-purple)] text-[var(--text-dark)] text-xs font-semibold shadow-lg shadow-[var(--accent-purple)]/40">Install</button>
        </div>
    </div>
</div>
<script src="{$base}/assets/js/app.js"></script>
</body>
</html>
HTML;
}

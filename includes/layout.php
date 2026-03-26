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
    $seed = htmlspecialchars($user['avatar_seed'] ?? ($user['name'] ?? 'User'));
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
    <div class="mb-8 px-2">
        <h2 class="text-lg font-black tracking-widest text-white uppercase">The Sanctuary</h2>
        <p class="text-xs text-[var(--text-muted)] opacity-60">Deep Work Mode</p>
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
<script src="{$base}/assets/js/app.js"></script>
</body>
</html>
HTML;
}

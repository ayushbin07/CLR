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
    $user = auth();
    $name  = htmlspecialchars($user['name'] ?? 'User');
    $seed  = htmlspecialchars($user['avatar_seed'] ?? $name);
    $base  = BASE_URL;
    $pages = [
        'index'      => ['Home',        'index.php'],
        'assignment' => ['Assignments', 'assignment.php'],
        'habits'     => ['Habits',      'habits.php'],
        'mess'       => ['Mess',        'mess.php'],
    ];
    $links = '';
    foreach ($pages as $key => [$label, $href]) {
        $active = $key === $activePage
            ? 'text-[var(--accent-purple)] font-semibold border-b-2 border-[var(--accent-purple)] pb-1'
            : 'text-[var(--text-muted)] hover:text-white transition-colors';
        $links .= "<a class=\"{$active}\" href=\"" . BASE_URL . "/{$href}\">{$label}</a>\n";
    }
    echo <<<HTML
<nav class="hidden lg:flex fixed top-0 w-full z-50 bg-[var(--bg-dark)]/80 backdrop-blur-xl items-center justify-between px-8 h-16 border-b border-[var(--border-subtle)]">
    <div class="flex items-center gap-4">
        <span class="text-xl font-bold tracking-tighter text-white">Sanctuary</span>
    </div>
    <div class="flex items-center space-x-8">
        {$links}
    </div>
    <div class="flex items-center space-x-4">
        <a href="{$base}/settings.php" class="p-2 hover:bg-[var(--card-dark)] rounded-lg transition-all">
            <span class="material-symbols-outlined text-[var(--text-muted)]">settings</span>
        </a>
        <div class="w-8 h-8 rounded-full bg-[var(--card-dark)] overflow-hidden">
            <img alt="Profile" class="w-full h-full object-cover" src="https://api.dicebear.com/7.x/avataaars/svg?seed={$seed}"/>
        </div>
    </div>
</nav>
HTML;
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
<aside class="sidebar w-64 fixed left-0 top-0 h-screen bg-[var(--card-dark)] border-r border-[var(--border-subtle)] p-6 flex-col hidden lg:flex z-40 pt-24">
    <div class="mb-8 px-2">
        <h2 class="text-lg font-black tracking-widest text-white uppercase">The Sanctuary</h2>
        <p class="text-xs text-[var(--text-muted)] opacity-60">Deep Work Mode</p>
    </div>
    <nav class="flex-1 space-y-2">{$links}</nav>
    <div class="mt-auto space-y-2 pt-6">
        <a href="{$base}/settings.php" class="flex items-center space-x-3 px-4 py-2 text-[var(--text-muted)] hover:text-white transition-colors">
            <span class="material-symbols-outlined text-sm">settings</span>
            <span class="text-xs">Settings</span>
        </a>
        <a href="{$base}/api/auth.php?action=logout" class="flex items-center space-x-3 px-4 py-2 text-[var(--text-muted)] hover:text-[#ffb4ab] transition-colors">
            <span class="material-symbols-outlined text-sm">logout</span>
            <span class="text-xs">Logout</span>
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

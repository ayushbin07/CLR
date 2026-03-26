<?php
require_once __DIR__ . '/includes/config.php';
requireAuth();
$user = auth();
$csrf = csrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Sanctuary | Home</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
</head>
<body class="min-h-screen">

    <!-- Desktop Top Navbar -->
    <nav class="hidden lg:flex fixed top-0 w-full z-50 bg-[var(--bg-dark)]/80 backdrop-blur-xl items-center justify-between px-8 h-16 border-b border-[var(--border-subtle)]">
        <div class="flex items-center gap-4">
            <span class="text-xl font-bold tracking-tighter text-white">Sanctuary</span>
        </div>
        <div class="flex items-center space-x-8">
            <a class="text-[var(--accent-purple)] font-semibold border-b-2 border-[var(--accent-purple)] pb-1" href="<?= BASE_URL ?>/index.php">Home</a>
            <a class="text-[var(--text-muted)] hover:text-white transition-colors" href="<?= BASE_URL ?>/assignment.php">Assignments</a>
            <a class="text-[var(--text-muted)] hover:text-white transition-colors" href="<?= BASE_URL ?>/habits.php">Habits</a>
            <a class="text-[var(--text-muted)] hover:text-white transition-colors" href="<?= BASE_URL ?>/mess.php">Mess</a>
        </div>
        <div class="flex items-center space-x-4">
            <button class="p-2 hover:bg-[var(--card-dark)] rounded-lg transition-all">
                <span class="material-symbols-outlined text-[var(--text-muted)]">notifications</span>
            </button>
            <a href="<?= BASE_URL ?>/settings.php" class="p-2 hover:bg-[var(--card-dark)] rounded-lg transition-all">
                <span class="material-symbols-outlined text-[var(--text-muted)]">settings</span>
            </a>
            <div class="w-8 h-8 rounded-full bg-[var(--card-dark)] overflow-hidden">
                <img alt="Profile" class="w-full h-full object-cover" src="https://api.dicebear.com/7.x/avataaars/svg?seed=<?= urlencode($user['avatar_seed'] ?? $user['name']) ?>"/>
            </div>
        </div>
    </nav>

    <!-- Desktop Sidebar -->
    <aside class="sidebar w-64 fixed left-0 top-0 h-screen bg-[var(--card-dark)] border-r border-[var(--border-subtle)] p-6 flex-col hidden lg:flex z-40 pt-24">
        <div class="mb-8 px-2">
            <h2 class="text-lg font-black tracking-widest text-white uppercase">The Sanctuary</h2>
            <p class="text-xs text-[var(--text-muted)] opacity-60">Deep Work Mode</p>
        </div>
        <nav class="flex-1 space-y-2">
            <a href="<?= BASE_URL ?>/index.php" class="flex items-center space-x-3 px-4 py-3 bg-[rgba(168,162,255,0.15)] text-[var(--accent-purple)] rounded-xl shadow-lg shadow-[#8E5CF6]/10 transition-transform duration-300">
                <span class="material-symbols-outlined">home</span>
                <span class="font-medium text-sm">Home</span>
            </a>
            <a href="<?= BASE_URL ?>/assignment.php" class="flex items-center space-x-3 px-4 py-3 text-[var(--text-muted)] hover:bg-[rgba(255,255,255,0.05)] hover:translate-x-1 rounded-xl transition-all duration-300">
                <span class="material-symbols-outlined">assignment</span>
                <span class="font-medium text-sm">Assignments</span>
            </a>
            <a href="<?= BASE_URL ?>/habits.php" class="flex items-center space-x-3 px-4 py-3 text-[var(--text-muted)] hover:bg-[rgba(255,255,255,0.05)] hover:translate-x-1 rounded-xl transition-all duration-300">
                <span class="material-symbols-outlined">auto_awesome</span>
                <span class="font-medium text-sm">Habits</span>
            </a>
            <a href="<?= BASE_URL ?>/mess.php" class="flex items-center space-x-3 px-4 py-3 text-[var(--text-muted)] hover:bg-[rgba(255,255,255,0.05)] hover:translate-x-1 rounded-xl transition-all duration-300">
                <span class="material-symbols-outlined">restaurant</span>
                <span class="font-medium text-sm">Mess</span>
            </a>
        </nav>
        <div class="mt-auto space-y-2 pt-6">
            <button class="w-full py-3 px-4 bg-[rgba(168,162,255,0.15)] text-[var(--accent-purple)] rounded-xl font-bold text-sm hover:bg-[rgba(168,162,255,0.25)] transition-all mb-4">
                Start Study Session
            </button>
            <a href="<?= BASE_URL ?>/settings.php" class="flex items-center space-x-3 px-4 py-2 text-[var(--text-muted)] hover:text-white transition-colors">
                <span class="material-symbols-outlined text-sm">settings</span>
                <span class="text-xs">Settings</span>
            </a>
            <a href="<?= BASE_URL ?>/api/auth.php?action=logout" class="flex items-center space-x-3 px-4 py-2 text-[var(--text-muted)] hover:text-[#ffb4ab] transition-colors">
                <span class="material-symbols-outlined text-sm">logout</span>
                <span class="text-xs">Logout</span>
            </a>
        </div>
    </aside>

    <!-- Mobile Bottom Nav -->
    <nav class="bottom-nav lg:hidden">
        <a href="<?= BASE_URL ?>/index.php" class="flex flex-col items-center gap-0.5 group">
            <span class="material-symbols-outlined text-[var(--accent-purple)] active-glow">home</span>
            <span class="text-[10px] font-medium text-[var(--accent-purple)]">Home</span>
            <div class="w-6 h-[2px] bg-[var(--accent-purple)] rounded-full mt-0.5 shadow-[0_0_8px_var(--accent-purple)]"></div>
        </a>
        <a href="<?= BASE_URL ?>/assignment.php" class="flex flex-col items-center gap-0.5 text-[var(--text-muted)]">
            <span class="material-symbols-outlined">assignment</span>
            <span class="text-[10px] font-medium">Assignments</span>
        </a>
        <a href="<?= BASE_URL ?>/habits.php" class="flex flex-col items-center gap-0.5 text-[var(--text-muted)]">
            <span class="material-symbols-outlined">auto_awesome</span>
            <span class="text-[10px] font-medium">Habits</span>
        </a>
        <a href="<?= BASE_URL ?>/mess.php" class="flex flex-col items-center gap-0.5 text-[var(--text-muted)]">
            <span class="material-symbols-outlined">restaurant</span>
            <span class="text-[10px] font-medium">Mess</span>
        </a>
        <a href="<?= BASE_URL ?>/settings.php" class="flex flex-col items-center gap-0.5 text-[var(--text-muted)]">
            <span class="material-symbols-outlined">settings</span>
            <span class="text-[10px] font-medium">Settings</span>
        </a>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="max-w-5xl mx-auto">
            <header class="px-6 pt-12 pb-6 lg:pt-16 lg:px-10">
                <h1 class="text-4xl font-bold tracking-tight mb-1">Hello, <?= htmlspecialchars($user['name']) ?></h1>
                <p class="text-[var(--text-muted)] text-lg">Here’s what you need to focus on today</p>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 px-6 lg:px-10 mb-8">
                <!-- Hero Section -->
                <section class="lg:col-span-12">
                    <div class="relative w-full aspect-[16/9] lg:aspect-[21/9] rounded-[32px] overflow-hidden bg-gradient-to-br from-[#2D2B4A] to-[#161524] p-8 flex items-end">
                        <div class="z-10 max-w-[60%]">
                            <h2 class="text-2xl lg:text-3xl font-semibold mb-2">Final Exams approaching</h2>
                            <p class="text-white/60 text-sm lg:text-base">Review your semester roadmap and start preparing.</p>
                        </div>
                        <img alt="Education illustration" class="absolute right-0 bottom-0 h-full object-contain opacity-80 mix-blend-lighten pointer-events-none" src="https://lh3.googleusercontent.com/aida-public/AB6AXuADnUwrAOX47yJnj2qf4hgN-RNYR5yoFx8HMa_BKueAaDPd9og1w06Osedtn3Q2ACVCgzvmaqVMW1gA1jDKQ_4vqLOAOm1Dvgs1t5RRWpKEK3nu4ueioc82FHqF8q8Y_eYIrWHFu543VrdK8-JCqqqnLFYJy_89XHRpFKD4C1xbi6gLj_bxDz3w5a_ia49t1xpPc0NI_Aj4iZz8DsAhqJ8bLMnuKEwhOOJ6Ux-_GbUe10E4lBdki1JFPuKeHiRi309rDTU8rTRWxRv_"/>
                    </div>
                </section>

                <!-- Todo List -->
                <section class="lg:col-span-5">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <h3 class="text-xl font-semibold">Todo List</h3>
                        <div class="flex items-center gap-2">
                            <button id="add-todo" class="text-sm bg-[var(--accent-purple)] text-[#0F0F12] px-3 py-1.5 rounded-lg font-semibold flex items-center gap-1 shadow-lg shadow-[#8E5CF6]/30 hover:translate-y-[-1px] transition-transform">
                                <span class="material-symbols-outlined text-sm leading-none">add</span>
                                Add Task
                            </button>
                            <button id="refresh-todos" class="text-sm text-[var(--text-muted)] hover:text-[var(--accent-purple)] transition-colors flex items-center gap-1">
                                Refresh
                                <span class="material-symbols-outlined text-sm leading-none">refresh</span>
                            </button>
                        </div>
                    </div>
                    <div id="todo-list" class="space-y-3 px-1"></div>
                </section>

                <!-- Today's Classes -->
                <section class="lg:col-span-7">
                    <h3 class="text-xl font-semibold mb-4 px-1">Today's Classes</h3>
                    <div class="flex gap-4 overflow-x-auto pb-4 no-scrollbar">
                        <div class="flex-none w-[220px] bg-[var(--card-dark)] rounded-[24px] p-5 border border-white/5 hover:border-white/10 transition-colors">
                            <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center mb-6">
                                <span class="material-symbols-outlined text-white/80">memory</span>
                            </div>
                            <h4 class="text-[var(--text-soft)] font-medium mb-1">Data Structures</h4>
                            <div class="h-[2px] w-full bg-white/10 rounded-full mb-4 overflow-hidden">
                                <div class="h-full bg-[var(--accent-purple)] w-1/3"></div>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[var(--text-muted)] text-xs flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[14px]">timer</span>
                                    10:30 AM - 12:00 PM
                                </p>
                                <p class="text-[var(--text-muted)] text-xs flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[14px]">location_on</span>
                                    Room 302
                                </p>
                            </div>
                        </div>
                        <div class="flex-none w-[220px] bg-[var(--card-dark)] rounded-[24px] p-5 border border-white/5 hover:border-white/10 transition-colors">
                            <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center mb-6">
                                <span class="material-symbols-outlined text-white/80">biotech</span>
                            </div>
                            <h4 class="text-[var(--text-soft)] font-medium mb-1">Organic Chemistry</h4>
                            <div class="h-[2px] w-full bg-white/10 rounded-full mb-4 overflow-hidden">
                                <div class="h-full bg-[var(--accent-purple)] w-0"></div>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[var(--text-muted)] text-xs flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[14px]">timer</span>
                                    1:00 PM - 2:30 PM
                                </p>
                                <p class="text-[var(--text-muted)] text-xs flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[14px]">location_on</span>
                                    Lab 101
                                </p>
                            </div>
                        </div>
                        <div class="flex-none w-[220px] bg-[var(--card-dark)] rounded-[24px] p-5 border border-white/5 hover:border-white/10 transition-colors">
                            <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center mb-6">
                                <span class="material-symbols-outlined text-white/80">public</span>
                            </div>
                            <h4 class="text-[var(--text-soft)] font-medium mb-1 truncate">Business Management</h4>
                            <div class="h-[2px] w-full bg-white/10 rounded-full mb-4 overflow-hidden">
                                <div class="h-full bg-[var(--accent-purple)] w-0"></div>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[var(--text-muted)] text-xs flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[14px]">timer</span>
                                    3:00 PM - 4:30 PM
                                </p>
                                <p class="text-[var(--text-muted)] text-xs flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[14px]">location_on</span>
                                    Hall B
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <script src="<?= BASE_URL ?>/assets/js/app.js"></script>
    <script>
    const BASE = <?= json_encode(BASE_URL) ?>;
    const CSRF = <?= json_encode($csrf) ?>;

    async function loadTodos() {
        const container = document.getElementById('todo-list');
        container.innerHTML = '<div class="p-6 text-center text-[var(--text-muted)] text-sm">Loading…</div>';
        try {
            const res = await fetch(`${BASE}/api/todos.php?action=list`);
            const todos = await res.json();
            if (!Array.isArray(todos) || !todos.length) {
                container.innerHTML = '<div class="p-6 text-center text-[var(--text-muted)] text-sm bg-[var(--card-dark)] rounded-2xl border border-white/5">No todos yet.</div>';
                return;
            }
            container.innerHTML = todos.map(t => {
                const checked = t.is_completed ? 'checked' : '';
                const titleCls = t.is_completed ? 'line-through text-[var(--text-muted)]' : 'text-[var(--text-soft)]';
                return `
                    <div class="todo-row flex items-center justify-between p-4 bg-[var(--card-dark)] rounded-2xl border border-white/5 hover:border-white/10 transition-colors" data-id="${t.id}">
                        <div class="flex items-center gap-4 pl-1">
                            <button class="custom-checkbox ${checked}" aria-label="Toggle todo" data-id="${t.id}">${checked ? '<span class="material-symbols-outlined text-xs font-bold text-[#0F0F12]">check</span>' : ''}</button>
                            <div>
                                <h4 class="${titleCls} font-medium text-sm leading-tight">${escapeHtml(t.title)}</h4>
                                <p class="text-[var(--text-muted)] text-[11px]">Created ${new Date(t.created_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                        <button class="delete-todo p-2 text-[var(--text-muted)] hover:text-red-400" title="Delete" data-id="${t.id}">
                            <span class="material-symbols-outlined text-sm">delete</span>
                        </button>
                    </div>`;
            }).join('');

            container.querySelectorAll('.custom-checkbox').forEach(cb => {
                cb.addEventListener('click', () => toggleTodo(cb.dataset.id));
            });
            container.querySelectorAll('.delete-todo').forEach(btn => {
                btn.addEventListener('click', () => deleteTodo(btn.dataset.id));
            });
        } catch (e) {
            container.innerHTML = '<div class="p-6 text-center text-red-400 text-sm bg-[var(--card-dark)] rounded-2xl border border-red-500/30">Failed to load todos.</div>';
        }
    }

    async function addTodo() {
        const title = prompt('Add a task');
        if (!title) return;
        const trimmed = title.trim();
        if (!trimmed) return;

        const fd = new FormData();
        fd.append('title', trimmed);
        fd.append('csrf_token', CSRF);

        const res = await fetch(`${BASE}/api/todos.php?action=create`, { method:'POST', body: fd });
        const data = await res.json();
        if (data.error) {
            alert(data.error);
            return;
        }
        loadTodos();
    }

    async function toggleTodo(id) {
        const fd = new FormData();
        fd.append('id', id);
        fd.append('csrf_token', CSRF);
        await fetch(`${BASE}/api/todos.php?action=toggle`, { method:'POST', body:fd });
        loadTodos();
    }

    async function deleteTodo(id) {
        const fd = new FormData();
        fd.append('id', id);
        fd.append('csrf_token', CSRF);
        await fetch(`${BASE}/api/todos.php?action=delete`, { method:'POST', body:fd });
        loadTodos();
    }

    function escapeHtml(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    document.getElementById('add-todo').addEventListener('click', addTodo);
    document.getElementById('refresh-todos').addEventListener('click', loadTodos);
    loadTodos();
    </script>
</body>
</html>

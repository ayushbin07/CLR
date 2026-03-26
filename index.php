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
                    <div id="hero-card" class="relative w-full aspect-[16/9] lg:aspect-[21/9] rounded-[32px] overflow-hidden bg-[var(--card-dark)] p-8 flex items-end transition-opacity duration-300">
                        <img id="hero-image" alt="Hero visual" class="absolute inset-0 w-full h-full object-cover opacity-80" src="" loading="lazy" />
                        <div class="absolute inset-0 bg-gradient-to-r from-[#13112b] via-[#0d0c1a]/80 to-transparent"></div>
                        <div class="z-10 max-w-[60%]">
                            <p class="text-[11px] uppercase tracking-[0.2em] text-white/60 mb-2">Weekly Spotlight</p>
                            <h2 id="hero-title" class="text-2xl lg:text-3xl font-semibold mb-2">Loading…</h2>
                            <p id="hero-subtitle" class="text-white/70 text-sm lg:text-base">Fetching your focus.</p>
                        </div>
                        <div id="hero-dots" class="absolute top-4 right-4 flex items-center gap-2 z-10"></div>
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
                    <div class="relative">
                        <button id="classes-left" class="hidden md:flex items-center justify-center absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 w-9 h-9 rounded-full bg-[var(--card-dark)] border border-white/10 text-white/70 hover:text-white hover:border-white/30 shadow-lg shadow-black/30 backdrop-blur-sm">
                            <span class="material-symbols-outlined text-lg">chevron_left</span>
                        </button>
                        <div id="classes-container" class="flex gap-3 overflow-x-auto pb-4 no-scrollbar snap-x snap-mandatory flex-nowrap min-w-0 cursor-grab active:cursor-grabbing scroll-smooth"></div>
                        <button id="classes-right" class="hidden md:flex items-center justify-center absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 w-9 h-9 rounded-full bg-[var(--card-dark)] border border-white/10 text-white/70 hover:text-white hover:border-white/30 shadow-lg shadow-black/30 backdrop-blur-sm">
                            <span class="material-symbols-outlined text-lg">chevron_right</span>
                        </button>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <script src="<?= BASE_URL ?>/assets/js/app.js"></script>
    <script>
    const BASE = <?= json_encode(BASE_URL) ?>;
    const CSRF = <?= json_encode($csrf) ?>;
    const CLASS_ICONS = ['book','science','public','code','architecture','stadia_controller','psychology','experiment','school'];
    const HERO_DEFAULTS = [
        {
            title: "This Week's Focus",
            subtitle: 'Lock in your priorities and stay on schedule.',
            image_url: 'https://images.unsplash.com/photo-1529070538774-1843cb3265df?auto=format&fit=crop&w=1400&q=80'
        },
        {
            title: 'Keep Your Momentum',
            subtitle: 'Plan your next deep work block and protect it.',
            image_url: 'https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=1400&q=80'
        }
    ];

    const heroCardEl = document.getElementById('hero-card');
    const heroTitleEl = document.getElementById('hero-title');
    const heroSubtitleEl = document.getElementById('hero-subtitle');
    const heroImageEl = document.getElementById('hero-image');
    const heroDotsEl = document.getElementById('hero-dots');
    let heroCards = [];
    let heroIdx = 0;
    let heroTimer = null;

    async function loadHeroCards() {
        try {
            const res = await fetch(`${BASE}/api/hero_cards.php?action=list`);
            const data = await res.json();
            heroCards = Array.isArray(data) && data.length ? data : HERO_DEFAULTS;
        } catch (e) {
            heroCards = HERO_DEFAULTS;
        }
        heroIdx = 0;
        renderHeroCard(true);
        startHeroRotation();
    }

    function renderHeroCard(initial = false) {
        const card = heroCards[heroIdx];
        if (!card) return;
        heroCardEl.classList.add('opacity-0');
        setTimeout(() => {
            heroTitleEl.textContent = card.title || '';
            heroSubtitleEl.textContent = card.subtitle || '';
            heroImageEl.src = card.image_url || '';
            heroCardEl.classList.remove('opacity-0');
            renderHeroDots();
        }, initial ? 0 : 160);
    }

    function renderHeroDots() {
        if (heroCards.length <= 1) {
            heroDotsEl.innerHTML = '';
            return;
        }
        heroDotsEl.innerHTML = heroCards.map((_, i) =>
            `<button data-hero="${i}" class="w-2.5 h-2.5 rounded-full ${i === heroIdx ? 'bg-white' : 'bg-white/40'}"></button>`
        ).join('');
        heroDotsEl.querySelectorAll('[data-hero]').forEach(btn => {
            btn.addEventListener('click', () => {
                heroIdx = Number(btn.dataset.hero) || 0;
                renderHeroCard();
                resetHeroRotation();
            });
        });
    }

    function startHeroRotation() {
        clearInterval(heroTimer);
        if (heroCards.length <= 1) return;
        heroTimer = setInterval(() => {
            heroIdx = (heroIdx + 1) % heroCards.length;
            renderHeroCard();
        }, 5000);
    }

    function resetHeroRotation() {
        clearInterval(heroTimer);
        startHeroRotation();
    }

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

    async function loadClasses() {
        const container = document.getElementById('classes-container');
        container.innerHTML = '<div class="flex-none w-[200px] h-[200px] p-5 rounded-[20px] border border-white/5 bg-[var(--card-dark)] text-[var(--text-muted)] text-sm">Loading today\'s classes…</div>';
        try {
            const res = await fetch(`${BASE}/api/home.php?action=timetable`);
            const classes = await res.json();
            if (!Array.isArray(classes) || !classes.length) {
                container.innerHTML = '<div class="flex-none w-[200px] h-[200px] p-5 rounded-[20px] border border-white/5 bg-[var(--card-dark)] text-[var(--text-muted)] text-sm">No classes scheduled for today.</div>';
                return;
            }
            container.innerHTML = classes.map((c, idx) => {
                const icon = CLASS_ICONS[idx % CLASS_ICONS.length];
                const room = c.room ? `<p class="text-[var(--text-muted)] text-[11px] flex items-center gap-1 mt-2"><span class="material-symbols-outlined text-[14px]">location_on</span>${escapeHtml(c.room)}</p>` : '';
                return `
                <div class="flex-none w-[220px] h-[200px] p-5 bg-[var(--card-dark)] rounded-[20px] border border-white/5 hover:border-white/10 transition-transform transition-colors duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-black/20 shadow-sm shadow-black/10 snap-start">
                    <div class="w-10 h-10 rounded-2xl bg-white/5 flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-white/80">${icon}</span>
                    </div>
                    <h4 class="text-[var(--text-soft)] font-semibold text-sm leading-tight mb-1">${escapeHtml(c.subject)}</h4>
                    <p class="text-[var(--text-muted)] text-[11px]">${escapeHtml(c.day_of_week)} • ${formatTime(c.start_time)} - ${formatTime(c.end_time)}</p>
                    ${room}
                </div>`;
            }).join('');
        } catch (e) {
            container.innerHTML = '<div class="flex-none w-[200px] h-[200px] p-5 rounded-[20px] border border-red-500/30 bg-[var(--card-dark)] text-red-400 text-sm">Failed to load classes.</div>';
        }
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

    function formatTime(t) {
        if (!t) return '';
        const [h, m] = t.split(':');
        const hour = parseInt(h, 10);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const hr12 = hour % 12 === 0 ? 12 : hour % 12;
        return `${hr12}:${m} ${ampm}`;
    }

    document.getElementById('add-todo').addEventListener('click', addTodo);
    document.getElementById('refresh-todos').addEventListener('click', loadTodos);
    loadTodos();
    loadClasses();
    loadHeroCards();

    // Enable drag-to-scroll on classes container
    (() => {
        const scroller = document.getElementById('classes-container');
        let isDown = false;
        let startX = 0;
        let scrollLeft = 0;

        const start = (clientX) => {
            isDown = true;
            startX = clientX;
            scrollLeft = scroller.scrollLeft;
        };
        const move = (clientX, evt) => {
            if (!isDown) return;
            evt.preventDefault();
            const walk = clientX - startX;
            scroller.scrollLeft = scrollLeft - walk;
        };
        const end = () => { isDown = false; };

        scroller.addEventListener('mousedown', (e) => start(e.clientX));
        scroller.addEventListener('mousemove', (e) => move(e.clientX, e));
        ['mouseleave','mouseup'].forEach(ev => scroller.addEventListener(ev, end));

        scroller.addEventListener('touchstart', (e) => start(e.touches[0].clientX), { passive:true });
        scroller.addEventListener('touchmove', (e) => move(e.touches[0].clientX, e), { passive:false });
        ['touchend','touchcancel'].forEach(ev => scroller.addEventListener(ev, end));

        const scrollBy = 220;
        const leftBtn = document.getElementById('classes-left');
        const rightBtn = document.getElementById('classes-right');
        const updateBtns = () => {
            const max = scroller.scrollWidth - scroller.clientWidth;
            leftBtn.style.display = scroller.scrollLeft > 4 ? 'flex' : 'none';
            rightBtn.style.display = scroller.scrollLeft < max - 4 ? 'flex' : 'none';
        };
        leftBtn?.addEventListener('click', () => { scroller.scrollBy({ left: -scrollBy, behavior: 'smooth' }); setTimeout(updateBtns, 200); });
        rightBtn?.addEventListener('click', () => { scroller.scrollBy({ left: scrollBy, behavior: 'smooth' }); setTimeout(updateBtns, 200); });
        scroller.addEventListener('scroll', updateBtns, { passive:true });
        setTimeout(updateBtns, 300);
    })();
    </script>
</body>
</html>

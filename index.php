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

    <!-- Desktop Top Navbar removed per design request; sidebar handles desktop nav -->

    <!-- Desktop Sidebar -->
    <aside class="sidebar w-64 fixed left-0 top-0 h-screen bg-[var(--card-dark)] border-r border-[var(--border-subtle)] p-6 flex-col hidden lg:flex z-40">
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
        <div class="mt-auto space-y-3 pt-6">
            <a href="<?= BASE_URL ?>/settings.php" class="neo-nav-pill">
                <span class="material-symbols-outlined text-sm">settings</span>
                <span class="text-xs font-semibold">Settings</span>
            </a>
            <a href="<?= BASE_URL ?>/api/auth.php?action=logout" class="neo-nav-pill neo-nav-pill--danger">
                <span class="material-symbols-outlined text-sm">logout</span>
                <span class="text-xs font-semibold">Logout</span>
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
            <header class="px-6 pt-6 pb-4 lg:pt-8 lg:px-10">
                <h1 class="text-4xl font-bold tracking-tight mb-1">Hello, <span class="text-[var(--accent-purple)]"><?= htmlspecialchars($user['name']) ?></span></h1>
                <p class="text-[var(--text-muted)] text-lg">Here’s what you need to focus on today</p>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 px-6 lg:px-10 mb-8">
                <!-- Hero Section -->
                <section class="lg:col-span-12">
                    <div class="card hero-frame">
                        <div class="card-overlay"></div>
                        <div class="card-inner">
                            <div id="hero-card" class="neo-image-card relative w-full aspect-[16/10] lg:aspect-[5/2] max-h-[320px] lg:max-h-[360px] p-8 flex items-end transition-opacity duration-300">
                                <img id="hero-image" alt="Hero visual" class="absolute inset-0 w-full h-full object-cover opacity-80" src="" loading="lazy" />
                                <div class="absolute inset-0 bg-gradient-to-r from-[#13112b] via-[#0d0c1a]/80 to-transparent"></div>
                                <div class="z-10 max-w-[60%] hero-text">
                                    <p class="text-[11px] uppercase tracking-[0.2em] text-white/60 mb-2">Weekly Spotlight</p>
                                    <h2 id="hero-title" class="text-2xl lg:text-3xl font-semibold mb-2">Loading…</h2>
                                    <p id="hero-subtitle" class="text-white/70 text-sm lg:text-base">Fetching your focus.</p>
                                </div>
                                <div id="hero-dots" class="absolute top-4 right-4 flex items-center gap-2 z-10"></div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Todo List -->
                <section class="lg:col-span-5">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <h3 class="text-xl font-semibold">Todo List</h3>
                        <div class="flex items-center gap-2">
                            <button id="add-todo" class="button neo-press">
                                <div class="button-outer">
                                    <div class="button-inner">
                                        <span class="flex items-center text-sm">
                                        
                                            <span>Add</span>
                                        </span>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                    <div id="todo-list" class="space-y-3 px-1"></div>
                </section>

                <!-- Today's Classes -->
                <section class="lg:col-span-7">
                    <h3 class="text-xl font-semibold mb-4 px-1">Today's Classes</h3>
                    <div class="relative">
                        <div id="classes-container" class="no-scrollbar flex gap-4 overflow-x-auto px-1 pb-4 lg:grid lg:grid-cols-3 lg:auto-rows-fr lg:gap-6 lg:gap-y-8 lg:overflow-visible lg:px-1 lg:pb-2"></div>
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
                    <div class="todo-row neo-card flex items-center justify-between p-4" data-id="${t.id}">
                        <div class="flex items-center gap-4 pl-1">
                            <button class="custom-checkbox ${checked}" aria-label="Toggle todo" data-id="${t.id}">${checked ? '<span class="material-symbols-outlined text-xs font-bold text-[#0F0F12]">check</span>' : ''}</button>
                            <div>
                                <h4 class="${titleCls} font-medium text-sm leading-tight">${escapeHtml(t.title)}</h4>
                                <p class="text-[var(--text-muted)] text-[11px]">Created ${new Date(t.created_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <button class="edit-todo p-2 text-[var(--text-muted)] hover:text-[var(--text)]" title="Edit" data-id="${t.id}" data-title="${escapeHtml(t.title)}">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                            <button class="delete-todo p-2 text-[var(--text-muted)] hover:text-red-400" title="Delete" data-id="${t.id}">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </div>
                    </div>`;
            }).join('');

            container.querySelectorAll('.custom-checkbox').forEach(cb => {
                cb.addEventListener('click', () => toggleTodo(cb.dataset.id));
            });
            container.querySelectorAll('.delete-todo').forEach(btn => {
                btn.addEventListener('click', () => deleteTodo(btn.dataset.id));
            });
            container.querySelectorAll('.edit-todo').forEach(btn => {
                btn.addEventListener('click', () => editTodo(btn.dataset.id, btn.dataset.title));
            });
        } catch (e) {
            container.innerHTML = '<div class="p-6 text-center text-red-400 text-sm bg-[var(--card-dark)] rounded-2xl border border-red-500/30">Failed to load todos.</div>';
        }
    }

    async function addTodo() {
        const modal = createPromptModal({
            title: 'Add a task',
            placeholder: 'Type your task…',
            confirmLabel: 'Add',
            onConfirm: async (value) => {
                const trimmed = (value || '').trim();
                if (!trimmed) return;

                const fd = new FormData();
                fd.append('title', trimmed);
                fd.append('csrf_token', CSRF);

                const res = await fetch(`${BASE}/api/todos.php?action=create`, { method:'POST', body: fd });
                const data = await res.json();
                if (data.error) {
                    showToast(data.error, true);
                    return;
                }
                loadTodos();
            },
        });
        document.body.appendChild(modal);
        modal.querySelector('input')?.focus();
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
                <div class="neo-card class-card flex-shrink-0 w-[220px] lg:w-full h-full p-4 rounded-[18px] flex flex-col">
                    <div class="class-card-icon w-9 h-9 rounded-2xl flex items-center justify-center mb-2">
                        <span class="material-symbols-outlined text-[18px]">${icon}</span>
                    </div>
                    <div class="mt-auto space-y-1">
                        <h4 class="text-[var(--text-soft)] font-semibold text-sm leading-tight">${escapeHtml(c.subject)}</h4>
                        <p class="text-[var(--text-muted)] text-[11px]">${escapeHtml(c.day_of_week)} • ${formatTime(c.start_time)} - ${formatTime(c.end_time)}</p>
                        ${room}
                    </div>
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

    async function editTodo(id, currentTitle = '') {
        const modal = createPromptModal({
            title: 'Edit task',
            value: currentTitle,
            placeholder: 'Update your task…',
            confirmLabel: 'Save',
            onConfirm: async (value) => {
                const trimmed = (value || '').trim();
                if (!trimmed) return;

                const fd = new FormData();
                fd.append('id', id);
                fd.append('title', trimmed);
                fd.append('csrf_token', CSRF);
                await fetch(`${BASE}/api/todos.php?action=update`, { method:'POST', body:fd });
                loadTodos();
            },
        });
        document.body.appendChild(modal);
        modal.querySelector('input')?.focus();
    }

    function escapeHtml(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    // Custom prompt modal (matches skeuo theme)
    function createPromptModal({ title = 'Prompt', value = '', placeholder = '', confirmLabel = 'OK', cancelLabel = 'Cancel', onConfirm }) {
        const wrapper = document.createElement('div');
        wrapper.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md';
        wrapper.innerHTML = `
            <div class="max-w-sm w-full bg-[var(--surface-2)] border border-[var(--border-subtle)] rounded-2xl shadow-[0_24px_50px_rgba(15,23,42,0.2)] overflow-hidden">
                <div class="px-5 pt-5 pb-3">
                    <h3 class="text-base font-semibold text-[var(--text-soft)] mb-2">${escapeHtml(title)}</h3>
                    <input type="text" value="${escapeHtml(value)}" placeholder="${escapeHtml(placeholder)}"
                        class="w-full bg-white/70 border border-[var(--border-subtle)] rounded-xl px-4 py-3 text-sm text-[var(--text)] outline-none focus:border-[var(--accent-strong)]/60 focus:ring-2 focus:ring-[var(--accent)]/20" />
                </div>
                <div class="flex justify-end gap-2 px-5 pb-4">
                    <button class="glassy-cta ghost w-auto text-sm px-4 py-2" data-action="cancel">${escapeHtml(cancelLabel)}</button>
                    <button class="glassy-cta w-auto text-sm px-4 py-2" data-action="confirm">${escapeHtml(confirmLabel)}</button>
                </div>
            </div>`;

        const input = wrapper.querySelector('input');
        const close = () => wrapper.remove();

        wrapper.addEventListener('click', (e) => {
            if (e.target === wrapper) close();
        });

        wrapper.querySelector('[data-action="cancel"]').addEventListener('click', close);
        wrapper.querySelector('[data-action="confirm"]').addEventListener('click', async () => {
            if (typeof onConfirm === 'function') await onConfirm(input.value);
            close();
        });

        input.addEventListener('keydown', async (e) => {
            if (e.key === 'Enter') {
                if (typeof onConfirm === 'function') await onConfirm(input.value);
                close();
            }
            if (e.key === 'Escape') close();
        });

        return wrapper;
    }

    function showToast(message, isError = false) {
        const existing = document.getElementById('toast');
        if (existing) existing.remove();
        const toast = document.createElement('div');
        toast.id = 'toast';
        toast.className = `fixed bottom-6 left-1/2 -translate-x-1/2 px-4 py-3 rounded-xl text-sm font-semibold border shadow-lg ${isError ? 'bg-red-500/20 border-red-400 text-red-200' : 'bg-[var(--accent)]/15 border-[var(--accent-strong)] text-[var(--text)]'}`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2800);
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
            if (!leftBtn || !rightBtn) return;
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

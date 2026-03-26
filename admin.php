<?php
require_once __DIR__ . '/includes/config.php';
requireAuth();
requireAdmin();
$csrf    = csrfToken();
$classes = db()->query('SELECT * FROM classes ORDER BY name')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>Sanctuary | Admin</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
</head>
<body class="min-h-screen">
<nav class="hidden lg:flex fixed top-0 w-full z-50 bg-[var(--bg-dark)]/80 backdrop-blur-xl items-center justify-between px-8 h-16 border-b border-[var(--border-subtle)]">
    <span class="text-xl font-bold tracking-tighter text-white">Sanctuary <span class="text-[var(--accent-purple)] text-sm font-normal ml-2">Admin</span></span>
    <div class="flex items-center gap-4">
        <a href="<?= BASE_URL ?>/index.php" class="text-sm text-[var(--text-muted)] hover:text-white">← Back to App</a>
        <a href="<?= BASE_URL ?>/api/auth.php?action=logout" class="text-sm text-[var(--text-muted)] hover:text-[#ffb4ab]">Logout</a>
    </div>
</nav>

<div id="flash" class="hidden fixed top-20 right-6 z-50 px-5 py-3 rounded-xl text-sm font-medium shadow-xl"></div>

<main class="pt-24 px-6 lg:px-12 pb-16 max-w-5xl mx-auto">
    <h1 class="text-3xl font-bold mb-10">Admin Panel</h1>

    <div class="grid lg:grid-cols-2 gap-8">

        <!-- ===== HERO CARDS ===== -->
        <section class="lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">Home Hero Cards</h2>
                <div class="flex items-center gap-2">
                    <button id="hero-add" class="px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-sm text-[var(--text-soft)] hover:bg-white/10 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">add</span> Add Card
                    </button>
                    <button id="hero-refresh" class="px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-sm text-[var(--text-muted)] hover:bg-white/10 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">refresh</span> Refresh
                    </button>
                </div>
            </div>
            <div class="bg-[var(--card-dark)] rounded-[24px] border border-white/5 p-6 space-y-5">
                <div id="hero-cards" class="grid sm:grid-cols-2 gap-4"></div>
                <div class="bg-white/5 border border-white/10 rounded-[18px] p-4">
                    <h3 class="text-sm font-semibold text-[var(--text-soft)] mb-3">Edit / Add Card</h3>
                    <form id="hero-form" class="space-y-3">
                        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                        <input type="hidden" name="id" id="hero-id">
                        <div>
                            <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-1">Title</label>
                            <input id="hero-title" name="title" type="text" required
                                   class="w-full bg-[var(--bg-dark)] border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white outline-none focus:border-[var(--accent-purple)]/50"
                                   placeholder="This Week's Focus" />
                        </div>
                        <div>
                            <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-1">Subtitle</label>
                            <input id="hero-subtitle" name="subtitle" type="text" required
                                   class="w-full bg-[var(--bg-dark)] border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white outline-none focus:border-[var(--accent-purple)]/50"
                                   placeholder="Lock in your priorities" />
                        </div>
                        <div>
                            <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-1">Image URL</label>
                            <input id="hero-image" name="image_url" type="url" required
                                   class="w-full bg-[var(--bg-dark)] border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white outline-none focus:border-[var(--accent-purple)]/50"
                                   placeholder="https://..." />
                        </div>
                        <div class="grid grid-cols-2 gap-3 items-center">
                            <div>
                                <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-1">Order</label>
                                <input id="hero-order" name="sort_order" type="number" min="1" value="1"
                                       class="w-full bg-[var(--bg-dark)] border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white outline-none focus:border-[var(--accent-purple)]/50" />
                            </div>
                            <label class="flex items-center gap-2 text-sm text-[var(--text-muted)] mt-5">
                                <input id="hero-active" name="is_active" type="checkbox" class="accent-[var(--accent-purple)]" checked>
                                Active
                            </label>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" id="hero-cancel" class="px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-sm text-[var(--text-muted)] hover:bg-white/10">Reset</button>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-[var(--accent-purple)] text-[#0F0F12] font-semibold text-sm shadow-lg shadow-[#8E5CF6]/20">Save Card</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- ===== MESS MENU ===== -->
        <section>
            <h2 class="text-xl font-semibold mb-4">Today's Mess Menu</h2>
            <form id="mess-form" class="bg-[var(--card-dark)] rounded-[24px] border border-white/5 p-6 space-y-4">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <div>
                    <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Date</label>
                    <input type="date" name="date" value="<?= date('Y-m-d') ?>"
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none focus:border-[var(--accent-purple)]/50"/>
                </div>
                <div>
                    <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Meal</label>
                    <select name="meal_type" class="w-full bg-[var(--bg-dark)] border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none">
                        <option value="breakfast">Breakfast</option>
                        <option value="lunch">Lunch</option>
                        <option value="dinner">Dinner</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Items</label>
                    <textarea name="items" rows="3" required
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none focus:border-[var(--accent-purple)]/50 resize-none"
                        placeholder="Idli, Sambar, Coconut Chutney…"></textarea>
                </div>
                <button type="submit"
                    class="w-full py-3 bg-[var(--accent-purple)] text-[#0F0F12] rounded-xl font-bold text-sm hover:opacity-90">
                    Save Menu
                </button>
            </form>
        </section>

        <!-- ===== TIMETABLE ===== -->
        <section>
            <h2 class="text-xl font-semibold mb-4">Add Timetable Slot</h2>
            <form id="timetable-form" class="bg-[var(--card-dark)] rounded-[24px] border border-white/5 p-6 space-y-4">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <div>
                    <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Class</label>
                    <select name="class_id" required class="w-full bg-[var(--bg-dark)] border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none">
                        <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Subject</label>
                    <input type="text" name="subject" required
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none focus:border-[var(--accent-purple)]/50"
                        placeholder="Data Structures"/>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Room</label>
                        <input type="text" name="room"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none focus:border-[var(--accent-purple)]/50"
                            placeholder="Room 302"/>
                    </div>
                    <div>
                        <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Day</label>
                        <select name="day_of_week" required class="w-full bg-[var(--bg-dark)] border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none">
                            <?php foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $d): ?>
                            <option value="<?= $d ?>"><?= $d ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Start</label>
                        <input type="time" name="start_time" required
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none"/>
                    </div>
                    <div>
                        <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">End</label>
                        <input type="time" name="end_time" required
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none"/>
                    </div>
                </div>
                <button type="submit"
                    class="w-full py-3 bg-[var(--accent-purple)] text-[#0F0F12] rounded-xl font-bold text-sm hover:opacity-90">
                    Add Slot
                </button>
            </form>
        </section>

        <!-- ===== JSON IMPORT ===== -->
        <section class="lg:col-span-2">
            <h2 class="text-xl font-semibold mb-4">Bulk Import Timetable (JSON)</h2>
            <div class="bg-[var(--card-dark)] rounded-[24px] border border-white/5 p-6">
                <p class="text-[var(--text-muted)] text-sm mb-4">
                    Paste a JSON array of timetable slots. Each object needs:
                    <code class="text-[var(--accent-purple)] bg-white/5 px-1 py-0.5 rounded text-xs">class_id, subject, room, day_of_week, start_time, end_time</code>
                    <br/>Use day_of_week short names: Mon, Tue, Wed, Thu, Fri, Sat, Sun.
                </p>
                <textarea id="json-import" rows="6"
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white font-mono outline-none focus:border-[var(--accent-purple)]/50 resize-y"
                    placeholder='[{"class_id":1,"subject":"Math","room":"101","day_of_week":"Mon","start_time":"09:00","end_time":"10:30"}]'></textarea>
                <button id="json-import-btn"
                    class="mt-4 px-6 py-3 bg-[var(--accent-purple)] text-[#0F0F12] rounded-xl font-bold text-sm hover:opacity-90">
                    Import JSON
                </button>
                <div class="mt-4 bg-white/5 border border-white/10 rounded-xl p-4 text-[12px] text-[var(--text-muted)] space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-[var(--text-soft)]">Copy-ready sample</span>
                        <button id="copy-tt-sample" class="text-[var(--accent-purple)] text-xs hover:underline">Copy</button>
                    </div>
                    <pre id="tt-sample" class="whitespace-pre-wrap break-words">[
  {"class_id":1,"subject":"Math","room":"101","day_of_week":"Mon","start_time":"09:00","end_time":"09:50"},
  {"class_id":1,"subject":"Physics","room":"102","day_of_week":"Mon","start_time":"10:00","end_time":"10:50"},
  {"class_id":1,"subject":"Lab","room":"201","day_of_week":"Tue","start_time":"11:00","end_time":"12:30"}
]</pre>
                </div>
            </div>
        </section>

        <!-- ===== MANAGE / REPLACE TIMETABLE ===== -->
        <section class="lg:col-span-2">
            <h2 class="text-xl font-semibold mb-4">Manage Timetable (Admin)</h2>
            <div class="bg-[var(--card-dark)] rounded-[24px] border border-white/5 p-6 space-y-6">
                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Class</label>
                        <select id="tt-class" class="w-full bg-[var(--bg-dark)] border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none">
                            <?php foreach ($classes as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Day (optional)</label>
                        <select id="tt-day" class="w-full bg-[var(--bg-dark)] border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none">
                            <option value="">All days</option>
                            <?php foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $d): ?>
                                <option value="<?= $d ?>"><?= $d ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button id="tt-load" class="flex-1 py-3 bg-white/5 border border-white/10 rounded-xl text-sm font-semibold text-[var(--text-muted)] hover:bg-white/10">Load Slots</button>
                        <button id="tt-clear-day" class="py-3 px-4 bg-[var(--accent-purple)] text-[#0F0F12] rounded-xl text-sm font-bold hover:opacity-90" title="Replace slots for selected scope">Replace</button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Replace with JSON (optional)</label>
                    <textarea id="tt-replace-json" rows="4" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white font-mono outline-none focus:border-[var(--accent-purple)]/50 resize-y" placeholder='[{"subject":"Math","room":"101","day_of_week":"Mon","start_time":"09:00","end_time":"10:30"}]'></textarea>
                    <p class="text-[10px] text-[var(--text-muted)] mt-2">When you click Replace: deletes existing slots for the selected class (and day if chosen), then inserts these slots. If JSON is empty, it just clears.</p>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-[var(--text-soft)]">Current Slots</h3>
                        <span id="tt-count" class="text-[11px] text-[var(--text-muted)]">—</span>
                    </div>
                    <div id="tt-list" class="rounded-[18px] border border-white/5 bg-white/5 divide-y divide-white/5">
                        <div class="p-4 text-sm text-[var(--text-muted)]">Load to view slots.</div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

<script>
const BASE = <?= json_encode(BASE_URL) ?>;
const CSRF = <?= json_encode($csrf) ?>;

function flash(msg, isError = false) {
    const el = document.getElementById('flash');
    el.textContent = msg;
    el.className = isError
        ? 'fixed top-20 right-6 z-50 px-5 py-3 rounded-xl text-sm font-medium shadow-xl bg-red-500/20 text-red-400 border border-red-500/30'
        : 'fixed top-20 right-6 z-50 px-5 py-3 rounded-xl text-sm font-medium shadow-xl bg-green-500/20 text-green-400 border border-green-500/30';
    setTimeout(() => el.classList.add('hidden'), 3500);
}

document.getElementById('mess-form').addEventListener('submit', async e => {
    e.preventDefault();
    const res = await fetch(`${BASE}/api/mess.php?action=menu`, { method:'POST', body:new FormData(e.target) });
    const d   = await res.json();
    d.success ? flash('Menu saved!') : flash(d.error || 'Error', true);
});

document.getElementById('timetable-form').addEventListener('submit', async e => {
    e.preventDefault();
    const res = await fetch(`${BASE}/api/timetable.php?action=add`, { method:'POST', body:new FormData(e.target) });
    const d   = await res.json();
    d.success ? (flash('Slot added!'), e.target.reset()) : flash(d.error || 'Error', true);
});

document.getElementById('json-import-btn').addEventListener('click', async () => {
    let data;
    try { data = JSON.parse(document.getElementById('json-import').value); }
    catch { return flash('Invalid JSON', true); }
    if (!Array.isArray(data)) return flash('Must be an array', true);

    const fd = new FormData();
    fd.append('csrf_token', CSRF);
    fd.append('slots', JSON.stringify(data));
    const res = await fetch(`${BASE}/api/timetable.php?action=import`, { method:'POST', body:fd });
    const d   = await res.json();
    d.success ? flash(`Imported ${d.count} slots!`) : flash(d.error || 'Error', true);
});

document.getElementById('copy-tt-sample').addEventListener('click', async () => {
    const text = document.getElementById('tt-sample').textContent.trim();
    try {
        await navigator.clipboard.writeText(text);
        flash('Sample copied');
    } catch {
        flash('Copy failed', true);
    }
});

// Hero cards management
const heroCardsWrap   = document.getElementById('hero-cards');
const heroForm        = document.getElementById('hero-form');
const heroIdInput     = document.getElementById('hero-id');
const heroTitleInput  = document.getElementById('hero-title');
const heroSubInput    = document.getElementById('hero-subtitle');
const heroImgInput    = document.getElementById('hero-image');
const heroOrderInput  = document.getElementById('hero-order');
const heroActiveInput = document.getElementById('hero-active');
let heroCardsData = [];

const esc = (s) => String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

function setHeroForm(card = null) {
    if (card) {
        heroIdInput.value    = card.id || '';
        heroTitleInput.value = card.title || '';
        heroSubInput.value   = card.subtitle || '';
        heroImgInput.value   = card.image_url || '';
        heroOrderInput.value = card.sort_order || 1;
        heroActiveInput.checked = !!card.is_active;
    } else {
        heroForm.reset();
        heroIdInput.value = '';
        heroOrderInput.value = 1;
        heroActiveInput.checked = true;
    }
}

async function loadHeroCardsAdmin() {
    heroCardsWrap.innerHTML = '<div class="p-4 text-sm text-[var(--text-muted)] bg-white/5 border border-white/10 rounded-xl">Loading…</div>';
    try {
        const res = await fetch(`${BASE}/api/hero_cards.php?action=list&all=1`);
        const data = await res.json();
        heroCardsData = Array.isArray(data) ? data : [];
        if (!heroCardsData.length) {
            heroCardsWrap.innerHTML = '<div class="p-4 text-sm text-[var(--text-muted)] bg-white/5 border border-white/10 rounded-xl">No hero cards yet. Add one to start.</div>';
            return;
        }
        heroCardsWrap.innerHTML = heroCardsData.map((c, idx) => `
            <div class="relative overflow-hidden rounded-2xl border border-white/10 bg-[#0f0f12] group">
                <img src="${esc(c.image_url)}" alt="Hero image" class="absolute inset-0 w-full h-full object-cover opacity-80">
                <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/40 to-transparent"></div>
                <div class="relative p-4 h-full flex flex-col justify-between">
                    <div>
                        <p class="text-[11px] uppercase tracking-[0.2em] text-white/60 mb-1">Order ${c.sort_order ?? 1}${c.is_active ? '' : ' • Inactive'}</p>
                        <h4 class="text-lg font-semibold text-white leading-tight mb-1">${esc(c.title)}</h4>
                        <p class="text-white/70 text-sm">${esc(c.subtitle)}</p>
                    </div>
                    <div class="flex items-center justify-between mt-3">
                        <span class="text-[11px] text-white/60">#${c.id}</span>
                        <button type="button" data-edit="${idx}" class="px-3 py-1.5 rounded-lg bg-white/10 text-white text-xs font-semibold flex items-center gap-1 hover:bg-white/20">
                            <span class="material-symbols-outlined text-sm">edit</span>
                            Edit
                        </button>
                    </div>
                </div>
            </div>
        `).join('');

        heroCardsWrap.querySelectorAll('[data-edit]').forEach(btn => {
            btn.addEventListener('click', () => {
                const idx = Number(btn.dataset.edit);
                const card = heroCardsData[idx];
                if (card) setHeroForm(card);
            });
        });
    } catch (e) {
        heroCardsWrap.innerHTML = '<div class="p-4 text-sm text-red-400 bg-red-500/10 border border-red-500/30 rounded-xl">Failed to load hero cards.</div>';
    }
}

heroForm.addEventListener('submit', async e => {
    e.preventDefault();
    const fd = new FormData(heroForm);
    if (!heroActiveInput.checked) fd.set('is_active', '0');
    fd.append('csrf_token', CSRF);
    const res = await fetch(`${BASE}/api/hero_cards.php?action=save`, { method:'POST', body: fd });
    const d   = await res.json();
    if (d.success) {
        flash('Hero card saved');
        setHeroForm(null);
        loadHeroCardsAdmin();
    } else {
        flash(d.error || 'Error saving card', true);
    }
});

document.getElementById('hero-add').addEventListener('click', () => setHeroForm(null));
document.getElementById('hero-refresh').addEventListener('click', loadHeroCardsAdmin);
document.getElementById('hero-cancel').addEventListener('click', () => setHeroForm(null));

loadHeroCardsAdmin();

// Timetable list/load/delete/replace
const ttList  = document.getElementById('tt-list');
const ttCount = document.getElementById('tt-count');

async function loadTimetable() {
    const classId = document.getElementById('tt-class').value;
    const day     = document.getElementById('tt-day').value;
    ttList.innerHTML = '<div class="p-4 text-sm text-[var(--text-muted)]">Loading…</div>';
    const res = await fetch(`${BASE}/api/timetable.php?action=list&class_id=${classId}&day_of_week=${day}`);
    const rows = await res.json();
    ttCount.textContent = rows.length ? `${rows.length} slots` : 'No slots';
    if (!rows.length) {
        ttList.innerHTML = '<div class="p-4 text-sm text-[var(--text-muted)]">No slots for this scope.</div>';
        return;
    }
    ttList.innerHTML = rows.map(r => `
        <div class="flex items-center justify-between p-3">
            <div class="text-sm text-[var(--text-soft)]">
                <span class="font-semibold">${r.subject}</span>
                <span class="text-[var(--text-muted)] ml-2">${r.room || ''}</span>
                <span class="text-[var(--text-muted)] ml-3">${r.day_of_week} ${r.start_time}–${r.end_time}</span>
            </div>
            <button class="text-[var(--text-muted)] hover:text-red-400 text-sm" data-id="${r.id}">Delete</button>
        </div>`).join('');

    ttList.querySelectorAll('button[data-id]').forEach(btn => {
        btn.addEventListener('click', async () => {
            const fd = new FormData(); fd.append('id', btn.dataset.id); fd.append('csrf_token', CSRF);
            await fetch(`${BASE}/api/timetable.php?action=delete`, { method:'POST', body:fd });
            loadTimetable();
        });
    });
}

document.getElementById('tt-load').addEventListener('click', e => { e.preventDefault(); loadTimetable(); });

document.getElementById('tt-clear-day').addEventListener('click', async e => {
    e.preventDefault();
    const classId = document.getElementById('tt-class').value;
    const day     = document.getElementById('tt-day').value;
    let slots = [];
    const txt = document.getElementById('tt-replace-json').value.trim();
    if (txt) {
        try { slots = JSON.parse(txt); } catch { return flash('Invalid JSON', true); }
        if (!Array.isArray(slots)) return flash('JSON must be an array', true);
    }
    const fd = new FormData();
    fd.append('csrf_token', CSRF);
    fd.append('class_id', classId);
    if (day) fd.append('day_of_week', day);
    fd.append('slots', JSON.stringify(slots));
    const res = await fetch(`${BASE}/api/timetable.php?action=replace`, { method:'POST', body:fd });
    const d   = await res.json();
    d.success ? flash(`Replaced ${d.count} slots`) : flash(d.error || 'Error', true);
    loadTimetable();
});
</script>
</body>
</html>

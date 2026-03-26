<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/layout.php';
requireAuth();
$csrf = csrfToken();

pageHead('Habits');
topNav('habits');
sidebar('habits');
bottomNav('habits');
?>
<main class="main-content">
    <div class="max-w-4xl mx-auto">
        <header class="px-6 pt-14 pb-6 lg:pt-16 lg:px-10">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-3xl lg:text-4xl font-bold tracking-tight mb-1">Habits</h1>
                    <p class="text-[var(--text-muted)] text-sm lg:text-base">Stay consistent, one day at a time</p>
                </div>
                <button id="open-habit-modal" class="w-10 h-10 rounded-full bg-[var(--card-dark)] border border-white/10 text-white flex items-center justify-center shadow-lg active:scale-95 transition-transform">
                    <span class="material-symbols-outlined font-bold">add</span>
                </button>
            </div>
        </header>

        <!-- Habit cards -->
        <div id="habit-grid" class="grid grid-cols-1 lg:grid-cols-2 gap-6 px-6 lg:px-10 mb-10">
            <div class="bg-[var(--card-dark)] rounded-[24px] h-36 animate-pulse"></div>
            <div class="bg-[var(--card-dark)] rounded-[24px] h-36 animate-pulse"></div>
        </div>

        <!-- Overall heatmap -->
        <section class="px-6 lg:px-10 mb-8">
            <h2 class="text-lg font-bold mb-4 px-1">Overall Progress</h2>
            <div class="bg-[var(--card-dark)] rounded-[28px] p-6 border border-white/5">
                <div class="flex items-end justify-between mb-6">
                    <div>
                        <span id="consistency-pct" class="text-3xl font-bold text-[var(--text-soft)]">—%</span>
                        <p class="text-[var(--text-muted)] text-xs uppercase tracking-wider mt-1">Avg Consistency (30 days)</p>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-medium text-[var(--accent-purple)]">Past 90 Days</span>
                    </div>
                </div>
                <div id="overall-heatmap" class="grid gap-1" style="grid-template-columns:repeat(18,minmax(0,1fr))"></div>
                <div class="mt-6 flex justify-between items-center text-[10px] text-[var(--text-muted)]">
                    <span id="hm-start-label">3 months ago</span>
                    <span>Today</span>
                    <div class="flex items-center gap-1.5 ml-auto">
                        <span>Less</span>
                        <div class="w-2 h-2 rounded-sm bg-white/5"></div>
                        <div class="w-2 h-2 rounded-sm bg-[var(--accent-purple)] opacity-40"></div>
                        <div class="w-2 h-2 rounded-sm bg-[var(--accent-purple)]"></div>
                        <span>More</span>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

<!-- Add Habit Modal -->
<div id="habit-modal" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="w-full max-w-sm bg-[var(--card-dark)] rounded-[28px] border border-white/10 p-6 shadow-2xl">
        <h2 class="text-lg font-bold mb-5">New Habit</h2>
        <form id="habit-form" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <div>
                <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Habit Name</label>
                <input type="text" name="name" required
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder:text-[var(--text-muted)] outline-none focus:border-[var(--accent-purple)]/50"
                    placeholder="e.g. Morning Reading"/>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" id="close-habit-modal"
                    class="flex-1 py-3 bg-white/5 border border-white/10 rounded-xl text-sm font-semibold text-[var(--text-muted)] hover:bg-white/10">Cancel</button>
                <button type="submit"
                    class="flex-1 py-3 bg-[var(--accent-purple)] text-[#0F0F12] rounded-xl text-sm font-bold hover:opacity-90">Create</button>
            </div>
        </form>
    </div>
</div>

<script>
const CSRF = <?= json_encode($csrf) ?>;

// -----------------------------------------------
async function loadHabits() {
    const [habitsRes, overallRes] = await Promise.all([
        fetch('/sanctuary/api/habits.php?action=list'),
        fetch('/sanctuary/api/habits.php?action=overall'),
    ]);
    const habits  = await habitsRes.json();
    const overall = await overallRes.json();

    renderHabitGrid(habits);
    renderOverallHeatmap(overall, habits);
}

// -----------------------------------------------
async function loadHabitHeatmap(habitId) {
    const res  = await fetch(`/sanctuary/api/habits.php?action=heatmap&habit_id=${habitId}&days=30`);
    return await res.json();
}

// -----------------------------------------------
function renderHabitGrid(habits) {
    const grid = document.getElementById('habit-grid');
    if (!habits.length) {
        grid.innerHTML = `<div class="lg:col-span-2 p-12 text-center text-[var(--text-muted)] bg-[var(--card-dark)] rounded-[24px] border border-white/5">
            <span class="material-symbols-outlined text-4xl mb-3 block opacity-30">auto_awesome</span>
            No habits yet. Click + to create your first one!
        </div>`;
        return;
    }
    grid.innerHTML = '';
    habits.forEach(async h => {
        const card = document.createElement('div');
        card.className = 'habit-card bg-[var(--card-dark)] rounded-[24px] border border-white/5 p-5';
        card.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="custom-checkbox ${h.done_today ? 'checked' : ''}" data-habit-id="${h.id}">
                        ${h.done_today ? '<span class="material-symbols-outlined text-xs font-bold text-[#0F0F12]">check</span>' : ''}
                    </div>
                    <h3 class="font-semibold text-[17px] text-[var(--text-soft)]">${escHtml(h.name)}</h3>
                </div>
                <div class="flex items-center gap-2">
                    <div class="text-right">
                        <p class="${h.streak > 0 ? 'text-[var(--accent-purple)]' : 'text-[var(--text-muted)]'} text-xs font-semibold">
                            ${h.streak} day streak
                        </p>
                    </div>
                    <button class="delete-habit-btn p-1 text-[var(--text-muted)] hover:text-red-400 transition-colors" data-id="${h.id}">
                        <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <div class="flex justify-between items-center text-[10px] text-[var(--text-muted)] uppercase tracking-widest mb-1">
                    <span>Last 30 Days</span>
                    <span>${h.consistency}% Consistency</span>
                </div>
                <div class="heatmap-row grid gap-1.5" style="grid-template-columns:repeat(15,minmax(0,1fr))" data-habit="${h.id}">
                    ${Array(30).fill('<div class="heatmap-square bg-white/10 animate-pulse"></div>').join('')}
                </div>
            </div>`;
        grid.appendChild(card);

        // Checkbox
        card.querySelector('.custom-checkbox').addEventListener('click', async () => {
            const fd = new FormData();
            fd.append('habit_id', h.id);
            fd.append('date', todayStr());
            fd.append('csrf_token', CSRF);
            await fetch('/sanctuary/api/habits.php?action=log', { method:'POST', body:fd });
            loadHabits();
        });

        // Delete
        card.querySelector('.delete-habit-btn').addEventListener('click', async () => {
            if (!confirm(`Delete habit "${h.name}"?`)) return;
            const fd = new FormData(); fd.append('id', h.id); fd.append('csrf_token', CSRF);
            await fetch('/sanctuary/api/habits.php?action=delete', { method:'POST', body:fd });
            loadHabits();
        });

        // Load per-habit heatmap
        const heatmapData = await loadHabitHeatmap(h.id);
        const row = card.querySelector(`[data-habit="${h.id}"]`);
        row.innerHTML = heatmapData.map((d, i) => {
            const isToday = i === heatmapData.length - 1;
            const ring    = isToday ? ' ring-2 ring-white/20' : '';
            return d.completed
                ? `<div class="heatmap-square bg-[var(--accent-purple)]${ring}" title="${d.date}"></div>`
                : `<div class="heatmap-square bg-white/5${ring}" title="${d.date}"></div>`;
        }).join('');
    });
}

// -----------------------------------------------
function renderOverallHeatmap(data, habits) {
    const container = document.getElementById('overall-heatmap');
    container.innerHTML = data.map(d => {
        const cls = d.intensity === 0 ? 'bg-white/5'
                  : d.intensity === 1 ? 'bg-[var(--accent-purple)] opacity-20'
                  : d.intensity === 2 ? 'bg-[var(--accent-purple)] opacity-40'
                  : d.intensity === 3 ? 'bg-[var(--accent-purple)] opacity-70'
                  : 'bg-[var(--accent-purple)]';
        return `<div class="heatmap-square ${cls}" title="${d.date}"></div>`;
    }).join('');

    // Avg consistency
    if (habits.length) {
        const avg = Math.round(habits.reduce((s, h) => s + h.consistency, 0) / habits.length);
        document.getElementById('consistency-pct').textContent = avg + '%';
    }
}

// -----------------------------------------------
// Add habit modal
document.getElementById('open-habit-modal').addEventListener('click', () => {
    document.getElementById('habit-modal').classList.remove('hidden');
});
document.getElementById('close-habit-modal').addEventListener('click', () => {
    document.getElementById('habit-modal').classList.add('hidden');
});

document.getElementById('habit-form').addEventListener('submit', async e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    const res = await fetch('/sanctuary/api/habits.php?action=create', { method:'POST', body:fd });
    const d   = await res.json();
    if (d.success) {
        document.getElementById('habit-modal').classList.add('hidden');
        e.target.reset();
        loadHabits();
    }
});

// -----------------------------------------------
function todayStr() {
    const d = new Date();
    return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
}
function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

loadHabits();
</script>
<?php pageFooter(); ?>

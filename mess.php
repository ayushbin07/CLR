<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/layout.php';
requireAuth();
$user    = auth();
$isAdmin = $user && ($user['role'] ?? '') === 'admin';
$csrf    = csrfToken();
$today   = date('l, F j');

pageHead('Mess Menu');
topNav('mess');
sidebar('mess');
bottomNav('mess');
?>
<main class="main-content">
    <div class="max-w-3xl mx-auto">
        <header class="px-6 pt-14 pb-8 lg:pt-16 lg:px-10 flex items-center justify-between">
            <div class="text-center lg:text-left">
                <h1 class="text-3xl lg:text-4xl font-bold tracking-tight mb-1">Mess Today</h1>
                <p class="text-[var(--text-muted)] text-sm lg:text-base"><?= $today ?></p>
            </div>
            <button id="toggle-archive" class="glassy-plus !w-11 !h-11 lg:!w-10 lg:!h-10 !p-0 rounded-xl focus-visible:ring-2 focus-visible:ring-[var(--accent-purple)] focus-visible:outline-none" title="Archive">
                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 0 24 24" width="20px" fill="currentColor"><path d="M0 0h24v24H0z" fill="none"/><path d="M20.54 5.23l-1.39-1.68C18.88 3.21 18.47 3 18 3H6c-.47 0-.88.21-1.16.55L3.46 5.23C3.17 5.57 3 6.02 3 6.5V19c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6.5c0-.48-.17-.93-.46-1.27zM6.24 5h11.52l.83 1H5.42l.82-1zM5 19V8h14v11H5zm8-5.35l4 3.53v-2.18h-8v2.18l4-3.53zM12 9.5l4 3.53h-2.5v2.82h-3v-2.82H8l4-3.53z"/></svg>
            </button>
        </header>

        <div class="px-6 lg:px-10" id="mess-container">
            <!-- Skeleton -->
            <div class="bg-[var(--card-dark)] rounded-[32px] border border-white/5 p-8 space-y-6">
                <div class="h-6 bg-white/5 rounded-xl animate-pulse w-1/3"></div>
                <div class="h-4 bg-white/5 rounded-xl animate-pulse w-full"></div>
                <div class="h-px bg-white/5"></div>
                <div class="h-6 bg-white/5 rounded-xl animate-pulse w-1/4"></div>
                <div class="h-4 bg-white/5 rounded-xl animate-pulse w-3/4"></div>
                <div class="h-px bg-white/5"></div>
                <div class="h-6 bg-white/5 rounded-xl animate-pulse w-1/3"></div>
                <div class="h-4 bg-white/5 rounded-xl animate-pulse w-2/3"></div>
            </div>
        </div>

    </div>
</main>

<script>
const BASE = <?= json_encode(BASE_URL) ?>;
const CSRF = <?= json_encode($csrf) ?>;
const IS_ADMIN = false; // Admin-only import moved to admin panel

const mealIcons = { breakfast: 'wb_twilight', lunch: 'light_mode', lunch_international: 'public', snacks: 'tapas', dinner: 'dark_mode' };
const mealLabels = { breakfast: 'Breakfast', lunch: 'Lunch', lunch_international: 'Lunch International', snacks: 'Snacks', dinner: 'Dinner' };

async function loadMess() {
    const res  = await fetch(`${BASE}/api/mess.php?action=today`);
    const menu = await res.json();
    const container = document.getElementById('mess-container');

    if (!menu.length) {
        container.innerHTML = `<div class="bg-[var(--card-dark)] rounded-[32px] border border-white/5 p-12 text-center text-[var(--text-muted)]">
            <span class="material-symbols-outlined text-4xl block mb-3 opacity-30">restaurant</span>
            No menu posted for today yet.
        </div>`;
        return;
    }

    const sections = menu.map((meal, i) => {
        const icon  = mealIcons[meal.meal_type]  || 'restaurant';
        const label = mealLabels[meal.meal_type] || meal.meal_type;
        const myReaction = meal.my_reaction;
        const divider = i < menu.length - 1 ? '<div class="h-px bg-white/10 w-full mt-6"></div>' : '';
        return `<section class="relative z-10" data-meal-id="${meal.id}">
            <div class="flex items-center gap-3 mb-2">
                <span class="material-symbols-outlined text-[var(--accent-purple)] text-xl">${icon}</span>
                <h2 class="text-lg font-semibold tracking-wide uppercase text-[var(--text-soft)]">${label}</h2>
            </div>
            <p class="text-[var(--text-soft)] text-lg leading-relaxed pl-8 mb-4">${escHtml(meal.items)}</p>
            <div class="flex gap-2 pl-8">
                <button data-meal-id="${meal.id}" data-reaction="like" class="reaction-btn px-4 py-2 border rounded-full flex items-center justify-center gap-2 transition-colors duration-200
                    ${myReaction === 'like' ? 'bg-emerald-500/20 border-emerald-500/40 text-emerald-400' : 'bg-white/5 border-white/10 hover:bg-white/10 text-[var(--text-soft)]'}">
                    <span class="text-base leading-none">👍</span>
                    <span class="text-xs font-semibold">${meal.likes || 0}</span>
                </button>
                <button data-meal-id="${meal.id}" data-reaction="dislike" class="reaction-btn px-4 py-2 border rounded-full flex items-center justify-center gap-2 transition-colors duration-200
                    ${myReaction === 'dislike' ? 'bg-red-500/20 border-red-500/40 text-red-400' : 'bg-white/5 border-white/10 hover:bg-white/10 text-[var(--text-soft)]'}">
                    <span class="text-base leading-none">👎</span>
                    <span class="text-xs font-semibold">${meal.dislikes || 0}</span>
                </button>
            </div>
        </section>${divider}`;
    }).join('');

    container.innerHTML = `<div class="meal-card rounded-[28px] border border-white/8 p-6 lg:p-8 flex flex-col gap-6 sm:gap-8" style="background:var(--card-dark);position:relative;overflow:hidden;">
        <span class="material-symbols-outlined watermark-icon hidden sm:block">restaurant</span>
        ${sections}
    </div>`;

    // Bind reactions
    document.querySelectorAll('.reaction-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const fd = new FormData();
            fd.append('mess_id',  btn.dataset.mealId);
            fd.append('reaction', btn.dataset.reaction);
            fd.append('csrf_token', CSRF);
            await fetch(`${BASE}/api/mess.php?action=react`, { method:'POST', body:fd });
            loadMess();
        });
    });
}


function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

async function showArchiveModal() {
    const existing = document.getElementById('archive-modal-wrapper');
    if (existing) existing.remove();

    const wrapper = document.createElement('div');
    wrapper.className = 'fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-md';
    wrapper.id = 'archive-modal-wrapper';
    wrapper.innerHTML = `
        <div class="max-w-xl w-full max-h-[80vh] flex flex-col bg-[var(--surface-2)] border border-[var(--border-subtle)] rounded-2xl shadow-[0_24px_50px_rgba(15,23,42,0.2)] overflow-hidden">
            <div class="px-5 pt-5 pb-3 flex justify-between items-center border-b border-[var(--border-subtle)]">
                <h3 class="text-base font-semibold text-[var(--text-soft)]">Mess Menu Archive</h3>
                <button class="text-[var(--text-muted)] hover:text-white" data-action="close">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0z" fill="none"/><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                </button>
            </div>
            <div class="p-4 overflow-y-auto space-y-4" id="archive-modal-list">
                <div class="text-center text-[var(--text-muted)] text-sm p-4">Loading entire menu history...</div>
            </div>
        </div>`;

    const close = () => wrapper.remove();
    wrapper.addEventListener('click', (e) => {
        if (e.target === wrapper) close();
    });
    wrapper.querySelector('[data-action="close"]').addEventListener('click', close);
    document.body.appendChild(wrapper);

    // Fetch and populate DB archive
    try {
        const res = await fetch(`${BASE}/api/mess.php?action=all`);
        const allMenus = await res.json();
        const listDiv = document.getElementById('archive-modal-list');
        
        if (!allMenus.length) {
            listDiv.innerHTML = '<div class="text-center text-[var(--text-muted)] text-sm p-4">No historical records found.</div>';
            return;
        }

        // Group by Date
        const grouped = {};
        for(const m of allMenus) {
            if(!grouped[m.date]) grouped[m.date] = [];
            grouped[m.date].push(m);
        }

        let html = '';
        const todayStr = new Date().toISOString().split('T')[0];

        for(const dateKey of Object.keys(grouped).sort().reverse()) {
            const dayLabel = dateKey === todayStr ? 'Today, ' + dateKey : dateKey;
            
            html += `<h4 class="text-sm font-bold text-[var(--text-soft)] sticky top-0 bg-[var(--surface-2)] py-2 border-b border-[var(--border-subtle)] mb-3">${escHtml(dayLabel)}</h4>`;
            
            html += `<div class="space-y-4 mb-6">`;
            for(const meal of grouped[dateKey]) {
                const icon  = mealIcons[meal.meal_type]  || 'restaurant';
                const label = mealLabels[meal.meal_type] || meal.meal_type;
                html += `
                    <div class="flex gap-3 text-sm">
                        <span class="material-symbols-outlined text-[var(--accent-purple)] text-lg shrink-0 mt-0.5">${icon}</span>
                        <div>
                            <p class="font-semibold text-[var(--text-soft)] uppercase tracking-wide text-xs mb-1">${label} 
                                <span class="text-[var(--accent-purple)]/80 font-medium lowercase text-[10px] ml-1">(👍 ${meal.likes || 0} / 👎 ${meal.dislikes || 0})</span>
                            </p>
                            <p class="text-[var(--text-muted)] leading-relaxed">${escHtml(meal.items)}</p>
                        </div>
                    </div>
                `;
            }
            html += `</div>`;
        }

        listDiv.innerHTML = html;
    } catch(e) {
        document.getElementById('archive-modal-list').innerHTML = '<div class="text-center text-red-400 text-sm p-4">Failed to load history.</div>';
    }
}

document.getElementById('toggle-archive')?.addEventListener('click', showArchiveModal);
loadMess();

</script>
<?php pageFooter(); ?>

<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/layout.php';
requireAuth();
$csrf    = csrfToken();
$today   = date('l, F j');

pageHead('Mess Menu');
topNav('mess');
sidebar('mess');
bottomNav('mess');
?>
<main class="main-content">
    <div class="max-w-3xl mx-auto">
        <header class="px-6 pt-14 pb-8 lg:pt-16 lg:px-10 text-center lg:text-left">
            <h1 class="text-3xl lg:text-4xl font-bold tracking-tight mb-1">Mess Today</h1>
            <p class="text-[var(--text-muted)] text-sm lg:text-base"><?= $today ?></p>
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

const mealIcons = { breakfast: 'wb_twilight', lunch: 'light_mode', dinner: 'dark_mode' };
const mealLabels = { breakfast: 'Breakfast', lunch: 'Lunch', dinner: 'Dinner' };

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
        const divider = i < menu.length - 1 ? '<div class="h-px bg-white/5 w-full"></div>' : '';
        return `<section class="relative z-10" data-meal-id="${meal.id}">
            <div class="flex items-center gap-3 mb-2">
                <span class="material-symbols-outlined text-[var(--accent-purple)] text-xl">${icon}</span>
                <h2 class="text-lg font-bold tracking-wide uppercase text-white/90 text-sm">${label}</h2>
            </div>
            <p class="text-[var(--text-soft)] text-lg leading-relaxed pl-8">${escHtml(meal.items)}</p>
        </section>${divider}`;
    }).join('');

    // Per-meal reaction counts aggregated
    const totalLikes    = menu.reduce((s, m) => s + parseInt(m.likes    || 0), 0);
    const totalDislikes = menu.reduce((s, m) => s + parseInt(m.dislikes || 0), 0);

    // Current user's reaction on first item (use overall day reaction)
    // We'll use the dinner (last) meal for the day-level reaction button
    const lastMeal   = menu[menu.length - 1];
    const myReaction = lastMeal.my_reaction;

    container.innerHTML = `<div class="meal-card rounded-[32px] border border-white/5 p-8 lg:p-12 flex flex-col gap-10" style="background:var(--card-dark);position:relative;overflow:hidden;">
        <span class="material-symbols-outlined watermark-icon hidden sm:block">restaurant</span>
        ${sections}
        <div class="mt-4 pt-4 border-t border-white/5">
            <p class="text-[var(--text-muted)] text-xs text-center mb-4">How's the menu today?</p>
            <div class="flex gap-3">
                <button id="like-btn" data-meal-id="${lastMeal.id}" data-reaction="like"
                    class="reaction-btn flex-1 border rounded-full py-4 flex items-center justify-center gap-2
                    ${myReaction === 'like' ? 'bg-emerald-500/20 border-emerald-500/40 text-emerald-400' : 'bg-white/5 border-white/10 hover:bg-white/10 text-[var(--text-soft)]'}">
                    <span class="text-xl">👍</span>
                    <span class="font-semibold text-sm tracking-wide">Like</span>
                    <span class="text-xs text-[var(--text-muted)] ml-1" id="like-count">${totalLikes}</span>
                </button>
                <button id="dislike-btn" data-meal-id="${lastMeal.id}" data-reaction="dislike"
                    class="reaction-btn flex-1 border rounded-full py-4 flex items-center justify-center gap-2
                    ${myReaction === 'dislike' ? 'bg-red-500/20 border-red-500/40 text-red-400' : 'bg-white/5 border-white/10 hover:bg-white/10 text-[var(--text-soft)]'}">
                    <span class="text-xl">👎</span>
                    <span class="font-semibold text-sm tracking-wide">Dislike</span>
                    <span class="text-xs text-[var(--text-muted)] ml-1" id="dislike-count">${totalDislikes}</span>
                </button>
            </div>
        </div>
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

loadMess();
</script>
<?php pageFooter(); ?>

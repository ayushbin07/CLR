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
                </p>
                <textarea id="json-import" rows="6"
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white font-mono outline-none focus:border-[var(--accent-purple)]/50 resize-y"
                    placeholder='[{"class_id":1,"subject":"Math","room":"101","day_of_week":"Mon","start_time":"09:00","end_time":"10:30"}]'></textarea>
                <button id="json-import-btn"
                    class="mt-4 px-6 py-3 bg-[var(--accent-purple)] text-[#0F0F12] rounded-xl font-bold text-sm hover:opacity-90">
                    Import JSON
                </button>
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
</script>
</body>
</html>

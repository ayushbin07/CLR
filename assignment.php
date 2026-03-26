<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/layout.php';
requireAuth();
$user = auth();
$csrf = csrfToken();
$classes = db()->query('SELECT * FROM classes ORDER BY name')->fetchAll();

pageHead('Assignments');
topNav('assignment');
sidebar('assignment');
bottomNav('assignment');
?>
<main class="main-content">
    <div class="max-w-4xl mx-auto">
        <header class="px-6 pt-12 pb-6 lg:pt-16 lg:px-10">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-3xl lg:text-4xl font-bold tracking-tight mb-1">Assignments</h1>
                    <p class="text-[var(--text-muted)] text-sm lg:text-base">All your tasks in one place</p>
                </div>
                <button id="open-modal-btn" class="glassy-plus" aria-label="Add assignment">
                    <span class="material-symbols-outlined font-bold">add</span>
                </button>
            </div>
        </header>

        <!-- Filter tabs -->
        <section class="px-6 lg:px-10 mb-8">
            <div class="segmented-control flex items-center max-w-md">
                <button class="flex-1 py-2 text-sm font-medium segmented-item active text-[var(--accent-purple)]" data-filter="all">All</button>
                <button class="flex-1 py-2 text-sm font-medium segmented-item text-[var(--text-muted)]" data-filter="pending">Pending</button>
                <button class="flex-1 py-2 text-sm font-medium segmented-item text-[var(--text-muted)]" data-filter="completed">Completed</button>
            </div>
        </section>

        <!-- Assignment list -->
        <section class="px-6 lg:px-10">
            <div id="assignment-list" class="space-y-3 min-h-[100px]">
                <div class="neo-card p-8 text-center text-[var(--text-muted)] animate-pulse text-sm">Loading…</div>
            </div>
        </section>
    </div>
</main>

<!-- Floating Add (mobile) -->
<button id="open-modal-btn-mob" class="lg:hidden fixed bottom-32 right-6 glassy-plus glassy-plus-lg z-40">
    <span class="material-symbols-outlined text-3xl font-semibold">add</span>
</button>

<!-- ===================== MODAL ===================== -->
<div id="assignment-modal" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="w-full max-w-lg bg-[var(--card-dark)] rounded-[28px] border border-white/10 p-6 shadow-2xl">
        <div class="flex items-center justify-between mb-6">
            <h2 id="modal-title" class="text-lg font-bold">New Assignment</h2>
            <button id="close-modal-btn" class="text-[var(--text-muted)] hover:text-white">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div id="modal-msg" class="hidden mb-4 px-4 py-3 rounded-xl text-sm font-medium bg-red-500/10 text-red-400 border border-red-500/20"></div>

        <form id="assignment-form" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <input type="hidden" name="id" id="edit-id" value="">

            <div>
                <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Title *</label>
                <input type="text" name="title" id="f-title" required
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder:text-[var(--text-muted)] outline-none focus:border-[var(--accent-purple)]/50"
                    placeholder="e.g. Algorithm Analysis Report"/>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Subject</label>
                    <input type="text" name="subject" id="f-subject"
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder:text-[var(--text-muted)] outline-none focus:border-[var(--accent-purple)]/50"
                        placeholder="Computer Science"/>
                </div>
                <div>
                    <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Type</label>
                    <select name="type" id="f-type" class="w-full bg-[var(--bg-dark)] border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none focus:border-[var(--accent-purple)]/50">
                        <option value="assignment">Assignment</option>
                        <option value="presentation">Presentation</option>
                        <option value="project">Project</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Deadline *</label>
                    <input type="datetime-local" name="deadline" id="f-deadline" required
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none focus:border-[var(--accent-purple)]/50"/>
                </div>
                <div>
                    <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Visibility</label>
                    <select name="visibility" id="f-visibility" class="w-full bg-[var(--bg-dark)] border border-white/10 rounded-xl px-4 py-3 text-sm text-white outline-none focus:border-[var(--accent-purple)]/50">
                        <option value="class">My Class</option>
                        <option value="public">Public</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs text-[var(--text-muted)] uppercase tracking-widest mb-2">Reference Link</label>
                <input type="url" name="link" id="f-link"
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder:text-[var(--text-muted)] outline-none focus:border-[var(--accent-purple)]/50"
                    placeholder="https://…"/>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" id="close-modal-btn2"
                    class="flex-1 glassy-cta ghost text-sm">
                    Cancel
                </button>
                <button type="submit" id="modal-submit-btn"
                    class="flex-1 glassy-cta text-sm">
                    Save Assignment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const BASE     = <?= json_encode(BASE_URL) ?>;
const CSRF     = <?= json_encode($csrf) ?>;
const USER_ID  = <?= json_encode($user['id']) ?>;
const IS_ADMIN = <?= json_encode($user['role'] === 'admin') ?>;
let currentFilter = 'all';

// -----------------------------------------------
// Load list
async function loadAssignments(filter = 'all') {
    currentFilter = filter;
    const res  = await fetch(`${BASE}/api/assignments.php?action=list&filter=${filter}`);
    const list = await res.json();
    const container = document.getElementById('assignment-list');

    if (!list.length) {
        container.innerHTML = '<div class="neo-card p-10 text-center text-[var(--text-muted)] text-sm">Nothing here yet.</div>';
        return;
    }

    container.innerHTML = list.map((a) => {
        const dl = deadlineLabel(a.deadline);
        const isDone = a.status === 'completed';
        const strike = isDone ? 'line-through' : '';
        const canEdit = IS_ADMIN || a.created_by == USER_ID;
        const subjectLine = [escHtml(a.subject || 'General'), dl.text].filter(Boolean).join(' • ');
        const linkLine = a.link ? `<a href="${escHtml(a.link)}" target="_blank" class="text-[var(--accent-strong)] text-[11px] underline underline-offset-2">View link</a>` : '';
        return `<div class="assignment-row neo-card flex items-center justify-between gap-3" data-id="${a.id}">
            <div class="flex items-center gap-4 pl-1 min-w-0">
                <button class="custom-checkbox assignment-check ${isDone ? 'checked' : ''}" aria-label="${isDone ? 'Mark pending' : 'Mark complete'}" data-id="${a.id}">${isDone ? '<span class="material-symbols-outlined text-xs font-bold text-[#0F0F12]">check</span>' : ''}</button>
                <div class="min-w-0 space-y-0.5">
                    <h4 class="assignment-title truncate ${strike}">${escHtml(a.title)}</h4>
                    <p class="assignment-sub truncate">${subjectLine}</p>
                    ${linkLine}
                </div>
            </div>
            <div class="flex items-center gap-1.5 shrink-0 pl-2">
                ${canEdit ? `
                <button class="edit-btn action-btn" data-assignment='${JSON.stringify(a)}'>
                    <span class="material-symbols-outlined text-sm">edit</span>
                </button>
                <button class="delete-btn action-btn delete" data-id="${a.id}">
                    <span class="material-symbols-outlined text-sm">delete</span>
                </button>` : ''}
            </div>
        </div>`;
    }).join('');

    // Bind actions
    container.querySelectorAll('.assignment-check').forEach(btn => {
        btn.addEventListener('click', async () => {
            const fd = new FormData(); fd.append('id', btn.dataset.id); fd.append('csrf_token', CSRF);
            await fetch(`${BASE}/api/assignments.php?action=toggle`, { method:'POST', body:fd });
            loadAssignments(currentFilter);
        });
    });
    container.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            if (!confirm('Delete this assignment?')) return;
            const fd = new FormData(); fd.append('id', btn.dataset.id); fd.append('csrf_token', CSRF);
            await fetch(`${BASE}/api/assignments.php?action=delete`, { method:'POST', body:fd });
            loadAssignments(currentFilter);
        });
    });
    container.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const a = JSON.parse(btn.dataset.assignment);
            openModal(a);
        });
    });
}

// -----------------------------------------------
// Modal
const modal = document.getElementById('assignment-modal');
function openModal(assignment = null) {
    const form = document.getElementById('assignment-form');
    form.reset();
    document.getElementById('modal-msg').classList.add('hidden');

    if (assignment) {
        document.getElementById('modal-title').textContent = 'Edit Assignment';
        document.getElementById('edit-id').value     = assignment.id;
        document.getElementById('f-title').value     = assignment.title;
        document.getElementById('f-subject').value   = assignment.subject || '';
        document.getElementById('f-type').value      = assignment.type;
        document.getElementById('f-visibility').value= assignment.visibility;
        document.getElementById('f-link').value      = assignment.link || '';
        // Format deadline for input
        const d = new Date(assignment.deadline);
        const pad = n => String(n).padStart(2, '0');
        document.getElementById('f-deadline').value =
            `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
        document.getElementById('modal-submit-btn').textContent = 'Save Changes';
    } else {
        document.getElementById('modal-title').textContent = 'New Assignment';
        document.getElementById('edit-id').value = '';
        document.getElementById('modal-submit-btn').textContent = 'Save Assignment';
    }
    modal.classList.remove('hidden');
}

function closeModal() { modal.classList.add('hidden'); }

document.getElementById('open-modal-btn').addEventListener('click', () => openModal());
document.getElementById('open-modal-btn-mob').addEventListener('click', () => openModal());
document.getElementById('close-modal-btn').addEventListener('click', closeModal);
document.getElementById('close-modal-btn2').addEventListener('click', closeModal);
modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });

document.getElementById('assignment-form').addEventListener('submit', async e => {
    e.preventDefault();
    const form = e.target;
    const id   = document.getElementById('edit-id').value;
    const action = id ? 'update' : 'create';
    const btn  = document.getElementById('modal-submit-btn');
    btn.disabled = true; btn.textContent = 'Saving…';

    const fd = new FormData(form);
    const res = await fetch(`${BASE}/api/assignments.php?action=${action}`, { method:'POST', body:fd });
    const d   = await res.json();

    if (d.success) {
        closeModal();
        loadAssignments(currentFilter);
    } else {
        const msg = document.getElementById('modal-msg');
        msg.textContent = d.error || 'Error saving.';
        msg.classList.remove('hidden');
    }
    btn.disabled = false; btn.textContent = id ? 'Save Changes' : 'Save Assignment';
});

// -----------------------------------------------
// Filter tabs
document.querySelectorAll('.segmented-item').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.segmented-item').forEach(b => {
            b.classList.remove('active', 'text-[var(--accent-purple)]');
            b.classList.add('text-[var(--text-muted)]');
        });
        btn.classList.add('active', 'text-[var(--accent-purple)]');
        btn.classList.remove('text-[var(--text-muted)]');
        loadAssignments(btn.dataset.filter);
    });
});

// -----------------------------------------------
function deadlineLabel(deadline) {
    const d = new Date(deadline), now = new Date();
    const diff = Math.ceil((d - now) / (1000*60*60*24));
    if (diff < 0)  return { text: 'Overdue',       cls: 'text-red-400' };
    if (diff === 0) return { text: 'Due Today',     cls: 'text-[var(--accent-warm)]' };
    if (diff === 1) return { text: 'Due Tomorrow',  cls: 'text-[var(--accent-warm)]' };
    return { text: `Due ${d.toLocaleDateString('en-IN',{day:'numeric',month:'short'})}`, cls: 'text-[var(--text-muted)]' };
}
function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

loadAssignments();
</script>
<?php pageFooter(); ?>

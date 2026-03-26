# Project Gaps Report

- Missing includes path: several pages call `require_once __DIR__ . '/includes/...';` but the repo has no `includes/` directory. Affected files: [assignment.php](assignment.php#L1), [habits.php](habits.php#L1), [mess.php](mess.php#L1), [login.php](login.php#L1). Current `config.php` and `layout.php` sit at root, so these requires will fatal.
- Missing /api folder and endpoints: frontend requests `/sanctuary/api/...` but files live at root and many do not exist. Calls that will 404 or fail: `/sanctuary/api/auth.php`, `/sanctuary/api/assignments.php`, `/sanctuary/api/todos.php`, `/sanctuary/api/mess.php`, `/sanctuary/api/habits.php`, `/sanctuary/api/timetable.php`, `/sanctuary/api/settings.php`, `/sanctuary/api/home.php`. Only some backend scripts exist at root; none are under an `api/` path.
- Unimplemented APIs despite UI/PRD:
  - Habits: UI hits `/sanctuary/api/habits.php?action=list|create|log|heatmap|overall` but no such file exists.
  - Mess: UI hits `/sanctuary/api/mess.php?action=today|react|menu` but no such file exists.
  - Timetable: Admin UI posts to `/sanctuary/api/timetable.php?action=add|import`; file missing.
  - Settings: Settings page posts to `/sanctuary/api/settings.php?action=update_profile|change_password`; file missing.
  - Home dashboard: PRD and UI expect `/sanctuary/api/home.php` for assignments/classes/todos; file missing.
- Asset path mismatch: templates load `/sanctuary/assets/css/styles.css` and `/sanctuary/assets/js/app.js` (see [layout.php](layout.php#L12) and [login.php](login.php#L17)) but only [styles.css](styles.css) and [scripts.js](scripts.js) exist at project root; `app.js` is absent.
- Post-login landing is wrong: `auth.php` redirects to `/index.php`, but [index.php](index.php#L1) is an admin-only panel requiring `requireAdmin();`. There is no PHP home/dashboard for normal users (UI lives in [index.html](index.html)).
- DB config still uses local defaults and no environment overrides: [config.php](config.php#L1-L22) hardcodes host/user/password/db and lacks `.env` or runtime configuration.
- CSRF/session helpers exist but endpoints missing: [config.php](config.php#L39-L63) defines CSRF helpers; missing API files cannot verify tokens, leaving forms without backend protection until those endpoints are implemented.

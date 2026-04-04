# Sanctuary (CLR)
Student focus system for assignments, habits, timetable, mess menu, and PWA installability—built with PHP + MySQL on the backend and a lightweight HTML/JS/Tailwind frontend.

![App Icon](assets/icons/app-icon.png)

[![PHP](https://img.shields.io/badge/PHP-8+-777BB4?logo=php&logoColor=white)](#backend-php--mysql) [![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?logo=javascript&logoColor=000)](#frontend-javascript--html--css) [![MySQL](https://img.shields.io/badge/MySQL-8+-4479A1?logo=mysql&logoColor=white)](#database) [![Tailwind](https://img.shields.io/badge/Tailwind_CSS-CDN-06B6D4?logo=tailwindcss&logoColor=white)](#frontend-javascript--html--css) [![PWA](https://img.shields.io/badge/PWA-Installable-5A0FC8?logo=pwa&logoColor=white)](#pwa)

## Overview
- Authenticated student dashboard (todos, "important" section, hero spotlight, today's classes)
- Assignment manager with status per user (pending/completed) and visibility (class/public)
- Habits with streaks + 30/90-day heatmaps
- Mess menu with reactions (like/dislike), admin bulk import, and expanded meal types (breakfast, lunch, lunch_international, snacks, dinner)
- Timetable per class with admin add/replace/import
- Settings for profile/avatar/password with unified card UI and accordion toggles
- Avatar photo upload (click-to-upload with camera hover icon, DiceBear fallback)
- Community "Show Your Presence" reviews marquee with admin moderation
- Admin panel for hero cards, mess, timetable (toggleable form sections, mobile-responsive)
- PWA-ready (manifest + service worker) with install banner and offline fallback

## Tech Stack
### Backend (PHP & MySQL)
- PHP 8+, PDO, sessions, CSRF tokens (`includes/config.php`)
- REST-style JSON endpoints under `api/` (auth, todos, assignments, habits, mess, timetable, settings, hero_cards, home, reviews, upload_avatar)
- Auth helpers `requireAuth()` / `requireAdmin()` guard all app pages and APIs
- Secure file upload via `finfo` MIME validation (`api/upload_avatar.php`), 5 MB limit, auto-deletes old avatar on replacement

### Frontend (JavaScript / HTML / CSS)
- Vanilla JS modules in page templates and `assets/js/app.js`
- Styling via Tailwind CDN + custom theme in `assets/css/styles.css`
- Google Fonts (Inter) + Material Symbols icons; DiceBear avatars for profiles (fallback when no photo uploaded)
- GPU-accelerated CSS marquee animation (`transform`-only, `prefers-reduced-motion` degrades to `overflow-x: auto`)
- Native CSS Scroll Snapping (`snap-x snap-mandatory`) for mobile carousels

### Build Tooling
- Optional: `npm install` then `npm run build` (Vite bundles `scripts.js` / `styles.css` to `dist/`)
- No composer dependencies; PHP runs as-is on Apache/XAMPP/WAMP or `php -S`

## Architecture & Flow
- **Session auth**: Login/Register via `api/auth.php`; session data stored server-side; unauthenticated users redirected to `login.php`.
- **CSRF protection**: Hidden field/header (`csrfToken()` in PHP) required on mutating requests (todos, assignments, mess, timetable, habits, settings, reviews, avatar upload).
- **Page shell**: Shared head/nav/footer in `includes/layout.php`; `BASE_URL` auto-derives from folder path so the app works in nested directories.
- **Data flow**: Pages fetch JSON from `/api/*` and render in-place (e.g., home calls `api/home.php?action=timetable` and `api/assignments.php`).

### Key Modules
- **Home (`index.php`)**: Hero cards (`api/hero_cards.php`), todos (`api/todos.php`), "important" block (upcoming assignments + current meal), today's classes (`api/home.php?action=timetable`).
- **Assignments (`assignment.php`)**: CRUD with per-user status (`api/assignments.php`).
- **Habits (`habits.php`)**: Create, toggle logs, streaks, heatmaps (`api/habits.php`).
- **Mess (`mess.php`)**: Daily menu + like/dislike reactions (`api/mess.php`). Admin can save/import menus. Supports `breakfast`, `lunch`, `lunch_international`, `snacks`, `dinner`.
- **Timetable (`timetable.php`)**: Admin add/replace/import slots (`api/timetable.php`); users view via home.
- **Settings (`settings.php`)**: Unified profile/avatar/password card; click-to-upload avatar with camera icon; "Change Password" accordion; "Show Your Presence" reviews section with inline form and infinite marquee.
- **Admin (`admin.php`)**: Manage hero cards, mess menus, timetable slots via toggleable form sections (CSRF + admin check, mobile layout fixed).

### API Surface (high level)
- `api/auth.php`: `login`, `register`, `logout`
- `api/todos.php`: `list`, `create`, `update`, `toggle`, `delete`
- `api/assignments.php`: `list`, `create`, `update`, `delete`, `toggle`
- `api/habits.php`: `list`, `heatmap`, `overall`, `create`, `log`, `delete`
- `api/mess.php`: `today`, `react`, `menu`, `import`
- `api/timetable.php` (admin): `add`, `import`, `replace`, `list`, `delete`
- `api/settings.php`: `update_profile`, `change_password`
- `api/hero_cards.php`: `list`, `save` (admin), `delete` (admin)
- `api/home.php`: `dashboard`, `timetable`
- `api/reviews.php`: `list`, `add` (CSRF), `delete` (admin + CSRF)
- `api/upload_avatar.php`: multipart upload, validates MIME, stores to `uploads/avatars/`

## Database
- Schema and seed data: `extra/schema.sql` (creates users/classes/assignments/todos/habits/mess/timetable/hero_cards, plus demo admin and student).
- Configure DB connection in `includes/config.php` (host/user/pass/db/port, timezone, `BASE_URL` detection).

### Required Migrations (run after initial schema)

**1 — Expanded Mess Meal Types**
```sql
ALTER TABLE mess_menu
  MODIFY COLUMN meal_type ENUM('breakfast','lunch','lunch_international','snacks','dinner') NOT NULL;
```

**2 — Avatar Photo Upload Column**
```sql
ALTER TABLE users ADD COLUMN avatar_path VARCHAR(255) NULL DEFAULT NULL AFTER avatar_style;
```
When `NULL`, the app falls back to the DiceBear generated avatar automatically.

**3 — Community Reviews Table**
```sql
CREATE TABLE IF NOT EXISTS reviews (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    comment     TEXT NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## Setup (Local / XAMPP / WAMP / PHP built-in)
1. **Prereqs**: PHP 8+, MySQL 8+; optional Node 18+ for Vite build.
2. **Database**: Create DB (default `sanctuary`) and import `extra/schema.sql`, then run the three migrations above.
3. **Config**: Update `includes/config.php` DB constants to match your environment (defaults suit XAMPP: user `root`, empty password).
4. **Avatar uploads folder**: Ensure `uploads/avatars/` exists and is writable (CHMOD 755).
5. **Serve**:
   - Apache/XAMPP/WAMP: place the repo under webroot (e.g., `htdocs/CLR`) and browse to `http://localhost/CLR/login.php`.
   - PHP built-in: `php -S localhost:8000 -t /path/to/CLR` then open `http://localhost:8000/login.php`.
6. **Login**: Demo users from seed data (admin `admin@sanctuary.dev` / `admin123`, student `ayush@sanctuary.dev` / `student123`). Change passwords after first login.
7. *(Optional)* **Frontend build**: `npm install && npm run build` (outputs `dist/` for static hosting); not required for PHP-serving mode.

### InfinityFree / Shared Hosting Notes
- Run migrations via phpMyAdmin.
- Add to `.htaccess` to raise upload limits:
  ```
  php_value upload_max_filesize 5M
  php_value post_max_size 6M
  ```

## PWA
- Manifest: `manifest.json` (start_url/scope are relative: `./`), icons in `assets/icons/`.
- Service worker: `service-worker.js` (network-first caching, offline fallback), registered dynamically in `assets/js/app.js` for nested paths.
- Install banners: automatic on home/settings when `beforeinstallprompt` fires; manual trigger via "Install" button.
- Troubleshooting & test steps: see `PWA_TESTING.md`.

## Project Structure (excerpt)
```
assets/css/styles.css      # Theme + components
assets/js/app.js           # PWA install + shared UI hooks
includes/config.php        # DB, sessions, auth helpers, CSRF, BASE_URL
includes/layout.php        # Shared head/sidebar/footer (prefers avatar_path over DiceBear)
api/                       # JSON endpoints (auth, todos, assignments, habits, mess,
                           #   timetable, settings, hero_cards, home, reviews, upload_avatar)
index.php / *.php          # Pages: home, login, assignments, habits, mess, settings, admin, timetable
manifest.json, service-worker.js
extra/schema.sql           # DB schema + seed data
uploads/avatars/           # User-uploaded profile photos (gitignored, create manually)
```

## Development Notes
- Mutating API calls require `csrf_token` from PHP helper.
- Keep app under HTTPS (or localhost) for PWA installability.
- `BASE_URL` is auto-computed; avoid hardcoding absolute paths.
- Reviews marquee uses `overflow-x: clip; overflow-y: visible` on `#reviews-outer` so card shadows bleed vertically without horizontal scroll. If the page background color changes, update the `#f2f4fb` gradient stops in the `::before`/`::after` pseudo-elements on `.reviews-fade-wrap`.
- All interactive elements enforce ≥ 44 px touch targets and `focus-visible` rings (`focus-visible:ring-[var(--accent-purple)]/50`) for keyboard accessibility.

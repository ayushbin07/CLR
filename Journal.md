# Project Journal

This file contains specific instructions and context for this project, serving a similar purpose to a `GEMINI.md` file.

## Context
* Add project-specific context here.

## Instructions
* Add instructions here.

## Database Migration: Mess Redesign
Run the following SQL to update the existing `mess_menu` table to support the new meal types (`lunch_international` and `snacks`) without losing existing data:

```sql
USE sanctuary;

-- Update the enum column to include the new meal types
ALTER TABLE mess_menu 
MODIFY COLUMN meal_type ENUM('breakfast','lunch','lunch_international','snacks','dinner') NOT NULL;

-- (Optional) Sample data for the new sections for today
INSERT IGNORE INTO mess_menu (date, meal_type, items) VALUES
(CURDATE(), 'lunch_international', 'Spaghetti Aglio e Olio, Garlic Bread, Fresh Garden Salad'),
(CURDATE(), 'snacks', 'Samosa, Mint Chutney, Masala Chai');
```

## April 5, 2026: UI/UX Refinements (Admin Panel & Settings)
During this session, we focused on refining the mobile-responsiveness and reducing visual clutter across the application's administrative and profile pages:
* **Database Updates**: Added `lunch_international` and `snacks` choices to the `meal_type` ENUM in the `mess_menu` table to support the expanded mess menu format.
* **Admin Panel Responsiveness**: 
  * Fixed layout blowouts caused by long JSON text fields stretching past the viewport bounds by adopting flexible min-width classes (`min-w-0`) across CSS grid elements.
  * Replaced persistently open forms (e.g., 'Add Hero Cards', 'Update Mess Menu/Timetable') with toggleable sections triggered by buttons.
* **Settings Page Adjustments**:
  * Merged the static Profile Details section and the Profile Edit Form into a single unified card component, activated by a sleek pencil (edit) icon.
  * Refactored the "Change Password" section into an elegant, collapsed-by-default accordion toggle.
  * Regained button styling consistency across the settings forms by applying the standard `glassy-cta` design system class.
* **A11y and UX Enhancements**:
  * Implemented strict Fitts's Law compliance by ensuring minimum 44px touch targets on all interactive elements across `index.php`, `admin.php`, and `settings.php`.
  * Added uniform `focus-visible` rings (`focus-visible:ring-[var(--accent-purple)]/50`) to all buttons, inputs, selects, and textareas for robust keyboard navigability.
  * Replaced custom JavaScript drag-to-scroll behaviors with native CSS Scroll Snapping (`snap-x snap-mandatory`), severely enhancing mobile scroll performance.

## ⚠️ DB Migration Required: Avatar Photo Upload
You MUST run this SQL on your database before the avatar upload feature works.
Only **one column** needs to be added to the `users` table:

```sql
ALTER TABLE users ADD COLUMN avatar_path VARCHAR(255) NULL DEFAULT NULL AFTER avatar_style;
```

**What this column does:**
- Stores the relative file path to the user's uploaded photo (e.g., `uploads/avatars/user_5.jpg`)
- When `NULL`, the app falls back to the existing DiceBear generated avatar automatically
- No other table changes needed — this is additive and non-breaking

**InfinityFree deployment checklist:**
1. Run the SQL above via phpMyAdmin on your InfinityFree database
2. Create the folder `uploads/avatars/` in your web root (already created locally)
3. Make sure the folder has write permissions (CHMOD 755 is fine)
4. Upload limit on InfinityFree is typically 2MB via PHP ini — override in `.htaccess`:
   ```
   php_value upload_max_filesize 5M
   php_value post_max_size 6M
   ```

## April 5, 2026: Avatar Photo Upload Feature
- Added `api/upload_avatar.php` — handles file upload, validates MIME type securely via `finfo`, enforces 5MB limit with a funny error message, and stores the file in `uploads/avatars/`
- Updated `settings.php` — avatar circle is now clickable (shows camera icon on hover), triggers a hidden file input, shows optimistic preview while uploading, and falls back to DiceBear on load failure (`onerror`)
- Updated `includes/layout.php` sidebar — prefers `avatar_path` over DiceBear with `onerror` fallback wired in
- Old uploaded file is deleted from disk when a new one is uploaded (no orphaned files)

## ⚠️ DB Migration Required: Reviews Table
Run this SQL to create the reviews table:

```sql
CREATE TABLE IF NOT EXISTS reviews (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    comment     TEXT NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**What this table does:**
- Stores user-submitted community reviews, linked to their user account
- `ON DELETE CASCADE` — if a user is deleted, their reviews are also removed automatically
- No other table changes needed

## April 5, 2026: Show Your Presence — Reviews Marquee
- Added `api/reviews.php` with `list`, `add` (CSRF protected), and `delete` (admin-only, CSRF protected) actions
- Added "Show Your Presence" section in `settings.php` just before the logout button:
  - Section title + **Write a Review** button on the far right
  - Inline review form (toggle open/close, live char counter, 300 char limit)
  - Infinite marquee of review cards scrolling right-to-left (linear, GPU-accelerated transform-only animation)
  - Cards use `neo-card` class — same skeuomorphic style as Today's Classes cards (`var(--surface)` white, layered box-shadow, hover lift)
  - Cards show: user avatar (uploaded or DiceBear fallback), user name, class, comment
  - Admin gets a red **Delete** button on each card
  - Hover pauses the marquee (`animation-play-state: paused`)
  - `prefers-reduced-motion` degrades to plain `overflow-x: auto` scroll

### Marquee Polish (refinements applied same session)
- **Animation speed**: `35s` → `12s` (original was too slow)
- **Shadow clipping**: Tried `overflow:hidden` + padding (failed), `mask-image` (clipped Y shadows), absolute overlay divs (color mismatch). Final solution: `overflow-x: clip; overflow-y: visible` on `#reviews-outer` so horizontal overflow is clipped but card shadows bleed upward/downward freely
- **Edge fade**: `::before`/`::after` pseudo-elements on `.reviews-fade-wrap` wrapper. Gradient color is `#f2f4fb` (matches actual content background, not `--bg`)
- **Textarea input color**: Changed from hardcoded `text-white` to `text-[var(--text-soft)]` to match theme
- **Removed** "Hover to pause" hint label
- **Vertical padding**: `#reviews-track` set to `py-8` (2rem) so shadows breathe without being clipped

### Key CSS Notes
```css
/* In settings.php <style> block */
#reviews-outer { overflow-x: clip; overflow-y: visible; }
.reviews-fade-wrap::before { background: linear-gradient(to right, #f2f4fb, transparent); }
.reviews-fade-wrap::after  { background: linear-gradient(to left,  #f2f4fb, transparent); }
```
> If page background changes, update `#f2f4fb` in the two gradient lines above.

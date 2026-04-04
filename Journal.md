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

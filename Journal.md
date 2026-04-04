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

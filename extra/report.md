# Project Gaps Report (updated)

- Core structure now present: `includes/` folder and all referenced APIs exist under [api/](api), and assets are resolved (`assets/js/app.js` and `assets/css/styles.css`). Login now routes to a user dashboard, not an admin-only page.
- Database configuration is still hardcoded for local defaults in [includes/config.php](includes/config.php#L4-L22); add environment-based overrides (.env) for deployment and secrets management.
- New hero cards feature requires the `hero_cards` table (see [schema.sql](schema.sql)); ensure the migration is applied in deployed databases.
- Session cookie `secure` flag is disabled in [includes/config.php](includes/config.php#L36-L45); set to true when serving over HTTPS in production.

## Habits Debug Report (latest)

- Fixed the inline JS syntax error in [habits.php](habits.php) by removing stray HTML in the script, restoring modal open/close/submit handlers, and removing the old "Habits interactions are disabled" stub.
- Wrapped habits JS in DOMContentLoaded, added `fetchJson` helper with invalid-JSON logging, and load-time error UI if API calls fail.
- Added `credentials: 'same-origin'` to all habit fetches (list/overall/heatmap/create/log/delete) and defensive JSON parsing + alerts for create/log/delete; toggle clicks now log `Toggle click <id>` before POST.
- Tailwind CDN warning is informational only; not a blocker for habits.

Current status: GET heatmap returns 200 OK, but POST create/log/delete still do not reflect changes (likely redirect/CSRF/server error). Need Network+console capture of those POST responses (status + body) after clicking a habit or creating one.


## Add a new time-aware card.
Add a Important section on homescreen. 
It has a card that shows all the neardeadline assignment/mess 

mess is divided on 3 type Breakfast timing is (07:00 AM - 08:50 AM), Lunch (12:00 PM to 02:00 PM), Dinner(07:00 PM - 08:30 PM) So the cards will display the mess items with the mealtype. I told you the timing.

The assignment should appear on the Important card when it is 6 Hours before deadline.

First make the card I will verify visually then implement logic.
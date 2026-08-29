---
paths:
  - 'app/Http/Controllers/Backend/EmailTemplateController.php,resources/js/pages/email-templates/**,database/seeders/EmailTemplateSeeder.php'
---

# Email Templates Seeders

## Email templates redesigned + seeded (2026-08-29)
Create/Edit rebuilt from the old flat `max-w-xl` form into the Card-sectioned `grid lg:grid-cols-3` layout (same grammar as [[business-settings]]/products Edit): "Template details" (name/subject), "Content" (body via RichTextEditor, replacing the plain Textarea — body is stored/rendered as HTML now), "Status" (Active toggle row), sticky "Publish" card with Save + Cancel. Index kept the app-wide bare `overflow-x-auto` table convention (not a Card-wrapped table — that would break consistency with every other Index page) but added a Preview action: an Eye-icon button opening a Dialog that renders the template body with `v-html` inside a div using Tailwind arbitrary-variant descendant selectors (`[&_h2]:...`, `[&_p]:...`, `[&_ul]:...`) for spacing — there's no `@tailwindcss/typography` plugin installed and no existing `prose`/v-html pattern elsewhere in resources/js/pages, so this is the first place doing this; reuse the same class string rather than adding the typography plugin.

Seeding: `database/seeders/EmailTemplateSeeder.php` (called from DatabaseSeeder after OrderSeeder) seeds exactly 3 templates — "Order confirmation", "Order shipped", "Welcome new customer" — via `updateOrCreate(['name' => ...], ...)` keyed on the unique `name` column, so re-running `db:seed` or `migrate:fresh --seed` is idempotent (verified: running it twice still leaves exactly 3 rows). Bodies use `{{token}}` placeholders (e.g. `{{order_number}}`, `{{customer_name}}`, `{{site_name}}`) as plain seed content only — there is no merge/substitution engine and EmailTemplate isn't referenced by any Mail/Notification class anywhere in the app (grepped, zero hits outside its own CRUD files), so these are purely admin-configured records, not wired to actually send yet. Don't assume sending them does anything until that integration is built.

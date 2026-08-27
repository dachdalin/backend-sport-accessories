---
paths:
  - 'app/Http/Controllers/Backend/MessageController.php,resources/js/pages/messages/**,database/migrations/**'
---

# Messages Migrations

## Internal Messages built (2026-08-27) — staff-to-staff 1:1 chat
Built internal messaging between User accounts (backend staff) — scope confirmed with user: 1:1 direct messages only (no group channels), text only (no attachments). Deliberately did NOT reuse the reference dump's `chattings` table (database/backend-sport-accessorie.sql:283) — it's marketplace-flavored (seller_id/admin_id/delivery_man_id), doesn't apply to this single-vendor app per existing backend-migrations.md guidance.

messages table: sender_id/receiver_id (both FK users cascadeOnDelete), body (text), read_at (nullable timestamp), indexes on [sender_id,receiver_id] and [receiver_id,read_at]. App\Models\Message belongsTo sender/receiver (both User). App\Http\Requests\Backend\StoreMessageRequest blocks messaging yourself via Rule::notIn([$this->user()->id]) on receiver_id. App\Actions\Messages\SendMessageAction (trivial but kept per Action-layering convention). App\Services\MessageService (flat namespace like WishlistService, not Services\Messages\) owns the read-heavy logic: conversations() (all other users + last message + unread count, computed in PHP via Collection::groupBy rather than raw SQL GREATEST/LEAST — this app's tests run on SQLite while prod is MySQL, and those functions aren't portable between the two), thread(), markRead(). Route: `messages` resource, only index/store — no show/edit/destroy (messages aren't edited or individually deleted).

Frontend: single resources/js/pages/messages/Index.vue, two-pane layout (conversation roster + thread), built entirely from existing shadcn-vue primitives and design tokens — no new colors/components. Polls via Inertia v3's `usePoll(4000, { only: ['conversations','messages'] })` (no websockets/broadcasting configured in this app). Sidebar entry added to the "Overview" nav group (not "Administrator") since it's a daily-use tool, not admin configuration. "Internal only" is enforced simply by sitting behind the existing `auth`+`verified` middleware group — no separate ACL needed since only backend User accounts can reach any backend route at all.

Don't rebuild — extend the existing files instead.

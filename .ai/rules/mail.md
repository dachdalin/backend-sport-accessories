---
paths:
  - 'app/Http/Controllers/Backend/NewsletterSubscriberController.php,app/Mail/**,app/Actions/NewsletterSubscribers/**,resources/js/pages/newsletter-subscribers/**,resources/views/mail/**'
---

# Mail

## Newsletter send-to-one/send-all built (2026-08-30) — first Mailable in the app
Added real email sending to newsletter subscribers, compose-on-the-fly (no template picker — user chose free-form over reusing [[email-templates-seeders]], whose templates still aren't wired to anything). `App\Mail\NewsletterMail` is the first Mailable class in this codebase (implements ShouldQueue + afterCommit(), envelope()/content() pattern, view `resources/views/mail/newsletter.blade.php`).

Trap: don't name a Mailable's constructor property `$subject` — `Illuminate\Mail\Mailable` already declares `public $subject` (non-readonly), so a `public readonly string $subject` promoted property fatals with "Cannot redeclare non-readonly property ... as readonly". Named it `$emailSubject` instead; envelope() still does `new Envelope(subject: $this->emailSubject)`.

`App\Actions\NewsletterSubscribers\SendNewsletterAction::handle(Collection $subscribers, array $data)` takes a plain `Illuminate\Support\Collection` (not Eloquent's) so the controller can pass either `collect([$newsletterSubscriber])` for a single send or a real `NewsletterSubscriber::query()->where('status', true)->get()` for send-all without type friction. `$data` is `['subject' => ..., 'body' => ...]` from `SendNewsletterRequest`.

Routes (both POST, gated `permission:edit newsletter subscribers` — reused the existing CRUD "edit" permission rather than adding a new one): `newsletter-subscribers/send-all` (`.send-all`) and `newsletter-subscribers/{newsletter_subscriber}/send` (`.send`), registered right after the `$gateCrud(...)->only([...])` resource line. Controller's `index()` now also returns `subscribedCount` (status=true count) so Index.vue can show/disable the "Send newsletter" button and pluralize "N subscribed address(es)" without a client-side computation.

Frontend: two Dialogs on Index.vue reusing the create/edit dual-Dialog convention already on this page — a header "Send newsletter" Button (Send icon, disabled + titled when `subscribedCount === 0`) and a per-row Mail-icon Button (disabled + titled when `!subscriber.status`, can't email an unsubscribed address) opening a `sendingToSubscriber` ref-driven Dialog. Both compose forms are identical: `Input name="subject"` + `RichTextEditor name="body"` (same component/pattern as [[email-templates-seeders]]), `reset-on-success` + `@success` closes the dialog.

Verified end-to-end for real (not just `Mail::fake()`): filled the per-row dialog in a live browser, submitted, and confirmed the queued job actually rendered and wrote the full HTML email (correct subject, correct RichTextEditor body) to `storage/logs/laravel.log` via the `log` mailer + `database` queue connection + the `queue:listen` worker started by `composer run dev`.

Don't rebuild — extend the existing files instead.

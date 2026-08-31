---
paths:
  - 'app/Http/Controllers/Backend/ShippingAddressController.php,resources/js/pages/shipping-addresses/**'
---

# Shipping Addresses

## Shipping address Create/Edit redesigned as Card-sectioned forms with a live label preview (2026-08-31)
Rebuilt from the old flat `max-w-xl` single-column form into the Card-sectioned `grid gap-6 lg:grid-cols-3 lg:items-start` layout (same convention as [[store-locations]]/blogs/flash-deals): "Recipient" (customer select, contact person, phone) and "Delivery address" (address type, address, city/state, zip/country) in the `lg:col-span-2` main column; a "Shipping label" card in the sidebar is the signature element — a live mock parcel label (dashed border, "SHIP TO" mono eyebrow, address-type stamp, CSS-drawn barcode strip) plus the "Set as default" toggle, then a sticky "Save" card.

`address_type` switched from a `Select` to a 3-way icon radio-pill group (House/Building2/MapPin from `@lucide/vue`), same `has-[:checked]` + invisible-overlay-`<input type=radio>` pattern as the priority/status pills in `support-tickets/Create.vue` — reuse that pattern (not a new one) for any other small fixed-choice field.

The label preview needs live values, so text `Input`s and the `is_default` `Checkbox` are now paired with local `ref`s via `v-model` *in addition to* their `name` attribute — `name` still drives the actual Inertia `<Form>` submission (FormData reads the DOM inputs by name regardless of v-model), the refs only feed the preview. The `customer_id` `Select` was deliberately left uncontrolled (`default-value`, no v-model) since the customer account doesn't appear on the label mock — don't wire it up "for consistency" without a reason.

Also fixed the `is_default` `Checkbox` missing `value="1"` on Create.vue (see [[checkbox]]) while touching this file.

Verified: vue-tsc/eslint clean, `npm run build` clean, `ShippingAddressControllerTest` 9/9 pass, and a live-browser check of the Create page confirming the label preview updates as Recipient/Address fields are typed and the default star/border toggle when "Set as default" is checked. Index.vue (the table) was intentionally left untouched — this redesign was scoped to Create/Edit only.

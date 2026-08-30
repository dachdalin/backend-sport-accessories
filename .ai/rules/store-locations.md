---
paths:
  - 'app/Http/Controllers/Backend/StoreLocationController.php,resources/js/pages/store-locations/**'
---

# Store Locations

## Store locations Create/Edit redesigned as Card-sectioned forms (2026-08-30)
Rebuilt from the old flat `max-w-xl` single-column form into the Card-sectioned `grid gap-6 lg:grid-cols-3 lg:items-start` layout (same convention as [[blogs]]/[[flash-deals]]/products) — proportioned down for this lighter 6-field resource: "Location details" (name, address, city — city capped `sm:max-w-xs` so it doesn't stretch full-width on large screens) and "Contact & hours" (phone + opening hours, `sm:grid-cols-2`) in the `lg:col-span-2` main column; "Visibility" (Active toggle row) and sticky "Publish" in the sidebar. No image field on this model, so no ImageDropzone/RichTextEditor needed here — kept to Input-only fields, matching the resource's actual complexity rather than padding it out.

Also fixed the pre-existing `status` Checkbox missing `value="1"` (see [[checkbox]]) on both pages while touching them.

Verified: eslint/prettier/types/build clean, `StoreLocationControllerTest` 8/8 pass, and a full live-browser round trip at a large-device width (1600px) — created a location, confirmed it landed correctly on the Index with the right Active status, reopened Edit and confirmed every field prefilled, then deleted it. No console errors. Don't rebuild — extend the existing files instead.

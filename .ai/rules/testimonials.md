---
paths:
  - 'app/Http/Controllers/Backend/TestimonialController.php,app/Models/Testimonial.php,resources/js/pages/testimonials/**'
---

# Testimonials

## Testimonial Create/Edit redesigned as Card-sectioned forms + avatar_url accessor (2026-08-30)
Rebuilt from the old flat `max-w-xl` single-column form into the Card-sectioned `grid gap-6 lg:grid-cols-3 lg:items-start` layout (same convention as [[blogs]]/[[store-locations]]/[[flash-deals]]/products): "Customer" (name+role, sm:grid-cols-2) and "Testimonial" (content + rating) in the lg:col-span-2 main column; "Photo" (avatar), "Visibility" (Active toggle), sticky "Publish" in the sidebar.

`rating` upgraded from a bare number input to `resources/js/components/StarRatingInput.vue` — a new reusable field: 5 real native `<input type="radio" name="rating">` elements, each nested INSIDE its own `<label>` (not adjacent siblings using `for`/`peer`). Nesting is required, not stylistic: sr-only radios as flex-item siblings of visible labels all collapse to the same static position (browsers don't apply flex layout to out-of-flow/absolutely-positioned children), so every star lands on one point and clicks miss. Nesting ties each radio's position to its own label. `avatar` upgraded to `ImageDropzone` (drag/drop + preview), Edit passes `:initial-previews="[props.testimonial.avatar_url]"`.

Added `Testimonial::avatarUrl()` accessor (`$appends`) resolving `Storage::disk($this->avatar_storage_type)->url($this->avatar)` — same logic `TestimonialResource` already had, now also available to backend Inertia pages (Index/Edit), replacing the old hardcoded `/storage/${avatar}` path that broke for any row stored on the `cloudinary` disk (post d78cfb7 migration). This class of bug likely also affects banners/blogs/team-members Edit pages, which still hardcode `/storage/`.

Note: the seeded default avatar `def.png` has no actual file under storage/app/public — its preview 404s regardless of this fix (pre-existing, unrelated to Cloudinary).

Verified: Pint/types/lint/build clean, `TestimonialControllerTest` + `Api/V1/TestimonialControllerTest` 14/14 pass, and a full live-browser round trip (create with 4/5 rating + avatar → correct row on Index → Edit prefills every field including rating and avatar preview → deleted).

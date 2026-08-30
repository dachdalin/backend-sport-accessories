---
paths:
  - 'app/Http/Controllers/Backend/BlogController.php,resources/js/pages/blogs/**'
---

# Blogs

## Blog Create/Edit redesigned as Card-sectioned forms (2026-08-30)
Rebuilt from the old flat `max-w-xl` single-column form into the Card-sectioned `grid gap-6 lg:grid-cols-3 lg:items-start` layout (same convention as [[flash-deals]]/[[email-templates-seeders]]/products): "Details" (title/category/writer, category+writer paired `sm:grid-cols-2`), "Content", "Media" in the `lg:col-span-2` main column; "Schedule" (publish date + Published toggle row) and sticky "Publish" (Rocket icon) in the sidebar.

`description` (the blog body — despite the generic field name, it's the only content field on the model) upgraded from a plain `Textarea` to `RichTextEditor`, matching the same upgrade already done for email-templates. `image` upload upgraded from a bare `<input type=file>` to `ImageDropzone` (drag/drop + preview), Edit passes `:initial-previews="[\`/storage/${blog.image}\`]"`.

Also fixed the pre-existing `is_published` Checkbox missing `value="1"` (see [[checkbox]]) on both pages while touching them.

Verified: types/lint/build/Pint clean, `BlogControllerTest` 9/9 pass, and a full live-browser round trip (create with rich-text body → appears in Index → Edit page correctly prefills the rich-text content from saved HTML → delete). Don't rebuild — extend the existing files instead.

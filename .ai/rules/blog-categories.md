---
paths:
  - 'app/Http/Controllers/Backend/BlogCategoryController.php,resources/js/pages/blog-categories/**'
---

# Blog Categories

## Blog categories index is paginated, like wishlists/contacts
BlogCategoryController@index uses ->paginate(15)->withQueryString(), not ->get(). blog-categories/Index.vue expects `blogCategories` prop as `{ data: BlogCategory[], links: {url,label,active}[] }` and renders links same as gift-cards/Index.vue, wishlists/Index.vue. Test asserts via `has('blogCategories.data', n)`.

---
paths:
  - 'app/Http/Controllers/Backend/ContactController.php,resources/js/pages/contacts/**'
---

# Contacts

## Contacts index is paginated, like gift-cards/feature-deals
ContactController@index uses ->paginate(15)->withQueryString(), not ->get(). contacts/Index.vue expects `contacts` prop as `{ data: Contact[], links: {url,label,active}[] }` and renders links same as gift-cards/Index.vue. Test asserts via `has('contacts.data', n)`.

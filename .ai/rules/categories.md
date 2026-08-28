---
paths:
  - 'resources/js/pages/categories/**'
---

# Categories

## Categories Create/Edit redesigned to match Users/Roles card style (2026-08-28)
Create.vue/Edit.vue rewritten from flat Heading-divided sections to the sectioned-Card pattern established in users/ and roles/ pages (see [[users]]): two Cards — "Identity" (Tag icon: name + icon upload) and "Organization" (FolderTree icon: parent_id select, position, home_status), form actions moved into the last Card's CardFooter with a border-t. Index.vue was left as-is — it already matched the table/Badge/Dialog-delete/pagination pattern used by users/Index.vue, no change needed there. If migrating another resource's Create/Edit to this style, categories is now a second reference alongside users/roles for the file-upload + select-field variant.

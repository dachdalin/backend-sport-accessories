---
paths:
  - 'resources/js/pages/roles/**,resources/js/components/PermissionMatrix.vue'
---

# Roles Js Components

## Role permission picker is a grouped matrix, not a flat checkbox grid
roles/Create.vue and roles/Edit.vue no longer render permissions as a flat grid. They use `resources/js/components/PermissionMatrix.vue`, which parses each permission's name via `/^(view|create|edit|delete) (.+)$/` to build a resource x action table (rows = resource, columns = view/create/edit/delete). Names that don't match the pattern (e.g. "view dashboard", "edit business settings") fall into an "Other access" chip list below the table.

Component is v-model:selected="ref<number[]>" — pages own the selected-ids array (Edit.vue seeds it from role.permissions). Leaf action checkboxes carry name="permissions[]" :value="id" (same wire format RoleController already expects); row/column/master checkboxes are pure UI (no name), toggling many ids at once via boolean|'indeterminate' state.

Grouping is 100% derived from permission name text at runtime — no hardcoded resource list in the frontend. If a new CRUD resource is added via RolePermissionSeeder's findOrCreate("{action} {resource}"), it appears in the matrix automatically. Don't reintroduce a flat `v-for="permission in permissions"` grid or a hardcoded resource-category map; extend the regex/general bucket instead if a new permission naming shape is introduced.

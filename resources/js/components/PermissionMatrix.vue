<script setup lang="ts">
import { Search } from '@lucide/vue';
import { computed, ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';

type Permission = {
    id: number;
    name: string;
};

const ACTIONS = ['view', 'create', 'edit', 'delete'] as const;
type Action = (typeof ACTIONS)[number];

const ACTION_LABELS: Record<Action, string> = {
    view: 'View',
    create: 'Create',
    edit: 'Edit',
    delete: 'Delete',
};

type MatrixRow = {
    resource: string;
    label: string;
    actions: Partial<Record<Action, Permission>>;
};

const ACTION_PATTERN = /^(view|create|edit|delete) (.+)$/;

const props = defineProps<{
    permissions: Permission[];
}>();

const selected = defineModel<number[]>('selected', { default: () => [] });

const query = ref('');

const selectedSet = computed(() => new Set(selected.value));

function isSelected(id: number): boolean {
    return selectedSet.value.has(id);
}

function setSelected(ids: number[], checked: boolean) {
    const next = new Set(selected.value);

    for (const id of ids) {
        if (checked) {
            next.add(id);
        } else {
            next.delete(id);
        }
    }

    selected.value = Array.from(next);
}

const { rows, general } = (() => {
    const map = new Map<string, MatrixRow>();
    const generalPermissions: Permission[] = [];

    for (const permission of props.permissions) {
        const match = permission.name.match(ACTION_PATTERN);

        if (!match) {
            generalPermissions.push(permission);
            continue;
        }

        const [, action, resource] = match;

        if (!map.has(resource)) {
            map.set(resource, {
                resource,
                label: resource.charAt(0).toUpperCase() + resource.slice(1),
                actions: {},
            });
        }

        map.get(resource)!.actions[action as Action] = permission;
    }

    return {
        rows: Array.from(map.values()).sort((a, b) =>
            a.label.localeCompare(b.label),
        ),
        general: generalPermissions,
    };
})();

const filteredRows = computed(() => {
    const q = query.value.trim().toLowerCase();

    if (!q) {
        return rows;
    }

    return rows.filter((row) => row.label.toLowerCase().includes(q));
});

function rowIds(row: MatrixRow): number[] {
    return Object.values(row.actions)
        .filter((permission): permission is Permission => !!permission)
        .map((permission) => permission.id);
}

function rowState(row: MatrixRow): boolean | 'indeterminate' {
    const ids = rowIds(row);
    const checkedCount = ids.filter((id) => isSelected(id)).length;

    if (checkedCount === 0) {
        return false;
    }

    return checkedCount === ids.length ? true : 'indeterminate';
}

function toggleRow(row: MatrixRow, checked: boolean) {
    setSelected(rowIds(row), checked);
}

function columnIds(action: Action): number[] {
    return rows
        .map((row) => row.actions[action]?.id)
        .filter((id): id is number => id !== undefined);
}

function columnState(action: Action): boolean | 'indeterminate' {
    const ids = columnIds(action);
    const checkedCount = ids.filter((id) => isSelected(id)).length;

    if (checkedCount === 0) {
        return false;
    }

    return checkedCount === ids.length ? true : 'indeterminate';
}

function toggleColumn(action: Action, checked: boolean) {
    setSelected(columnIds(action), checked);
}

const allIds = computed(() => [
    ...rows.flatMap((row) => rowIds(row)),
    ...general.map((permission) => permission.id),
]);

const allState = computed<boolean | 'indeterminate'>(() => {
    const checkedCount = allIds.value.filter((id) => isSelected(id)).length;

    if (checkedCount === 0) {
        return false;
    }

    return checkedCount === allIds.value.length ? true : 'indeterminate';
});

function toggleAll(checked: boolean) {
    setSelected(allIds.value, checked);
}
</script>

<template>
    <div v-if="permissions.length > 0" class="flex flex-col">
        <div
            class="flex flex-col gap-3 border-b border-sidebar-border/70 pb-4 sm:flex-row sm:items-center sm:justify-between dark:border-sidebar-border"
        >
            <div class="relative w-full sm:max-w-xs">
                <Search
                    class="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-muted-foreground"
                    aria-hidden="true"
                />
                <Input
                    v-model="query"
                    placeholder="Find a resource…"
                    class="pl-8"
                />
            </div>

            <label
                class="flex cursor-pointer items-center gap-2.5 self-start rounded-lg border border-input px-3 py-2 text-sm font-medium transition-colors has-[[data-state=checked]]:border-primary has-[[data-state=checked]]:bg-primary/5 sm:self-auto"
            >
                <Checkbox
                    :model-value="allState"
                    @update:model-value="(v) => toggleAll(v === true)"
                />
                Select all permissions
                <Badge variant="secondary">
                    {{ selected.length }} / {{ allIds.length }}
                </Badge>
            </label>
        </div>

        <div
            v-if="rows.length > 0"
            class="mt-4 max-h-[26rem] overflow-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
        >
            <table class="w-full border-collapse text-left text-sm">
                <thead>
                    <tr
                        class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border"
                    >
                        <th
                            class="sticky top-0 left-0 z-20 bg-card px-4 py-3 font-medium"
                        >
                            Resource
                        </th>
                        <th
                            v-for="action in ACTIONS"
                            :key="action"
                            class="sticky top-0 z-10 bg-card px-3 py-3 text-center font-medium"
                        >
                            <label
                                class="flex cursor-pointer flex-col items-center gap-1.5"
                            >
                                <Checkbox
                                    :model-value="columnState(action)"
                                    @update:model-value="
                                        (v) => toggleColumn(action, v === true)
                                    "
                                />
                                {{ ACTION_LABELS[action] }}
                            </label>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="row in filteredRows"
                        :key="row.resource"
                        class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border"
                    >
                        <td
                            class="sticky left-0 z-10 bg-card px-4 py-2.5 font-medium"
                        >
                            <label
                                class="flex cursor-pointer items-center gap-2.5"
                            >
                                <Checkbox
                                    :model-value="rowState(row)"
                                    @update:model-value="
                                        (v) => toggleRow(row, v === true)
                                    "
                                />
                                {{ row.label }}
                            </label>
                        </td>
                        <td
                            v-for="action in ACTIONS"
                            :key="action"
                            class="px-3 py-2.5 text-center"
                        >
                            <Checkbox
                                v-if="row.actions[action]"
                                :id="`permission-${row.actions[action]!.id}`"
                                name="permissions[]"
                                :value="row.actions[action]!.id"
                                :model-value="
                                    isSelected(row.actions[action]!.id)
                                "
                                class="mx-auto"
                                @update:model-value="
                                    (v) =>
                                        setSelected(
                                            [row.actions[action]!.id],
                                            v === true,
                                        )
                                "
                            />
                            <span v-else class="text-muted-foreground">—</span>
                        </td>
                    </tr>
                    <tr v-if="filteredRows.length === 0">
                        <td
                            colspan="5"
                            class="px-4 py-6 text-center text-muted-foreground"
                        >
                            No resources match "{{ query }}".
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="general.length > 0" class="mt-6">
            <p
                class="mb-2 text-xs font-medium tracking-wide text-muted-foreground uppercase"
            >
                Other access
            </p>
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                <label
                    v-for="permission in general"
                    :key="permission.id"
                    :for="`permission-${permission.id}`"
                    class="flex cursor-pointer items-center gap-3 rounded-lg border border-input px-3 py-2.5 capitalize transition-colors has-[[data-state=checked]]:border-primary has-[[data-state=checked]]:bg-primary/5"
                >
                    <Checkbox
                        :id="`permission-${permission.id}`"
                        name="permissions[]"
                        :value="permission.id"
                        :model-value="isSelected(permission.id)"
                        @update:model-value="
                            (v) => setSelected([permission.id], v === true)
                        "
                    />
                    <span class="text-sm font-medium">{{
                        permission.name
                    }}</span>
                </label>
            </div>
        </div>
    </div>
    <p
        v-else
        class="rounded-lg border border-dashed p-3 text-sm text-muted-foreground"
    >
        No permissions defined yet. This role can still be created and assigned
        to admins.
    </p>
</template>

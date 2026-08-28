<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Lock, Pencil, Plus, ShieldCheck, Trash2 } from '@lucide/vue';
import RoleController from '@/actions/App/Http/Controllers/Backend/RoleController';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { create, edit, index } from '@/routes/roles';

interface Role {
    id: number;
    name: string;
    permissions_count: number;
    created_at: string;
}

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type Paginated<T> = {
    data: T[];
    links: PaginationLink[];
};

defineProps<{
    roles: Paginated<Role>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Roles',
                href: index(),
            },
        ],
    },
});

const PROTECTED_ROLE = 'admin';

function formatDate(value: string): string {
    return new Date(value).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>

<template>
    <Head title="Roles" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Roles"
                description="Control what admins can do by grouping permissions into roles"
            />
            <Button as-child>
                <Link :href="create()">
                    <Plus />
                    Add role
                </Link>
            </Button>
        </div>

        <div
            class="relative overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
        >
            <table class="w-full text-left text-sm">
                <thead
                    class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border"
                >
                    <tr>
                        <th class="px-4 py-3 font-medium">Role</th>
                        <th class="px-4 py-3 font-medium">Permissions</th>
                        <th class="px-4 py-3 font-medium">Created</th>
                        <th class="px-4 py-3 font-medium">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="role in roles.data"
                        :key="role.id"
                        class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border"
                    >
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2 font-medium">
                                <ShieldCheck class="size-4 text-muted-foreground" />
                                {{ role.name }}
                                <Lock
                                    v-if="role.name === PROTECTED_ROLE"
                                    class="size-3.5 text-muted-foreground"
                                />
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <Badge v-if="role.permissions_count > 0" variant="secondary">
                                {{ role.permissions_count }}
                                {{ role.permissions_count === 1 ? 'permission' : 'permissions' }}
                            </Badge>
                            <span v-else class="text-muted-foreground">No permissions</span>
                        </td>
                        <td class="px-4 py-3 text-muted-foreground">
                            {{ formatDate(role.created_at) }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <Button
                                    variant="outline"
                                    size="icon-sm"
                                    as-child
                                >
                                    <Link :href="edit(role)">
                                        <Pencil />
                                        <span class="sr-only">Edit</span>
                                    </Link>
                                </Button>

                                <Dialog v-if="role.name !== PROTECTED_ROLE">
                                    <DialogTrigger as-child>
                                        <Button
                                            variant="destructive"
                                            size="icon-sm"
                                        >
                                            <Trash2 />
                                            <span class="sr-only">Delete</span>
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent>
                                        <Form
                                            v-bind="
                                                RoleController.destroy.form({
                                                    role: role.id,
                                                })
                                            "
                                            :options="{
                                                preserveScroll: true,
                                            }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete
                                                    "{{ role.name }}"?</DialogTitle
                                                >
                                                <DialogDescription>
                                                    Admins with this role lose its permissions immediately. This cannot be undone.
                                                </DialogDescription>
                                            </DialogHeader>

                                            <DialogFooter class="gap-2">
                                                <DialogClose as-child>
                                                    <Button
                                                        variant="secondary"
                                                    >
                                                        Cancel
                                                    </Button>
                                                </DialogClose>
                                                <Button
                                                    type="submit"
                                                    variant="destructive"
                                                    :disabled="processing"
                                                >
                                                    Delete
                                                </Button>
                                            </DialogFooter>
                                        </Form>
                                    </DialogContent>
                                </Dialog>
                                <Button
                                    v-else
                                    variant="outline"
                                    size="icon-sm"
                                    disabled
                                    title="The admin role is protected and cannot be deleted."
                                >
                                    <Lock />
                                    <span class="sr-only">Protected</span>
                                </Button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="roles.data.length === 0">
                        <td
                            colspan="4"
                            class="px-4 py-6 text-center text-muted-foreground"
                        >
                            No roles yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-if="roles.links.length > 3"
            class="flex flex-wrap items-center justify-center gap-1"
        >
            <template v-for="(link, index) in roles.links" :key="index">
                <span
                    v-if="!link.url"
                    class="rounded-md px-3 py-1.5 text-sm text-muted-foreground"
                    v-html="link.label"
                />
                <Link
                    v-else
                    :href="link.url"
                    preserve-scroll
                    class="rounded-md px-3 py-1.5 text-sm"
                    :class="
                        link.active
                            ? 'bg-primary text-primary-foreground'
                            : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground'
                    "
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>

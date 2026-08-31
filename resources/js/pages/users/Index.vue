<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Pencil, Trash2 } from '@lucide/vue';
import UserController from '@/actions/App/Http/Controllers/Backend/UserController';
import Heading from '@/components/Heading.vue';
import Pagination from '@/components/Pagination.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { create, edit, index } from '@/routes/users';

type Role = {
    id: number;
    name: string;
};

type User = {
    id: number;
    name: string;
    email: string;
    image: string | null;
    status: boolean;
    roles: Role[];
    created_at: string;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type Paginated<T> = {
    data: T[];
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
    current_page: number;
    last_page: number;
};

defineProps<{
    users: Paginated<User>;
}>();

function formatDate(value: string): string {
    return new Date(value).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

function initials(name: string): string {
    return name
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase())
        .join('');
}

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Users',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Users" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Users"
                description="Manage admin accounts and their assigned roles"
            />
            <Button as-child>
                <Link :href="create()">Add user</Link>
            </Button>
        </div>

        <div
            class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
        >
            <table class="w-full text-left text-sm">
                <thead
                    class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border"
                >
                    <tr>
                        <th class="p-3 font-medium">User</th>
                        <th class="p-3 font-medium">Roles</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 font-medium">Created</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="user in users.data"
                        :key="user.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3">
                            <div class="flex items-center gap-3">
                                <Avatar>
                                    <AvatarImage
                                        v-if="user.image"
                                        :src="user.image"
                                        :alt="user.name"
                                    />
                                    <AvatarFallback>{{
                                        initials(user.name)
                                    }}</AvatarFallback>
                                </Avatar>
                                <div class="min-w-0">
                                    <div class="truncate font-medium">
                                        {{ user.name }}
                                    </div>
                                    <div
                                        class="truncate text-xs text-muted-foreground"
                                    >
                                        {{ user.email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="p-3">
                            <div
                                v-if="user.roles.length"
                                class="flex flex-wrap gap-1"
                            >
                                <Badge
                                    v-for="role in user.roles"
                                    :key="role.id"
                                    variant="secondary"
                                >
                                    {{ role.name }}
                                </Badge>
                            </div>
                            <span v-else class="text-muted-foreground">—</span>
                        </td>
                        <td class="p-3">
                            <Badge
                                :variant="user.status ? 'default' : 'destructive'"
                                class="gap-1.5"
                            >
                                <span
                                    class="size-1.5 rounded-full bg-current"
                                    aria-hidden="true"
                                />
                                {{ user.status ? 'Active' : 'Banned' }}
                            </Badge>
                        </td>
                        <td class="p-3 text-muted-foreground">
                            {{ formatDate(user.created_at) }}
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-1">
                                <Button
                                    variant="ghost"
                                    size="icon-sm"
                                    as-child
                                >
                                    <Link :href="edit(user)" aria-label="Edit user">
                                        <Pencil aria-hidden="true" />
                                    </Link>
                                </Button>

                                <Dialog>
                                    <DialogTrigger as-child>
                                        <Button
                                            variant="ghost"
                                            size="icon-sm"
                                            aria-label="Delete user"
                                            class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                                        >
                                            <Trash2 aria-hidden="true" />
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent>
                                        <Form
                                            v-bind="
                                                UserController.destroy.form(
                                                    user,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        user.name
                                                    }}"?</DialogTitle
                                                >
                                            </DialogHeader>

                                            <DialogFooter class="mt-6 gap-2">
                                                <DialogClose as-child>
                                                    <Button variant="secondary"
                                                        >Cancel</Button
                                                    >
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
                            </div>
                        </td>
                    </tr>

                    <tr v-if="users.data.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="5"
                        >
                            No users yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :meta="users" label="users" />
    </div>
</template>

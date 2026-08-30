<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import UserController from '@/actions/App/Http/Controllers/Backend/UserController';
import Heading from '@/components/Heading.vue';
import Pagination from '@/components/Pagination.vue';
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
import { create, edit, index, show } from '@/routes/users';

type Role = {
    id: number;
    name: string;
};

type User = {
    id: number;
    name: string;
    email: string;
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
                        <th class="p-3 font-medium">Name</th>
                        <th class="p-3 font-medium">Email</th>
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
                        <td class="p-3 font-medium">
                            <Link :href="show(user)" class="hover:underline">{{
                                user.name
                            }}</Link>
                        </td>
                        <td class="p-3 text-muted-foreground">
                            {{ user.email }}
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
                            <Badge :variant="user.status ? 'default' : 'destructive'">
                                {{ user.status ? 'Active' : 'Banned' }}
                            </Badge>
                        </td>
                        <td class="p-3 text-muted-foreground">
                            {{ formatDate(user.created_at) }}
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="show(user)">View</Link>
                                </Button>
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(user)">Edit</Link>
                                </Button>

                                <Dialog>
                                    <DialogTrigger as-child>
                                        <Button variant="destructive" size="sm"
                                            >Delete</Button
                                        >
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
                            colspan="6"
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

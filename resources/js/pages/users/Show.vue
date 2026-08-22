<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import UserController from '@/actions/App/Http/Controllers/Backend/UserController';
import Heading from '@/components/Heading.vue';
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
import { edit, index, show } from '@/routes/users';

type Role = {
    id: number;
    name: string;
};

type User = {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    roles: Role[];
    created_at: string;
    updated_at: string;
};

const props = defineProps<{
    user: User;
}>();

function formatDate(value: string | null): string {
    if (!value) {
        return '—';
    }

    return new Date(value).toLocaleString();
}

defineOptions({
    layout: (pageProps: { user: User }) => ({
        breadcrumbs: [
            {
                title: 'Users',
                href: index(),
            },
            {
                title: pageProps.user.name,
                href: show(pageProps.user),
            },
        ],
    }),
});
</script>

<template>
    <Head :title="user.name" />

    <div class="flex flex-col space-y-6">
        <div class="flex items-center justify-between">
            <Heading :title="user.name" description="Admin account details" />
            <div class="flex gap-2">
                <Button variant="outline" as-child>
                    <Link :href="edit(props.user)">Edit</Link>
                </Button>

                <Dialog>
                    <DialogTrigger as-child>
                        <Button variant="destructive">Delete</Button>
                    </DialogTrigger>
                    <DialogContent>
                        <Form
                            v-bind="UserController.destroy.form(props.user)"
                            :options="{ preserveScroll: true }"
                            v-slot="{ processing }"
                        >
                            <DialogHeader class="space-y-3">
                                <DialogTitle
                                    >Delete "{{ user.name }}"?</DialogTitle
                                >
                            </DialogHeader>

                            <DialogFooter class="mt-6 gap-2">
                                <DialogClose as-child>
                                    <Button variant="secondary">Cancel</Button>
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
        </div>

        <div
            class="max-w-xl divide-y divide-sidebar-border/70 rounded-xl border border-sidebar-border/70 dark:divide-sidebar-border dark:border-sidebar-border"
        >
            <div class="grid grid-cols-3 gap-4 p-4">
                <dt class="text-sm font-medium text-muted-foreground">Name</dt>
                <dd class="col-span-2 text-sm">{{ user.name }}</dd>
            </div>

            <div class="grid grid-cols-3 gap-4 p-4">
                <dt class="text-sm font-medium text-muted-foreground">Email</dt>
                <dd class="col-span-2 text-sm">{{ user.email }}</dd>
            </div>

            <div class="grid grid-cols-3 gap-4 p-4">
                <dt class="text-sm font-medium text-muted-foreground">
                    Email verified
                </dt>
                <dd class="col-span-2 text-sm">
                    <Badge
                        :variant="
                            user.email_verified_at ? 'default' : 'secondary'
                        "
                    >
                        {{
                            user.email_verified_at
                                ? `Verified ${formatDate(user.email_verified_at)}`
                                : 'Not verified'
                        }}
                    </Badge>
                </dd>
            </div>

            <div class="grid grid-cols-3 gap-4 p-4">
                <dt class="text-sm font-medium text-muted-foreground">Roles</dt>
                <dd class="col-span-2">
                    <div v-if="user.roles.length" class="flex flex-wrap gap-1">
                        <Badge
                            v-for="role in user.roles"
                            :key="role.id"
                            variant="secondary"
                        >
                            {{ role.name }}
                        </Badge>
                    </div>
                    <span v-else class="text-sm text-muted-foreground">—</span>
                </dd>
            </div>

            <div class="grid grid-cols-3 gap-4 p-4">
                <dt class="text-sm font-medium text-muted-foreground">
                    Created
                </dt>
                <dd class="col-span-2 text-sm">
                    {{ formatDate(user.created_at) }}
                </dd>
            </div>

            <div class="grid grid-cols-3 gap-4 p-4">
                <dt class="text-sm font-medium text-muted-foreground">
                    Last updated
                </dt>
                <dd class="col-span-2 text-sm">
                    {{ formatDate(user.updated_at) }}
                </dd>
            </div>
        </div>
    </div>
</template>

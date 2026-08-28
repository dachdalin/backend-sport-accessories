<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Clock, KeyRound, ShieldCheck, UserRound } from '@lucide/vue';
import UserController from '@/actions/App/Http/Controllers/Backend/UserController';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
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

        <div class="flex max-w-xl flex-col gap-6">
            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2.5">
                        <UserRound
                            class="size-4.5 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <CardTitle>Identity</CardTitle>
                    </div>
                    <CardDescription>
                        Name and email the user signs in with.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <dl class="grid grid-cols-3 gap-y-4">
                        <dt class="text-sm font-medium text-muted-foreground">
                            Name
                        </dt>
                        <dd class="col-span-2 text-sm">{{ user.name }}</dd>

                        <dt class="text-sm font-medium text-muted-foreground">
                            Email
                        </dt>
                        <dd class="col-span-2 text-sm">{{ user.email }}</dd>
                    </dl>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2.5">
                        <KeyRound
                            class="size-4.5 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <CardTitle>Credentials</CardTitle>
                    </div>
                    <CardDescription>
                        Sign-in verification status.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <dl class="grid grid-cols-3 gap-y-4">
                        <dt class="text-sm font-medium text-muted-foreground">
                            Email verified
                        </dt>
                        <dd class="col-span-2 text-sm">
                            <Badge
                                :variant="
                                    user.email_verified_at
                                        ? 'default'
                                        : 'secondary'
                                "
                            >
                                {{
                                    user.email_verified_at
                                        ? `Verified ${formatDate(user.email_verified_at)}`
                                        : 'Not verified'
                                }}
                            </Badge>
                        </dd>
                    </dl>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2.5">
                        <ShieldCheck
                            class="size-4.5 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <CardTitle>Access</CardTitle>
                    </div>
                    <CardDescription>
                        Roles assigned to this user.
                    </CardDescription>
                </CardHeader>
                <CardContent>
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
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2.5">
                        <Clock
                            class="size-4.5 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <CardTitle>Activity</CardTitle>
                    </div>
                    <CardDescription>
                        When this account was created and last changed.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <dl class="grid grid-cols-3 gap-y-4">
                        <dt class="text-sm font-medium text-muted-foreground">
                            Created
                        </dt>
                        <dd class="col-span-2 text-sm">
                            {{ formatDate(user.created_at) }}
                        </dd>

                        <dt class="text-sm font-medium text-muted-foreground">
                            Last updated
                        </dt>
                        <dd class="col-span-2 text-sm">
                            {{ formatDate(user.updated_at) }}
                        </dd>
                    </dl>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

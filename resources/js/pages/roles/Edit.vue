<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ShieldCheck, Tag } from '@lucide/vue';
import RoleController from '@/actions/App/Http/Controllers/Backend/RoleController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { edit, index } from '@/routes/roles';

type Permission = {
    id: number;
    name: string;
};

type Role = {
    id: number;
    name: string;
    permissions: Permission[];
};

const props = defineProps<{
    role: Role;
    permissions: Permission[];
}>();

function hasPermission(permissionId: number): boolean {
    return props.role.permissions.some(
        (permission) => permission.id === permissionId,
    );
}

defineOptions({
    layout: (pageProps: { role: Role }) => ({
        breadcrumbs: [
            {
                title: 'Roles',
                href: index(),
            },
            {
                title: 'Edit role',
                href: edit(pageProps.role),
            },
        ],
    }),
});
</script>

<template>
    <Head :title="`Edit ${role.name}`" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit role"
            description="Update the role's name and permissions"
        />

        <Form
            v-bind="RoleController.update.form(role)"
            class="flex flex-col gap-6"
            v-slot="{ errors, processing }"
        >
            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2.5">
                        <Tag
                            class="size-4.5 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <CardTitle>Name</CardTitle>
                    </div>
                    <CardDescription>
                        What admins will see when assigning this role.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid max-w-sm gap-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            name="name"
                            required
                            autofocus
                            :default-value="role.name"
                        />
                        <InputError :message="errors.name" />
                    </div>
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
                        Choose what this role can access.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="permissions.length > 0"
                        class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <label
                            v-for="permission in permissions"
                            :key="permission.id"
                            :for="`permission-${permission.id}`"
                            class="flex cursor-pointer items-center gap-3 rounded-lg border border-input px-3 py-2.5 transition-colors has-[[data-state=checked]]:border-primary has-[[data-state=checked]]:bg-primary/5"
                        >
                            <Checkbox
                                :id="`permission-${permission.id}`"
                                name="permissions[]"
                                :value="permission.id"
                                :default-value="hasPermission(permission.id)"
                            />
                            <span class="text-sm font-medium">{{
                                permission.name
                            }}</span>
                        </label>
                    </div>
                    <p
                        v-else
                        class="rounded-lg border border-dashed p-3 text-sm text-muted-foreground"
                    >
                        No permissions defined yet. This role can still be
                        created and assigned to admins.
                    </p>
                    <InputError :message="errors.permissions" class="mt-2" />
                </CardContent>
                <CardFooter class="gap-3 border-t pt-6">
                    <Button :disabled="processing">
                        <Spinner v-if="processing" />
                        Save changes
                    </Button>
                    <Button variant="outline" as-child>
                        <Link :href="index()">Cancel</Link>
                    </Button>
                </CardFooter>
            </Card>
        </Form>
    </div>
</template>

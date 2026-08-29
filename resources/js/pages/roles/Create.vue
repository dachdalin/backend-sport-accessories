<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ShieldCheck, Tag } from '@lucide/vue';
import { ref } from 'vue';
import RoleController from '@/actions/App/Http/Controllers/Backend/RoleController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import PermissionMatrix from '@/components/PermissionMatrix.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { create, index } from '@/routes/roles';

type Permission = {
    id: number;
    name: string;
};

defineProps<{
    permissions: Permission[];
}>();

const selectedPermissionIds = ref<number[]>([]);

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Roles',
                href: index(),
            },
            {
                title: 'Add role',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add role" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add role"
            description="Name the role, then choose what it can do"
        />

        <Form
            v-bind="RoleController.store.form()"
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
                            placeholder="Editor"
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
                    <PermissionMatrix
                        v-model:selected="selectedPermissionIds"
                        :permissions="permissions"
                    />
                    <InputError :message="errors.permissions" class="mt-2" />
                </CardContent>
                <CardFooter class="gap-3 border-t pt-6">
                    <Button :disabled="processing">
                        <Spinner v-if="processing" />
                        Create role
                    </Button>
                    <Button variant="outline" as-child>
                        <Link :href="index()">Cancel</Link>
                    </Button>
                </CardFooter>
            </Card>
        </Form>
    </div>
</template>

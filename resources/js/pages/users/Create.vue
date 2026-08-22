<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import UserController from '@/actions/App/Http/Controllers/Backend/UserController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { create, index } from '@/routes/users';

type Role = {
    id: number;
    name: string;
};

defineProps<{
    roles: Role[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Users',
                href: index(),
            },
            {
                title: 'Add user',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add user" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add user"
            description="Create a new admin account and assign its roles"
        />

        <Form
            v-bind="UserController.store.form()"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    name="name"
                    required
                    autofocus
                    placeholder="Jane Smith"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Email</Label>
                <Input
                    id="email"
                    name="email"
                    type="email"
                    required
                    placeholder="jane@example.com"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        name="password"
                        type="password"
                        required
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password</Label>
                    <Input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        required
                    />
                </div>
            </div>

            <div class="grid gap-2">
                <Label>Roles</Label>
                <div class="flex flex-col gap-2">
                    <div
                        v-for="role in roles"
                        :key="role.id"
                        class="flex items-center gap-2"
                    >
                        <Checkbox
                            :id="`role-${role.id}`"
                            name="roles[]"
                            :value="role.id"
                        />
                        <Label
                            :for="`role-${role.id}`"
                            class="font-normal capitalize"
                            >{{ role.name }}</Label
                        >
                    </div>
                    <p
                        v-if="roles.length === 0"
                        class="text-sm text-muted-foreground"
                    >
                        No roles defined yet.
                    </p>
                </div>
                <InputError :message="errors.roles" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create user</Button>
            </div>
        </Form>
    </div>
</template>

<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { CheckCircle2 } from '@lucide/vue';
import { computed, ref } from 'vue';
import UserController from '@/actions/App/Http/Controllers/Backend/UserController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Spinner } from '@/components/ui/spinner';
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

const password = ref('');
const passwordConfirmation = ref('');
const passwordsMatch = computed(
    () =>
        password.value.length > 0 &&
        passwordConfirmation.value.length > 0 &&
        password.value === passwordConfirmation.value,
);
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
            class="space-y-6"
            v-slot="{ errors, processing }"
        >
            <Heading
                variant="small"
                title="Account"
                description="Name and email the user will sign in with."
            />

            <div class="grid grid-cols-2 gap-4">
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
            </div>

            <Separator />

            <Heading
                variant="small"
                title="Password"
                description="Set the password the user will sign in with."
            />

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <PasswordInput
                        id="password"
                        name="password"
                        v-model="password"
                        autocomplete="new-password"
                        required
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">
                        Confirm password
                    </Label>
                    <PasswordInput
                        id="password_confirmation"
                        name="password_confirmation"
                        v-model="passwordConfirmation"
                        autocomplete="new-password"
                        required
                    />
                </div>
            </div>
            <p
                v-if="passwordsMatch"
                class="flex items-center gap-1.5 text-sm text-emerald-600 dark:text-emerald-500"
            >
                <CheckCircle2 class="size-4" />
                Passwords match
            </p>

            <Separator />

            <Heading
                variant="small"
                title="Roles"
                description="Choose what this user can access."
            />

            <div class="grid gap-2">
                <div class="grid grid-cols-2 gap-2">
                    <label
                        v-for="role in roles"
                        :key="role.id"
                        :for="`role-${role.id}`"
                        class="flex cursor-pointer items-center gap-3 rounded-lg border border-input px-3 py-2.5 transition-colors has-[[data-state=checked]]:border-primary has-[[data-state=checked]]:bg-primary/5"
                    >
                        <Checkbox
                            :id="`role-${role.id}`"
                            name="roles[]"
                            :value="role.id"
                        />
                        <span class="text-sm font-medium capitalize">{{
                            role.name
                        }}</span>
                    </label>
                </div>
                <p
                    v-if="roles.length === 0"
                    class="text-sm text-muted-foreground"
                >
                    No roles defined yet.
                </p>
                <InputError :message="errors.roles" />
            </div>

            <Separator />

            <div class="flex items-center gap-3">
                <Button :disabled="processing">
                    <Spinner v-if="processing" />
                    Create user
                </Button>
                <Button variant="outline" as-child>
                    <Link :href="index()">Cancel</Link>
                </Button>
            </div>
        </Form>
    </div>
</template>

<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import TeamMemberController from '@/actions/App/Http/Controllers/Backend/TeamMemberController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { create, index } from '@/routes/team-members';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Team members',
                href: index(),
            },
            {
                title: 'Add team member',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add team member" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add team member"
            description="Add a new staff profile to the about page"
        />

        <Form
            v-bind="TeamMemberController.store.form()"
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
                    placeholder="Jane Doe"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="role">Role</Label>
                <Input
                    id="role"
                    name="role"
                    required
                    placeholder="Head Coach"
                />
                <InputError :message="errors.role" />
            </div>

            <div class="grid gap-2">
                <Label for="bio">Bio</Label>
                <Textarea
                    id="bio"
                    name="bio"
                    rows="4"
                    placeholder="Short biography"
                />
                <InputError :message="errors.bio" />
            </div>

            <div class="grid gap-2">
                <Label for="photo">Photo</Label>
                <Input id="photo" name="photo" type="file" accept="image/*" />
                <InputError :message="errors.photo" />
            </div>

            <div class="grid gap-2">
                <Label for="photo_alt_text">Photo alt text</Label>
                <Input
                    id="photo_alt_text"
                    name="photo_alt_text"
                    placeholder="Describes the photo"
                />
                <InputError :message="errors.photo_alt_text" />
            </div>

            <div class="grid gap-2">
                <Label for="sort_order">Sort order</Label>
                <Input
                    id="sort_order"
                    name="sort_order"
                    type="number"
                    min="0"
                    placeholder="0"
                />
                <InputError :message="errors.sort_order" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" :default-value="true" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create team member</Button>
            </div>
        </Form>
    </div>
</template>

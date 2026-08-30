<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import TeamMemberController from '@/actions/App/Http/Controllers/Backend/TeamMemberController';
import Heading from '@/components/Heading.vue';
import ImageDropzone from '@/components/ImageDropzone.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
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
            class="max-w-2xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
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

            <ImageDropzone
                name="photo"
                label="Photo"
                hint="PNG, JPG or WEBP, up to 2MB."
                :max-size-mb="2"
                :error="errors.photo"
                :processing="processing"
            />

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
                    default-value="0"
                />
                <InputError :message="errors.sort_order" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    value="1"
                    :default-value="true"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <Button class="w-full sm:w-auto" :disabled="processing">
                    <Spinner v-if="processing" />
                    Create team member
                </Button>
                <Button variant="outline" as-child class="w-full sm:w-auto">
                    <Link :href="index()">Cancel</Link>
                </Button>
            </div>
        </Form>
    </div>
</template>

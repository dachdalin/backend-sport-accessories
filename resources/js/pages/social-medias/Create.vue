<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import SocialMediaController from '@/actions/App/Http/Controllers/Backend/SocialMediaController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { create, index } from '@/routes/social-medias';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Social media',
                href: index(),
            },
            {
                title: 'Add social link',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add social link" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add social link"
            description="Add a social media link to your storefront"
        />

        <Form
            v-bind="SocialMediaController.store.form()"
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
                    placeholder="Facebook"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="link">Link</Label>
                <Input
                    id="link"
                    name="link"
                    type="url"
                    required
                    placeholder="https://facebook.com/yourstore"
                />
                <InputError :message="errors.link" />
            </div>

            <div class="grid gap-2">
                <Label for="icon">Icon</Label>
                <Input id="icon" name="icon" placeholder="facebook" />
                <InputError :message="errors.icon" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" :default-value="true" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create social link</Button>
            </div>
        </Form>
    </div>
</template>

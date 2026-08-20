<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import BannerController from '@/actions/App/Http/Controllers/Backend/BannerController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { create, index } from '@/routes/banners';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Banners',
                href: index(),
            },
            {
                title: 'Add banner',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add banner" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add banner"
            description="Create a new promotional banner"
        />

        <Form
            v-bind="BannerController.store.form()"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="title">Title</Label>
                <Input
                    id="title"
                    name="title"
                    required
                    autofocus
                    placeholder="Banner title"
                />
                <InputError :message="errors.title" />
            </div>

            <div class="grid gap-2">
                <Label for="image">Image</Label>
                <Input id="image" name="image" type="file" accept="image/*" />
                <InputError :message="errors.image" />
            </div>

            <div class="grid gap-2">
                <Label for="image_alt_text">Image alt text</Label>
                <Input
                    id="image_alt_text"
                    name="image_alt_text"
                    placeholder="Describes the image"
                />
                <InputError :message="errors.image_alt_text" />
            </div>

            <div class="grid gap-2">
                <Label for="link_url">Link URL</Label>
                <Input
                    id="link_url"
                    name="link_url"
                    type="url"
                    placeholder="https://example.com"
                />
                <InputError :message="errors.link_url" />
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
                <Checkbox id="status" name="status" :default-value="true" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create banner</Button>
            </div>
        </Form>
    </div>
</template>

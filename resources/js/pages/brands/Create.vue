<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import BrandController from '@/actions/App/Http/Controllers/Backend/BrandController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { create, index } from '@/routes/brands';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Brands',
                href: index(),
            },
            {
                title: 'Add brand',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add brand" />

    <div class="flex flex-col space-y-6">
        <Heading title="Add brand" description="Create a new product brand" />

        <Form
            v-bind="BrandController.store.form()"
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
                    placeholder="Brand name"
                />
                <InputError :message="errors.name" />
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

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" :default-value="true" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create brand</Button>
            </div>
        </Form>
    </div>
</template>

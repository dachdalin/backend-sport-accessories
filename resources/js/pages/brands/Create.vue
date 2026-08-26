<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { onBeforeUnmount, ref } from 'vue';
import BrandController from '@/actions/App/Http/Controllers/Backend/BrandController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
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

const imagePreview = ref<string | null>(null);

function onImageChange(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0];

    if (imagePreview.value) {
        URL.revokeObjectURL(imagePreview.value);
    }

    imagePreview.value = file ? URL.createObjectURL(file) : null;
}

onBeforeUnmount(() => {
    if (imagePreview.value) {
        URL.revokeObjectURL(imagePreview.value);
    }
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
                <div class="flex items-center gap-3">
                    <img
                        v-if="imagePreview"
                        :src="imagePreview"
                        alt="Image preview"
                        class="size-16 shrink-0 rounded-md border border-input object-cover"
                    />
                    <div
                        v-else
                        class="flex size-16 shrink-0 items-center justify-center rounded-md border border-dashed border-input text-xs text-muted-foreground"
                    >
                        No image
                    </div>
                    <Input
                        id="image"
                        name="image"
                        type="file"
                        accept="image/*"
                        @change="onImageChange"
                    />
                </div>
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

            <div class="flex items-center gap-3">
                <Button :disabled="processing">
                    <Spinner v-if="processing" />
                    Create brand
                </Button>
                <Button variant="outline" as-child>
                    <Link :href="index()">Cancel</Link>
                </Button>
            </div>
        </Form>
    </div>
</template>

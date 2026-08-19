<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import BrandController from '@/actions/App/Http/Controllers/Backend/BrandController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit, index } from '@/routes/brands';

type Brand = {
    id: number;
    name: string;
    image: string;
    image_alt_text: string | null;
    status: boolean;
};

defineProps<{
    brand: Brand;
}>();

defineOptions({
    layout: (pageProps: { brand: Brand }) => ({
        breadcrumbs: [
            {
                title: 'Brands',
                href: index(),
            },
            {
                title: 'Edit brand',
                href: edit(pageProps.brand),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit brand" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit brand"
            :description="`Update the details for ${brand.name}`"
        />

        <Form
            v-bind="BrandController.update.form(brand)"
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
                    :default-value="brand.name"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <img
                    :src="`/storage/${brand.image}`"
                    :alt="brand.image_alt_text ?? brand.name"
                    class="size-16 rounded object-cover"
                />
                <Label for="image">Replace image</Label>
                <Input id="image" name="image" type="file" accept="image/*" />
                <InputError :message="errors.image" />
            </div>

            <div class="grid gap-2">
                <Label for="image_alt_text">Image alt text</Label>
                <Input
                    id="image_alt_text"
                    name="image_alt_text"
                    :default-value="brand.image_alt_text ?? ''"
                    placeholder="Describes the image"
                />
                <InputError :message="errors.image_alt_text" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="brand.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save brand</Button>
            </div>
        </Form>
    </div>
</template>

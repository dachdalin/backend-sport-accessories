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
import { edit, index } from '@/routes/brands';

type Brand = {
    id: number;
    name: string;
    image: string;
    image_alt_text: string | null;
    status: boolean;
};

const props = defineProps<{
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

const imagePreview = ref<string>(`/storage/${props.brand.image}`);
const uploadedImagePreview = ref<string | null>(null);

function onImageChange(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0];

    if (uploadedImagePreview.value) {
        URL.revokeObjectURL(uploadedImagePreview.value);
    }

    uploadedImagePreview.value = file ? URL.createObjectURL(file) : null;
}

onBeforeUnmount(() => {
    if (uploadedImagePreview.value) {
        URL.revokeObjectURL(uploadedImagePreview.value);
    }
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
                <Label for="image">Image</Label>
                <div class="flex items-center gap-3">
                    <img
                        :src="uploadedImagePreview ?? imagePreview"
                        :alt="brand.image_alt_text ?? brand.name"
                        class="size-16 shrink-0 rounded-md border border-input object-cover"
                    />
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

            <div class="flex items-center gap-3">
                <Button :disabled="processing">
                    <Spinner v-if="processing" />
                    Save brand
                </Button>
                <Button variant="outline" as-child>
                    <Link :href="index()">Cancel</Link>
                </Button>
            </div>
        </Form>
    </div>
</template>

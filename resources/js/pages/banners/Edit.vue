<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import BannerController from '@/actions/App/Http/Controllers/Backend/BannerController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit, index } from '@/routes/banners';

type Banner = {
    id: number;
    title: string;
    image: string;
    image_alt_text: string | null;
    link_url: string | null;
    sort_order: number;
    status: boolean;
};

const props = defineProps<{
    banner: Banner;
}>();

defineOptions({
    layout: (pageProps: { banner: Banner }) => ({
        breadcrumbs: [
            {
                title: 'Banners',
                href: index(),
            },
            {
                title: 'Edit banner',
                href: edit(pageProps.banner),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit banner" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit banner"
            :description="`Update the details for ${props.banner.title}`"
        />

        <Form
            v-bind="BannerController.update.form(props.banner)"
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
                    :default-value="props.banner.title"
                />
                <InputError :message="errors.title" />
            </div>

            <div class="grid gap-2">
                <img
                    :src="`/storage/${props.banner.image}`"
                    :alt="props.banner.image_alt_text ?? props.banner.title"
                    class="h-16 w-28 rounded object-cover"
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
                    :default-value="props.banner.image_alt_text ?? ''"
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
                    :default-value="props.banner.link_url ?? ''"
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
                    :default-value="props.banner.sort_order"
                />
                <InputError :message="errors.sort_order" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="props.banner.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save banner</Button>
            </div>
        </Form>
    </div>
</template>

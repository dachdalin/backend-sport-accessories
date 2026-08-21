<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import FeatureDealController from '@/actions/App/Http/Controllers/Backend/FeatureDealController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit, index } from '@/routes/feature-deals';

type FeatureDeal = {
    id: number;
    photo: string;
    url: string | null;
    status: boolean;
};

defineProps<{
    featureDeal: FeatureDeal;
}>();

defineOptions({
    layout: (pageProps: { featureDeal: FeatureDeal }) => ({
        breadcrumbs: [
            {
                title: 'Feature deals',
                href: index(),
            },
            {
                title: 'Edit feature deal',
                href: edit(pageProps.featureDeal),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit feature deal" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit feature deal"
            description="Update the featured deal tile"
        />

        <Form
            v-bind="FeatureDealController.update.form(featureDeal)"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <img
                    :src="`/storage/${featureDeal.photo}`"
                    alt="Feature deal photo"
                    class="h-10 w-16 rounded object-cover"
                />
                <Label for="photo">Replace photo</Label>
                <Input id="photo" name="photo" type="file" accept="image/*" />
                <InputError :message="errors.photo" />
            </div>

            <div class="grid gap-2">
                <Label for="url">URL</Label>
                <Input
                    id="url"
                    name="url"
                    type="url"
                    :default-value="featureDeal.url ?? ''"
                    placeholder="https://example.com"
                />
                <InputError :message="errors.url" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="featureDeal.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save feature deal</Button>
            </div>
        </Form>
    </div>
</template>

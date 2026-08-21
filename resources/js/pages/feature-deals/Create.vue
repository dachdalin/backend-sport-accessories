<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import FeatureDealController from '@/actions/App/Http/Controllers/Backend/FeatureDealController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { create, index } from '@/routes/feature-deals';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Feature deals',
                href: index(),
            },
            {
                title: 'Add feature deal',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add feature deal" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add feature deal"
            description="Create a new featured deal tile"
        />

        <Form
            v-bind="FeatureDealController.store.form()"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="photo">Photo</Label>
                <Input id="photo" name="photo" type="file" accept="image/*" />
                <InputError :message="errors.photo" />
            </div>

            <div class="grid gap-2">
                <Label for="url">URL</Label>
                <Input
                    id="url"
                    name="url"
                    type="url"
                    placeholder="https://example.com"
                />
                <InputError :message="errors.url" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" :default-value="true" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create feature deal</Button>
            </div>
        </Form>
    </div>
</template>

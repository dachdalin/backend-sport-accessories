<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import ReturnPolicyController from '@/actions/App/Http/Controllers/Backend/ReturnPolicyController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { edit, index } from '@/routes/return-policies';

type ReturnPolicy = {
    id: number;
    title: string;
    description: string;
    days_allowed: number;
    status: boolean;
};

const props = defineProps<{
    returnPolicy: ReturnPolicy;
}>();

defineOptions({
    layout: (pageProps: { returnPolicy: ReturnPolicy }) => ({
        breadcrumbs: [
            {
                title: 'Return policies',
                href: index(),
            },
            {
                title: 'Edit return policy',
                href: edit(pageProps.returnPolicy),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit return policy" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit return policy"
            :description="`Update the details for ${props.returnPolicy.title}`"
        />

        <Form
            v-bind="ReturnPolicyController.update.form(props.returnPolicy)"
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
                    :default-value="props.returnPolicy.title"
                />
                <InputError :message="errors.title" />
            </div>

            <div class="grid gap-2">
                <Label for="description">Description</Label>
                <Textarea
                    id="description"
                    name="description"
                    required
                    :default-value="props.returnPolicy.description"
                />
                <InputError :message="errors.description" />
            </div>

            <div class="grid gap-2">
                <Label for="days_allowed">Days allowed</Label>
                <Input
                    id="days_allowed"
                    name="days_allowed"
                    type="number"
                    min="0"
                    max="365"
                    :default-value="props.returnPolicy.days_allowed"
                />
                <InputError :message="errors.days_allowed" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="props.returnPolicy.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save return policy</Button>
            </div>
        </Form>
    </div>
</template>

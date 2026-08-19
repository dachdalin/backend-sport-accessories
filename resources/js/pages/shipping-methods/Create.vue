<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import ShippingMethodController from '@/actions/App/Http/Controllers/Backend/ShippingMethodController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { create, index } from '@/routes/shipping-methods';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Shipping methods',
                href: index(),
            },
            {
                title: 'Add shipping method',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add shipping method" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add shipping method"
            description="Create a new shipping option for checkout"
        />

        <Form
            v-bind="ShippingMethodController.store.form()"
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
                    placeholder="Standard shipping"
                />
                <InputError :message="errors.title" />
            </div>

            <div class="grid gap-2">
                <Label for="cost">Cost</Label>
                <Input
                    id="cost"
                    name="cost"
                    type="number"
                    step="0.01"
                    min="0"
                    required
                    default-value="0"
                />
                <InputError :message="errors.cost" />
            </div>

            <div class="grid gap-2">
                <Label for="duration">Duration</Label>
                <Input
                    id="duration"
                    name="duration"
                    placeholder="e.g. 3-5 days"
                />
                <InputError :message="errors.duration" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" :default-value="true" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create shipping method</Button>
            </div>
        </Form>
    </div>
</template>

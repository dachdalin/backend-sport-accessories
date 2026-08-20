<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import LoyaltyTierController from '@/actions/App/Http/Controllers/Backend/LoyaltyTierController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { create, index } from '@/routes/loyalty-tiers';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Loyalty tiers',
                href: index(),
            },
            {
                title: 'Add loyalty tier',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add loyalty tier" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add loyalty tier"
            description="Create a new customer loyalty tier"
        />

        <Form
            v-bind="LoyaltyTierController.store.form()"
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
                    placeholder="Gold"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="points_required">Points required</Label>
                <Input
                    id="points_required"
                    name="points_required"
                    type="number"
                    min="0"
                    default-value="0"
                />
                <InputError :message="errors.points_required" />
            </div>

            <div class="grid gap-2">
                <Label for="discount_percentage">Discount percentage</Label>
                <Input
                    id="discount_percentage"
                    name="discount_percentage"
                    type="number"
                    min="0"
                    max="100"
                    default-value="0"
                />
                <InputError :message="errors.discount_percentage" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" :default-value="true" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create loyalty tier</Button>
            </div>
        </Form>
    </div>
</template>

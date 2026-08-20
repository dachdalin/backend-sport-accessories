<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import LoyaltyTierController from '@/actions/App/Http/Controllers/Backend/LoyaltyTierController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit, index } from '@/routes/loyalty-tiers';

type LoyaltyTier = {
    id: number;
    name: string;
    points_required: number;
    discount_percentage: number;
    status: boolean;
};

const props = defineProps<{
    loyaltyTier: LoyaltyTier;
}>();

defineOptions({
    layout: (pageProps: { loyaltyTier: LoyaltyTier }) => ({
        breadcrumbs: [
            {
                title: 'Loyalty tiers',
                href: index(),
            },
            {
                title: 'Edit loyalty tier',
                href: edit(pageProps.loyaltyTier),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit loyalty tier" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit loyalty tier"
            :description="`Update the details for ${props.loyaltyTier.name}`"
        />

        <Form
            v-bind="LoyaltyTierController.update.form(props.loyaltyTier)"
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
                    :default-value="props.loyaltyTier.name"
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
                    :default-value="props.loyaltyTier.points_required"
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
                    :default-value="props.loyaltyTier.discount_percentage"
                />
                <InputError :message="errors.discount_percentage" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="props.loyaltyTier.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save loyalty tier</Button>
            </div>
        </Form>
    </div>
</template>

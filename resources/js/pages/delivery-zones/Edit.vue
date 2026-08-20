<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import DeliveryZoneController from '@/actions/App/Http/Controllers/Backend/DeliveryZoneController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit, index } from '@/routes/delivery-zones';

type DeliveryZone = {
    id: number;
    zip_code: string;
    city: string | null;
    delivery_charge: string;
    status: boolean;
};

const props = defineProps<{
    deliveryZone: DeliveryZone;
}>();

defineOptions({
    layout: (pageProps: { deliveryZone: DeliveryZone }) => ({
        breadcrumbs: [
            {
                title: 'Delivery zones',
                href: index(),
            },
            {
                title: 'Edit delivery zone',
                href: edit(pageProps.deliveryZone),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit delivery zone" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit delivery zone"
            :description="`Update the details for ${props.deliveryZone.zip_code}`"
        />

        <Form
            v-bind="DeliveryZoneController.update.form(props.deliveryZone)"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="zip_code">Zip code</Label>
                <Input
                    id="zip_code"
                    name="zip_code"
                    required
                    autofocus
                    :default-value="props.deliveryZone.zip_code"
                />
                <InputError :message="errors.zip_code" />
            </div>

            <div class="grid gap-2">
                <Label for="city">City</Label>
                <Input
                    id="city"
                    name="city"
                    :default-value="props.deliveryZone.city ?? ''"
                    placeholder="Beverly Hills"
                />
                <InputError :message="errors.city" />
            </div>

            <div class="grid gap-2">
                <Label for="delivery_charge">Delivery charge</Label>
                <Input
                    id="delivery_charge"
                    name="delivery_charge"
                    type="number"
                    step="0.01"
                    min="0"
                    required
                    :default-value="props.deliveryZone.delivery_charge"
                />
                <InputError :message="errors.delivery_charge" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="props.deliveryZone.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save delivery zone</Button>
            </div>
        </Form>
    </div>
</template>

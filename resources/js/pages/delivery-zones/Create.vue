<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import DeliveryZoneController from '@/actions/App/Http/Controllers/Backend/DeliveryZoneController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { create, index } from '@/routes/delivery-zones';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Delivery zones',
                href: index(),
            },
            {
                title: 'Add delivery zone',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add delivery zone" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add delivery zone"
            description="Add a zip code you deliver to and its charge"
        />

        <Form
            v-bind="DeliveryZoneController.store.form()"
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
                    placeholder="90210"
                />
                <InputError :message="errors.zip_code" />
            </div>

            <div class="grid gap-2">
                <Label for="city">City</Label>
                <Input id="city" name="city" placeholder="Beverly Hills" />
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
                    placeholder="5.00"
                />
                <InputError :message="errors.delivery_charge" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" :default-value="true" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create delivery zone</Button>
            </div>
        </Form>
    </div>
</template>

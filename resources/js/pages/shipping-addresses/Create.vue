<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import ShippingAddressController from '@/actions/App/Http/Controllers/Backend/ShippingAddressController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { create, index } from '@/routes/shipping-addresses';

interface SelectOption {
    value: number;
    label: string;
}

defineProps<{
    customers: SelectOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Shipping addresses',
                href: index(),
            },
            {
                title: 'Add shipping address',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add shipping address" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add shipping address"
            description="Save a shipping address for a customer"
        />

        <Form
            v-bind="ShippingAddressController.store.form()"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="customer_id">Customer</Label>
                <Select name="customer_id">
                    <SelectTrigger id="customer_id" class="w-full">
                        <SelectValue placeholder="Select customer" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="option in customers"
                            :key="option.value"
                            :value="String(option.value)"
                        >
                            {{ option.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors.customer_id" />
            </div>

            <div class="grid gap-2">
                <Label for="contact_person_name">Contact person</Label>
                <Input
                    id="contact_person_name"
                    name="contact_person_name"
                    required
                    autofocus
                    placeholder="Jane Doe"
                />
                <InputError :message="errors.contact_person_name" />
            </div>

            <div class="grid gap-2">
                <Label for="phone">Phone</Label>
                <Input id="phone" name="phone" placeholder="+1 555 0100" />
                <InputError :message="errors.phone" />
            </div>

            <div class="grid gap-2">
                <Label for="address_type">Address type</Label>
                <Select name="address_type" default-value="home">
                    <SelectTrigger id="address_type" class="w-full">
                        <SelectValue placeholder="Select type" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="home">Home</SelectItem>
                        <SelectItem value="office">Office</SelectItem>
                        <SelectItem value="other">Other</SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors.address_type" />
            </div>

            <div class="grid gap-2">
                <Label for="address">Address</Label>
                <Input
                    id="address"
                    name="address"
                    required
                    placeholder="123 Main St"
                />
                <InputError :message="errors.address" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="city">City</Label>
                    <Input id="city" name="city" required />
                    <InputError :message="errors.city" />
                </div>

                <div class="grid gap-2">
                    <Label for="state">State</Label>
                    <Input id="state" name="state" />
                    <InputError :message="errors.state" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="zip">ZIP code</Label>
                    <Input id="zip" name="zip" />
                    <InputError :message="errors.zip" />
                </div>

                <div class="grid gap-2">
                    <Label for="country">Country</Label>
                    <Input id="country" name="country" required />
                    <InputError :message="errors.country" />
                </div>
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="is_default" name="is_default" />
                <Label for="is_default">Default address</Label>
                <InputError :message="errors.is_default" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">
                    Create shipping address
                </Button>
            </div>
        </Form>
    </div>
</template>

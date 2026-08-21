<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import CustomerController from '@/actions/App/Http/Controllers/Backend/CustomerController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit, index } from '@/routes/customers';

type Customer = {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    address: string | null;
    status: boolean;
};

const props = defineProps<{
    customer: Customer;
}>();

defineOptions({
    layout: (pageProps: { customer: Customer }) => ({
        breadcrumbs: [
            {
                title: 'Customers',
                href: index(),
            },
            {
                title: 'Edit customer',
                href: edit(pageProps.customer),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit customer" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit customer"
            :description="`Update the details for ${props.customer.name}`"
        />

        <Form
            v-bind="CustomerController.update.form(props.customer)"
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
                    :default-value="props.customer.name"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        name="email"
                        type="email"
                        required
                        :default-value="props.customer.email"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="phone">Phone</Label>
                    <Input
                        id="phone"
                        name="phone"
                        :default-value="props.customer.phone ?? ''"
                        placeholder="+1 555 000 0000"
                    />
                    <InputError :message="errors.phone" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="address">Address</Label>
                <Input
                    id="address"
                    name="address"
                    :default-value="props.customer.address ?? ''"
                    placeholder="123 Main Street, London"
                />
                <InputError :message="errors.address" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="props.customer.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save customer</Button>
            </div>
        </Form>
    </div>
</template>

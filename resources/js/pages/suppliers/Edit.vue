<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import SupplierController from '@/actions/App/Http/Controllers/Backend/SupplierController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit, index } from '@/routes/suppliers';

type Supplier = {
    id: number;
    name: string;
    contact_person: string | null;
    email: string | null;
    phone: string | null;
    address: string | null;
    status: boolean;
};

const props = defineProps<{
    supplier: Supplier;
}>();

defineOptions({
    layout: (pageProps: { supplier: Supplier }) => ({
        breadcrumbs: [
            {
                title: 'Suppliers',
                href: index(),
            },
            {
                title: 'Edit supplier',
                href: edit(pageProps.supplier),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit supplier" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit supplier"
            :description="`Update the details for ${props.supplier.name}`"
        />

        <Form
            v-bind="SupplierController.update.form(props.supplier)"
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
                    :default-value="props.supplier.name"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="contact_person">Contact person</Label>
                <Input
                    id="contact_person"
                    name="contact_person"
                    :default-value="props.supplier.contact_person ?? ''"
                    placeholder="Jane Smith"
                />
                <InputError :message="errors.contact_person" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        name="email"
                        type="email"
                        :default-value="props.supplier.email ?? ''"
                        placeholder="sales@acme.example"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="phone">Phone</Label>
                    <Input
                        id="phone"
                        name="phone"
                        :default-value="props.supplier.phone ?? ''"
                        placeholder="+44 20 1234 5678"
                    />
                    <InputError :message="errors.phone" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="address">Address</Label>
                <Input
                    id="address"
                    name="address"
                    :default-value="props.supplier.address ?? ''"
                    placeholder="123 Main Street, London"
                />
                <InputError :message="errors.address" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="props.supplier.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save supplier</Button>
            </div>
        </Form>
    </div>
</template>

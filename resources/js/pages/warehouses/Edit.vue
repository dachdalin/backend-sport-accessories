<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import WarehouseController from '@/actions/App/Http/Controllers/Backend/WarehouseController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit, index } from '@/routes/warehouses';

type Warehouse = {
    id: number;
    name: string;
    code: string;
    address: string | null;
    city: string | null;
    country: string | null;
    phone: string | null;
    status: boolean;
};

const props = defineProps<{
    warehouse: Warehouse;
}>();

defineOptions({
    layout: (pageProps: { warehouse: Warehouse }) => ({
        breadcrumbs: [
            {
                title: 'Warehouses',
                href: index(),
            },
            {
                title: 'Edit warehouse',
                href: edit(pageProps.warehouse),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit warehouse" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit warehouse"
            :description="`Update the details for ${props.warehouse.name}`"
        />

        <Form
            v-bind="WarehouseController.update.form(props.warehouse)"
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
                    :default-value="props.warehouse.name"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="code">Code</Label>
                <Input
                    id="code"
                    name="code"
                    required
                    :default-value="props.warehouse.code"
                />
                <InputError :message="errors.code" />
            </div>

            <div class="grid gap-2">
                <Label for="address">Address</Label>
                <Input
                    id="address"
                    name="address"
                    :default-value="props.warehouse.address ?? ''"
                    placeholder="123 Main Street"
                />
                <InputError :message="errors.address" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="city">City</Label>
                    <Input
                        id="city"
                        name="city"
                        :default-value="props.warehouse.city ?? ''"
                        placeholder="London"
                    />
                    <InputError :message="errors.city" />
                </div>

                <div class="grid gap-2">
                    <Label for="country">Country</Label>
                    <Input
                        id="country"
                        name="country"
                        :default-value="props.warehouse.country ?? ''"
                        placeholder="United Kingdom"
                    />
                    <InputError :message="errors.country" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="phone">Phone</Label>
                <Input
                    id="phone"
                    name="phone"
                    :default-value="props.warehouse.phone ?? ''"
                    placeholder="+44 20 1234 5678"
                />
                <InputError :message="errors.phone" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="props.warehouse.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save warehouse</Button>
            </div>
        </Form>
    </div>
</template>

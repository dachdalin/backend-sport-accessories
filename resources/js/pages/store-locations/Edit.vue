<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import StoreLocationController from '@/actions/App/Http/Controllers/Backend/StoreLocationController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit, index } from '@/routes/store-locations';

type StoreLocation = {
    id: number;
    name: string;
    address: string;
    city: string;
    phone: string | null;
    opening_hours: string | null;
    status: boolean;
};

const props = defineProps<{
    storeLocation: StoreLocation;
}>();

defineOptions({
    layout: (pageProps: { storeLocation: StoreLocation }) => ({
        breadcrumbs: [
            {
                title: 'Store locations',
                href: index(),
            },
            {
                title: 'Edit store location',
                href: edit(pageProps.storeLocation),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit store location" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit store location"
            :description="`Update the details for ${props.storeLocation.name}`"
        />

        <Form
            v-bind="StoreLocationController.update.form(props.storeLocation)"
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
                    :default-value="props.storeLocation.name"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="address">Address</Label>
                <Input
                    id="address"
                    name="address"
                    required
                    :default-value="props.storeLocation.address"
                />
                <InputError :message="errors.address" />
            </div>

            <div class="grid gap-2">
                <Label for="city">City</Label>
                <Input
                    id="city"
                    name="city"
                    required
                    :default-value="props.storeLocation.city"
                />
                <InputError :message="errors.city" />
            </div>

            <div class="grid gap-2">
                <Label for="phone">Phone</Label>
                <Input
                    id="phone"
                    name="phone"
                    :default-value="props.storeLocation.phone ?? ''"
                />
                <InputError :message="errors.phone" />
            </div>

            <div class="grid gap-2">
                <Label for="opening_hours">Opening hours</Label>
                <Input
                    id="opening_hours"
                    name="opening_hours"
                    :default-value="props.storeLocation.opening_hours ?? ''"
                />
                <InputError :message="errors.opening_hours" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="props.storeLocation.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save store location</Button>
            </div>
        </Form>
    </div>
</template>

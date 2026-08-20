<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import StoreLocationController from '@/actions/App/Http/Controllers/Backend/StoreLocationController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { create, index } from '@/routes/store-locations';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Store locations',
                href: index(),
            },
            {
                title: 'Add store location',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add store location" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add store location"
            description="Create a new physical retail location"
        />

        <Form
            v-bind="StoreLocationController.store.form()"
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
                    placeholder="Downtown Flagship Store"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="address">Address</Label>
                <Input
                    id="address"
                    name="address"
                    required
                    placeholder="123 Main Street"
                />
                <InputError :message="errors.address" />
            </div>

            <div class="grid gap-2">
                <Label for="city">City</Label>
                <Input id="city" name="city" required placeholder="Bangkok" />
                <InputError :message="errors.city" />
            </div>

            <div class="grid gap-2">
                <Label for="phone">Phone</Label>
                <Input id="phone" name="phone" placeholder="+66 2 123 4567" />
                <InputError :message="errors.phone" />
            </div>

            <div class="grid gap-2">
                <Label for="opening_hours">Opening hours</Label>
                <Input
                    id="opening_hours"
                    name="opening_hours"
                    placeholder="9:00 AM - 9:00 PM"
                />
                <InputError :message="errors.opening_hours" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" :default-value="true" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create store location</Button>
            </div>
        </Form>
    </div>
</template>

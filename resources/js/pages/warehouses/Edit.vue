<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { MapPinIcon, SaveIcon, WarehouseIcon } from '@lucide/vue';
import WarehouseController from '@/actions/App/Http/Controllers/Backend/WarehouseController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
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

    <div class="flex flex-col gap-6">
        <Heading
            title="Edit warehouse"
            :description="`Update the details for ${props.warehouse.name}`"
        />

        <Form
            v-bind="WarehouseController.update.form(props.warehouse)"
            class="flex flex-col gap-6"
            v-slot="{ errors, processing }"
        >
            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2.5">
                        <WarehouseIcon
                            class="size-4.5 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <CardTitle>Identity</CardTitle>
                    </div>
                    <CardDescription>
                        What this warehouse is called and its short code on
                        stock documents.
                    </CardDescription>
                </CardHeader>
                <CardContent class="flex flex-col gap-4">
                    <div class="grid gap-4 sm:grid-cols-2">
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
                                class="font-mono"
                            />
                            <InputError :message="errors.code" />
                        </div>
                    </div>

                    <label
                        for="status"
                        class="flex items-center gap-2.5 rounded-lg border border-input px-3 py-2.5 has-[:checked]:border-primary/50 has-[:checked]:bg-primary/5"
                    >
                        <Checkbox
                            id="status"
                            name="status"
                            :default-value="props.warehouse.status"
                        />
                        <span class="grid gap-0.5">
                            <span class="text-sm font-medium">Active</span>
                            <span class="text-xs text-muted-foreground">
                                Available as a stock location while active.
                            </span>
                        </span>
                    </label>
                    <InputError :message="errors.status" />
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2.5">
                        <MapPinIcon
                            class="size-4.5 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <CardTitle>Location</CardTitle>
                    </div>
                    <CardDescription>
                        Where this warehouse is, for shipping labels and pickup
                        routing.
                    </CardDescription>
                </CardHeader>
                <CardContent class="flex flex-col gap-4">
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

                    <div class="grid gap-4 sm:grid-cols-2">
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

                    <div class="grid gap-2 sm:max-w-xs">
                        <Label for="phone">Phone</Label>
                        <Input
                            id="phone"
                            name="phone"
                            :default-value="props.warehouse.phone ?? ''"
                            placeholder="+44 20 1234 5678"
                        />
                        <InputError :message="errors.phone" />
                    </div>
                </CardContent>
                <CardFooter class="flex-col gap-3 border-t pt-6 sm:flex-row">
                    <Button class="w-full sm:w-auto" :disabled="processing">
                        <Spinner v-if="processing" />
                        <SaveIcon v-else class="size-4" aria-hidden="true" />
                        Save warehouse
                    </Button>
                    <Button class="w-full sm:w-auto" variant="outline" as-child>
                        <Link :href="index()">Cancel</Link>
                    </Button>
                </CardFooter>
            </Card>
        </Form>
    </div>
</template>

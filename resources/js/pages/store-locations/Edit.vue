<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Clock, Eye, MapPin, Rocket } from '@lucide/vue';
import StoreLocationController from '@/actions/App/Http/Controllers/Backend/StoreLocationController';
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

defineProps<{
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

    <div class="flex flex-col gap-6">
        <Heading
            title="Edit store location"
            :description="`Update the details for ${storeLocation.name}`"
        />

        <Form
            v-bind="StoreLocationController.update.form(storeLocation)"
            class="grid gap-6 lg:grid-cols-3 lg:items-start"
            v-slot="{ errors, processing }"
        >
            <div class="flex flex-col gap-6 lg:col-span-2">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <MapPin
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Location details</CardTitle>
                        </div>
                        <CardDescription>
                            Where customers can find this store.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div class="grid gap-2">
                            <Label for="name">Name</Label>
                            <Input
                                id="name"
                                name="name"
                                required
                                autofocus
                                :default-value="storeLocation.name"
                            />
                            <InputError :message="errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="address">Address</Label>
                            <Input
                                id="address"
                                name="address"
                                required
                                :default-value="storeLocation.address"
                            />
                            <InputError :message="errors.address" />
                        </div>

                        <div class="grid gap-2 sm:max-w-xs">
                            <Label for="city">City</Label>
                            <Input
                                id="city"
                                name="city"
                                required
                                :default-value="storeLocation.city"
                            />
                            <InputError :message="errors.city" />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Clock
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Contact &amp; hours</CardTitle>
                        </div>
                        <CardDescription>
                            How and when customers can reach this store.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="phone">Phone</Label>
                            <Input
                                id="phone"
                                name="phone"
                                :default-value="storeLocation.phone ?? ''"
                                placeholder="+66 2 123 4567"
                            />
                            <InputError :message="errors.phone" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="opening_hours">Opening hours</Label>
                            <Input
                                id="opening_hours"
                                name="opening_hours"
                                :default-value="
                                    storeLocation.opening_hours ?? ''
                                "
                                placeholder="9:00 AM - 9:00 PM"
                            />
                            <InputError :message="errors.opening_hours" />
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="flex flex-col gap-6">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Eye
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Visibility</CardTitle>
                        </div>
                        <CardDescription>
                            Whether this location is shown to customers.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <label
                            for="status"
                            class="flex cursor-pointer items-center gap-3 rounded-lg border border-input px-3 py-2.5 transition-colors has-[[data-state=checked]]:border-primary has-[[data-state=checked]]:bg-primary/5"
                        >
                            <Checkbox
                                id="status"
                                name="status"
                                value="1"
                                :default-value="storeLocation.status"
                            />
                            <span class="text-sm font-medium">Active</span>
                        </label>
                        <InputError :message="errors.status" />
                    </CardContent>
                </Card>

                <Card class="lg:sticky lg:top-6">
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Rocket
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Publish</CardTitle>
                        </div>
                        <CardDescription>
                            Save your changes to this location.
                        </CardDescription>
                    </CardHeader>
                    <CardFooter class="gap-3 border-t pt-6">
                        <Button :disabled="processing">
                            <Spinner v-if="processing" />
                            Save store location
                        </Button>
                        <Button variant="outline" as-child>
                            <Link :href="index()">Cancel</Link>
                        </Button>
                    </CardFooter>
                </Card>
            </div>
        </Form>
    </div>
</template>

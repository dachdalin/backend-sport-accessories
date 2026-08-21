<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import ShippingAddressController from '@/actions/App/Http/Controllers/Backend/ShippingAddressController';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { create, edit, index } from '@/routes/shipping-addresses';

interface ShippingAddress {
    id: number;
    contact_person_name: string;
    address_type: string;
    address: string;
    city: string;
    country: string;
    is_default: boolean;
    customer: {
        id: number;
        name: string;
    };
}

defineProps<{
    shippingAddresses: ShippingAddress[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Shipping addresses',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Shipping addresses" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Shipping addresses"
                description="Manage saved customer shipping addresses"
            />
            <Button as-child>
                <Link :href="create()">Add shipping address</Link>
            </Button>
        </div>

        <div
            class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
        >
            <table class="w-full text-left text-sm">
                <thead
                    class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border"
                >
                    <tr>
                        <th class="p-3 font-medium">Customer</th>
                        <th class="p-3 font-medium">Contact</th>
                        <th class="p-3 font-medium">Address</th>
                        <th class="p-3 font-medium">City</th>
                        <th class="p-3 font-medium">Type</th>
                        <th class="p-3 font-medium">Default</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="shippingAddress in shippingAddresses"
                        :key="shippingAddress.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">
                            {{ shippingAddress.customer.name }}
                        </td>
                        <td class="p-3">
                            {{ shippingAddress.contact_person_name }}
                        </td>
                        <td class="max-w-xs truncate p-3 text-muted-foreground">
                            {{ shippingAddress.address }}
                        </td>
                        <td class="p-3">
                            {{ shippingAddress.city }},
                            {{ shippingAddress.country }}
                        </td>
                        <td class="p-3 capitalize">
                            {{ shippingAddress.address_type }}
                        </td>
                        <td class="p-3">
                            <Badge
                                v-if="shippingAddress.is_default"
                                variant="default"
                            >
                                Default
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(shippingAddress)"
                                        >Edit</Link
                                    >
                                </Button>

                                <Dialog>
                                    <DialogTrigger as-child>
                                        <Button variant="destructive" size="sm"
                                            >Delete</Button
                                        >
                                    </DialogTrigger>
                                    <DialogContent>
                                        <Form
                                            v-bind="
                                                ShippingAddressController.destroy.form(
                                                    shippingAddress,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete this shipping
                                                    address?</DialogTitle
                                                >
                                            </DialogHeader>

                                            <DialogFooter class="mt-6 gap-2">
                                                <DialogClose as-child>
                                                    <Button variant="secondary"
                                                        >Cancel</Button
                                                    >
                                                </DialogClose>
                                                <Button
                                                    type="submit"
                                                    variant="destructive"
                                                    :disabled="processing"
                                                >
                                                    Delete
                                                </Button>
                                            </DialogFooter>
                                        </Form>
                                    </DialogContent>
                                </Dialog>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="shippingAddresses.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="7"
                        >
                            No shipping addresses yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

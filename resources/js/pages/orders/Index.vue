<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import OrderController from '@/actions/App/Http/Controllers/Backend/OrderController';
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
import { usePermissions } from '@/composables/usePermissions';
import { create, edit, index } from '@/routes/orders';

type Order = {
    id: number;
    order_number: string;
    customer_name: string;
    order_status: string;
    payment_status: string;
    order_amount: string;
    items_count: number;
    created_at: string;
};

defineProps<{
    orders: Order[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Orders',
                href: index(),
            },
        ],
    },
});

const orderStatusVariant: Record<string, 'default' | 'secondary' | 'destructive'> = {
    pending: 'secondary',
    processing: 'default',
    shipped: 'default',
    delivered: 'default',
    cancelled: 'destructive',
    returned: 'destructive',
};

const paymentStatusVariant: Record<string, 'default' | 'secondary' | 'destructive'> = {
    unpaid: 'secondary',
    paid: 'default',
    refunded: 'destructive',
};

const { can } = usePermissions();
</script>

<template>
    <Head title="Orders" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Orders"
                description="Manage customer orders"
            />
            <Button v-if="can('create orders')" as-child>
                <Link :href="create()">Add order</Link>
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
                        <th class="p-3 font-medium">Order #</th>
                        <th class="p-3 font-medium">Customer</th>
                        <th class="p-3 font-medium">Items</th>
                        <th class="p-3 font-medium">Total</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 font-medium">Payment</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="order in orders"
                        :key="order.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">{{ order.order_number }}</td>
                        <td class="p-3">{{ order.customer_name }}</td>
                        <td class="p-3">{{ order.items_count }}</td>
                        <td class="p-3">${{ order.order_amount }}</td>
                        <td class="p-3">
                            <Badge :variant="orderStatusVariant[order.order_status] ?? 'secondary'">
                                {{ order.order_status }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <Badge :variant="paymentStatusVariant[order.payment_status] ?? 'secondary'">
                                {{ order.payment_status }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button
                                    v-if="can('edit orders')"
                                    variant="outline"
                                    size="sm"
                                    as-child
                                >
                                    <Link :href="edit(order)">Edit</Link>
                                </Button>

                                <Dialog v-if="can('delete orders')">
                                    <DialogTrigger as-child>
                                        <Button variant="destructive" size="sm"
                                            >Delete</Button
                                        >
                                    </DialogTrigger>
                                    <DialogContent>
                                        <Form
                                            v-bind="
                                                OrderController.destroy.form(
                                                    order,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete order "{{
                                                        order.order_number
                                                    }}"?</DialogTitle
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

                    <tr v-if="orders.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="7"
                        >
                            No orders yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

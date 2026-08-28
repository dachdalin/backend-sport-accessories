<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Package, Receipt, ShoppingCart, UserRound } from '@lucide/vue';
import OrderController from '@/actions/App/Http/Controllers/Backend/OrderController';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
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
import { edit, index, show } from '@/routes/orders';

type OrderItem = {
    id: number;
    product_name: string;
    unit_price: string;
    quantity: number;
    subtotal: string;
};

type Order = {
    id: number;
    order_number: string;
    customer_name: string;
    customer_email: string | null;
    customer_phone: string | null;
    shipping_address: string;
    order_status: string;
    payment_status: string;
    payment_method: string | null;
    discount_amount: string;
    discount_type: string | null;
    shipping_cost: string;
    order_amount: string;
    order_note: string | null;
    items: OrderItem[];
    created_at: string;
    updated_at: string;
};

const props = defineProps<{
    order: Order;
}>();

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

function formatDate(value: string): string {
    return new Date(value).toLocaleString();
}

defineOptions({
    layout: (pageProps: { order: Order }) => ({
        breadcrumbs: [
            {
                title: 'Orders',
                href: index(),
            },
            {
                title: pageProps.order.order_number,
                href: show(pageProps.order),
            },
        ],
    }),
});

const { can } = usePermissions();
</script>

<template>
    <Head :title="order.order_number" />

    <div class="flex flex-col space-y-6">
        <div class="flex items-center justify-between">
            <Heading
                :title="order.order_number"
                description="Order details"
            />
            <div class="flex gap-2">
                <Button
                    v-if="can('edit orders')"
                    variant="outline"
                    as-child
                >
                    <Link :href="edit(props.order)">Edit</Link>
                </Button>

                <Dialog v-if="can('delete orders')">
                    <DialogTrigger as-child>
                        <Button variant="destructive">Delete</Button>
                    </DialogTrigger>
                    <DialogContent>
                        <Form
                            v-bind="OrderController.destroy.form(props.order)"
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
                                    <Button variant="secondary">Cancel</Button>
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
        </div>

        <div class="flex max-w-3xl flex-col gap-6">
            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2.5">
                        <Package
                            class="size-4.5 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <CardTitle>Order</CardTitle>
                    </div>
                    <CardDescription>
                        Status and payment for this order.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <dl class="grid grid-cols-3 gap-y-4">
                        <dt class="text-sm font-medium text-muted-foreground">
                            Status
                        </dt>
                        <dd class="col-span-2 text-sm">
                            <Badge
                                :variant="
                                    orderStatusVariant[order.order_status] ??
                                    'secondary'
                                "
                            >
                                {{ order.order_status }}
                            </Badge>
                        </dd>

                        <dt class="text-sm font-medium text-muted-foreground">
                            Payment
                        </dt>
                        <dd class="col-span-2 text-sm">
                            <Badge
                                :variant="
                                    paymentStatusVariant[
                                        order.payment_status
                                    ] ?? 'secondary'
                                "
                            >
                                {{ order.payment_status }}
                            </Badge>
                        </dd>

                        <dt class="text-sm font-medium text-muted-foreground">
                            Payment method
                        </dt>
                        <dd class="col-span-2 text-sm">
                            {{ order.payment_method ?? '—' }}
                        </dd>

                        <dt class="text-sm font-medium text-muted-foreground">
                            Placed
                        </dt>
                        <dd class="col-span-2 text-sm">
                            {{ formatDate(order.created_at) }}
                        </dd>

                        <dt class="text-sm font-medium text-muted-foreground">
                            Last updated
                        </dt>
                        <dd class="col-span-2 text-sm">
                            {{ formatDate(order.updated_at) }}
                        </dd>

                        <template v-if="order.order_note">
                            <dt
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Note
                            </dt>
                            <dd class="col-span-2 text-sm">
                                {{ order.order_note }}
                            </dd>
                        </template>
                    </dl>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2.5">
                        <UserRound
                            class="size-4.5 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <CardTitle>Customer</CardTitle>
                    </div>
                    <CardDescription>
                        Who placed the order and where it ships.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <dl class="grid grid-cols-3 gap-y-4">
                        <dt class="text-sm font-medium text-muted-foreground">
                            Name
                        </dt>
                        <dd class="col-span-2 text-sm">
                            {{ order.customer_name }}
                        </dd>

                        <dt class="text-sm font-medium text-muted-foreground">
                            Email
                        </dt>
                        <dd class="col-span-2 text-sm">
                            {{ order.customer_email ?? '—' }}
                        </dd>

                        <dt class="text-sm font-medium text-muted-foreground">
                            Phone
                        </dt>
                        <dd class="col-span-2 text-sm">
                            {{ order.customer_phone ?? '—' }}
                        </dd>

                        <dt class="text-sm font-medium text-muted-foreground">
                            Shipping address
                        </dt>
                        <dd class="col-span-2 text-sm whitespace-pre-line">
                            {{ order.shipping_address }}
                        </dd>
                    </dl>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2.5">
                        <ShoppingCart
                            class="size-4.5 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <CardTitle>Items</CardTitle>
                    </div>
                    <CardDescription>
                        Products included in this order.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div
                        class="overflow-x-auto rounded-lg border border-sidebar-border/70 dark:border-sidebar-border"
                    >
                        <table class="w-full text-left text-sm">
                            <thead
                                class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border"
                            >
                                <tr>
                                    <th class="p-3 font-medium">Product</th>
                                    <th class="p-3 font-medium">Price</th>
                                    <th class="p-3 font-medium">Qty</th>
                                    <th class="p-3 text-right font-medium">
                                        Subtotal
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="item in order.items"
                                    :key="item.id"
                                    class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                                >
                                    <td class="p-3">
                                        {{ item.product_name }}
                                    </td>
                                    <td class="p-3">
                                        ${{ item.unit_price }}
                                    </td>
                                    <td class="p-3">{{ item.quantity }}</td>
                                    <td class="p-3 text-right">
                                        ${{ item.subtotal }}
                                    </td>
                                </tr>

                                <tr v-if="order.items.length === 0">
                                    <td
                                        class="p-6 text-center text-muted-foreground"
                                        colspan="4"
                                    >
                                        No items on this order.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2.5">
                        <Receipt
                            class="size-4.5 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <CardTitle>Totals</CardTitle>
                    </div>
                    <CardDescription>
                        Shipping, discount, and the final amount charged.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <dl class="grid grid-cols-3 gap-y-4">
                        <dt class="text-sm font-medium text-muted-foreground">
                            Shipping cost
                        </dt>
                        <dd class="col-span-2 text-sm">
                            ${{ order.shipping_cost }}
                        </dd>

                        <dt class="text-sm font-medium text-muted-foreground">
                            Discount
                        </dt>
                        <dd class="col-span-2 text-sm">
                            ${{ order.discount_amount }}
                            <span
                                v-if="order.discount_type"
                                class="text-muted-foreground"
                                >({{ order.discount_type }})</span
                            >
                        </dd>

                        <dt class="text-sm font-medium text-muted-foreground">
                            Order total
                        </dt>
                        <dd class="col-span-2 text-sm font-semibold">
                            ${{ order.order_amount }}
                        </dd>
                    </dl>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

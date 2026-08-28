<script setup lang="ts">
import { Form, Head, Link, router } from '@inertiajs/vue3';
import { X } from '@lucide/vue';
import { onBeforeUnmount, ref } from 'vue';
import OrderController from '@/actions/App/Http/Controllers/Backend/OrderController';
import Heading from '@/components/Heading.vue';
import Pagination from '@/components/Pagination.vue';
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
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
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

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type Paginated<T> = {
    data: T[];
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
    current_page: number;
    last_page: number;
};

type SelectOption = {
    value: string;
    label: string;
};

const props = defineProps<{
    orders: Paginated<Order>;
    orderStatuses: SelectOption[];
    paymentStatuses: SelectOption[];
    filters: {
        order_status?: string;
        payment_status?: string;
        search?: string;
    };
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

const orderStatusFilter = ref(props.filters.order_status ?? 'all');
const paymentStatusFilter = ref(props.filters.payment_status ?? 'all');
const search = ref(props.filters.search ?? '');
const hasFilters = ref(
    Boolean(
        props.filters.order_status ||
            props.filters.payment_status ||
            props.filters.search,
    ),
);

function applyFilters(): void {
    hasFilters.value =
        orderStatusFilter.value !== 'all' ||
        paymentStatusFilter.value !== 'all' ||
        search.value.trim() !== '';

    router.get(
        index().url,
        {
            order_status:
                orderStatusFilter.value === 'all'
                    ? undefined
                    : orderStatusFilter.value,
            payment_status:
                paymentStatusFilter.value === 'all'
                    ? undefined
                    : paymentStatusFilter.value,
            search: search.value.trim() || undefined,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}

let searchDebounce: ReturnType<typeof setTimeout> | undefined;

function onSearchInput(): void {
    clearTimeout(searchDebounce);
    searchDebounce = setTimeout(applyFilters, 300);
}

onBeforeUnmount(() => clearTimeout(searchDebounce));

function clearFilters(): void {
    orderStatusFilter.value = 'all';
    paymentStatusFilter.value = 'all';
    search.value = '';
    applyFilters();
}
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

        <div class="flex flex-wrap items-center gap-3">
            <Input
                v-model="search"
                type="search"
                placeholder="Search order #, customer name, or email"
                class="w-full sm:w-72"
                @input="onSearchInput"
            />

            <Select
                v-model="orderStatusFilter"
                @update:model-value="applyFilters"
            >
                <SelectTrigger class="w-full sm:w-44">
                    <SelectValue placeholder="All statuses" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="all">All statuses</SelectItem>
                    <SelectItem
                        v-for="option in orderStatuses"
                        :key="option.value"
                        :value="option.value"
                    >
                        {{ option.label }}
                    </SelectItem>
                </SelectContent>
            </Select>

            <Select
                v-model="paymentStatusFilter"
                @update:model-value="applyFilters"
            >
                <SelectTrigger class="w-full sm:w-44">
                    <SelectValue placeholder="All payment statuses" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="all">All payment statuses</SelectItem>
                    <SelectItem
                        v-for="option in paymentStatuses"
                        :key="option.value"
                        :value="option.value"
                    >
                        {{ option.label }}
                    </SelectItem>
                </SelectContent>
            </Select>

            <Button
                v-if="hasFilters"
                variant="ghost"
                size="sm"
                @click="clearFilters"
            >
                <X class="size-4" aria-hidden="true" />
                Clear filters
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
                        v-for="order in orders.data"
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

                    <tr v-if="orders.data.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="7"
                        >
                            {{
                                hasFilters
                                    ? 'No orders match these filters.'
                                    : 'No orders yet.'
                            }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :meta="orders" label="orders" />
    </div>
</template>

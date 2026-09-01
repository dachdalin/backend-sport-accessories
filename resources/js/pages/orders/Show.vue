<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Printer, ReceiptText, ShieldCheck, Trash2 } from '@lucide/vue';
import { computed } from 'vue';
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

type Business = {
    name: string;
    logoUrl: string | null;
    email: string;
    phone: string;
    address: string;
    currencySymbol: string;
};

const props = defineProps<{
    order: Order;
    business: Business;
}>();

const orderStatusVariant: Record<
    string,
    'default' | 'secondary' | 'destructive'
> = {
    pending: 'secondary',
    processing: 'default',
    shipped: 'default',
    delivered: 'default',
    cancelled: 'destructive',
    returned: 'destructive',
};

const paymentStatusVariant: Record<
    string,
    'default' | 'secondary' | 'destructive'
> = {
    unpaid: 'secondary',
    paid: 'default',
    refunded: 'destructive',
};

const { can } = usePermissions();

const itemsTotal = computed(() =>
    props.order.items.reduce(
        (total, item) => total + parseMoney(item.subtotal),
        0,
    ),
);

const beforeFinalAdjustments = computed(
    () => itemsTotal.value + parseMoney(props.order.shipping_cost),
);

function parseMoney(value: string | null): number {
    return Number(String(value ?? '0').replaceAll(',', '')) || 0;
}

function formatCurrency(value: string | number): string {
    return `${props.business.currencySymbol}${Number(value).toLocaleString(
        undefined,
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        },
    )}`;
}

function formatDate(value: string): string {
    return new Date(value).toLocaleString();
}

function printInvoice(): void {
    window.print();
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
</script>

<template>
    <Head :title="`Invoice ${order.order_number}`" />

    <div class="flex flex-col gap-6 print:block">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between print:hidden"
        >
            <Heading :title="order.order_number" description="Order invoice" />

            <div class="flex flex-wrap gap-2">
                <Button variant="outline" @click="printInvoice">
                    <Printer class="size-4" aria-hidden="true" />
                    Print
                </Button>

                <Button v-if="can('edit orders')" variant="outline" as-child>
                    <Link :href="edit(props.order)">Edit</Link>
                </Button>

                <Dialog v-if="can('delete orders')">
                    <DialogTrigger as-child>
                        <Button variant="destructive">
                            <Trash2 class="size-4" aria-hidden="true" />
                            Delete
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <Form
                            v-bind="OrderController.destroy.form(props.order)"
                            :options="{ preserveScroll: true }"
                            v-slot="{ processing }"
                        >
                            <DialogHeader class="space-y-3">
                                <DialogTitle>
                                    Delete order "{{ order.order_number }}"?
                                </DialogTitle>
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

        <section
            class="relative max-w-5xl overflow-hidden rounded-lg border border-[#D7DEE8] bg-white text-[#18202B] shadow-sm print:max-w-none print:rounded-none print:border-0 print:shadow-none"
        >
            <div
                class="absolute inset-y-0 left-0 w-2 bg-[#1F8A70] print:w-1.5"
                aria-hidden="true"
            />
            <div
                class="absolute top-0 right-0 h-28 w-28 border-b border-l border-[#D7DEE8] bg-[linear-gradient(135deg,#F7C948_0_12%,transparent_12%_25%,#1F8A70_25%_37%,transparent_37%)] opacity-80"
                aria-hidden="true"
            />

            <div class="grid gap-8 p-6 pl-8 sm:p-8 sm:pl-10 lg:p-10 lg:pl-12">
                <header
                    class="grid gap-6 border-b border-[#D7DEE8] pb-8 md:grid-cols-[1fr_auto] md:items-start"
                >
                    <div class="flex gap-4">
                        <div
                            class="flex size-16 shrink-0 items-center justify-center rounded-md border border-[#D7DEE8] bg-[#F6F8FA]"
                        >
                            <img
                                v-if="business.logoUrl"
                                :src="business.logoUrl"
                                :alt="`${business.name} logo`"
                                class="size-full object-contain p-2"
                            />
                            <ShieldCheck
                                v-else
                                class="size-8 text-[#1F8A70]"
                                aria-hidden="true"
                            />
                        </div>

                        <div class="min-w-0">
                            <p
                                class="text-xs font-semibold text-[#1F8A70] uppercase"
                            >
                                Seller
                            </p>
                            <h1
                                class="mt-1 text-2xl font-black tracking-[0] text-[#18202B] sm:text-3xl"
                            >
                                {{ business.name }}
                            </h1>
                            <div
                                class="mt-3 grid gap-1 text-sm text-[#556171] sm:grid-cols-2"
                            >
                                <p v-if="business.email">
                                    {{ business.email }}
                                </p>
                                <p v-if="business.phone">
                                    {{ business.phone }}
                                </p>
                                <p
                                    v-if="business.address"
                                    class="whitespace-pre-line sm:col-span-2"
                                >
                                    {{ business.address }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="md:text-right">
                        <div
                            class="inline-flex items-center gap-2 rounded-md border-2 border-[#18202B] bg-[#F7C948] px-4 py-2 text-xl font-black tracking-[0] text-[#18202B] uppercase"
                        >
                            <ReceiptText class="size-5" aria-hidden="true" />
                            Invoice
                        </div>
                        <dl class="mt-4 grid gap-2 text-sm">
                            <div
                                class="flex justify-between gap-6 md:justify-end"
                            >
                                <dt class="font-medium text-[#556171]">
                                    Invoice no.
                                </dt>
                                <dd class="font-mono text-[#18202B]">
                                    {{ order.order_number }}
                                </dd>
                            </div>
                            <div
                                class="flex justify-between gap-6 md:justify-end"
                            >
                                <dt class="font-medium text-[#556171]">Date</dt>
                                <dd class="font-mono text-[#18202B]">
                                    {{ formatDate(order.created_at) }}
                                </dd>
                            </div>
                            <div
                                class="flex justify-between gap-6 md:justify-end"
                            >
                                <dt class="font-medium text-[#556171]">
                                    Payment
                                </dt>
                                <dd>
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
                            </div>
                            <div
                                class="flex justify-between gap-6 md:justify-end"
                            >
                                <dt class="font-medium text-[#556171]">
                                    Order
                                </dt>
                                <dd>
                                    <Badge
                                        :variant="
                                            orderStatusVariant[
                                                order.order_status
                                            ] ?? 'secondary'
                                        "
                                    >
                                        {{ order.order_status }}
                                    </Badge>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </header>

                <section
                    class="grid gap-4 rounded-md border border-[#D7DEE8] bg-[#F6F8FA] p-4 md:grid-cols-2"
                >
                    <div>
                        <p
                            class="text-xs font-semibold text-[#1F8A70] uppercase"
                        >
                            Buyer
                        </p>
                        <h2 class="mt-1 text-lg font-bold">
                            {{ order.customer_name }}
                        </h2>
                        <div class="mt-2 grid gap-1 text-sm text-[#556171]">
                            <p v-if="order.customer_email">
                                {{ order.customer_email }}
                            </p>
                            <p v-if="order.customer_phone">
                                {{ order.customer_phone }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <p
                            class="text-xs font-semibold text-[#1F8A70] uppercase"
                        >
                            Ship to
                        </p>
                        <p
                            class="mt-2 text-sm whitespace-pre-line text-[#556171]"
                        >
                            {{ order.shipping_address }}
                        </p>
                    </div>
                </section>

                <section class="overflow-x-auto">
                    <table class="w-full table-fixed text-left text-sm">
                        <thead>
                            <tr
                                class="border-y-2 border-[#18202B] bg-[#18202B] text-white"
                            >
                                <th class="w-14 px-3 py-3 font-semibold">No</th>
                                <th class="px-3 py-3 font-semibold">Item</th>
                                <th
                                    class="w-20 px-3 py-3 text-right font-semibold"
                                >
                                    Qty
                                </th>
                                <th
                                    class="w-32 px-3 py-3 text-right font-semibold"
                                >
                                    Unit price
                                </th>
                                <th
                                    class="w-32 px-3 py-3 text-right font-semibold"
                                >
                                    Amount
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(item, itemIndex) in order.items"
                                :key="item.id"
                                class="border-b border-[#D7DEE8]"
                            >
                                <td class="px-3 py-4 font-mono text-[#556171]">
                                    {{ itemIndex + 1 }}
                                </td>
                                <td class="px-3 py-4 font-medium">
                                    {{ item.product_name }}
                                </td>
                                <td class="px-3 py-4 text-right font-mono">
                                    {{ item.quantity }}
                                </td>
                                <td class="px-3 py-4 text-right font-mono">
                                    {{
                                        formatCurrency(
                                            parseMoney(item.unit_price),
                                        )
                                    }}
                                </td>
                                <td class="px-3 py-4 text-right font-mono">
                                    {{
                                        formatCurrency(
                                            parseMoney(item.subtotal),
                                        )
                                    }}
                                </td>
                            </tr>

                            <tr v-if="order.items.length === 0">
                                <td
                                    class="px-3 py-10 text-center text-[#556171]"
                                    colspan="5"
                                >
                                    No items on this order.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </section>

                <section
                    class="grid gap-6 border-t border-[#D7DEE8] pt-6 lg:grid-cols-[1fr_22rem]"
                >
                    <div class="rounded-md bg-[#F6F8FA] p-4">
                        <p
                            class="text-xs font-semibold text-[#1F8A70] uppercase"
                        >
                            Note
                        </p>
                        <p class="mt-2 text-sm text-[#556171]">
                            {{
                                order.order_note ?? 'No note for this invoice.'
                            }}
                        </p>
                        <p class="mt-4 text-sm text-[#556171]">
                            Payment method:
                            <span class="font-medium text-[#18202B]">
                                {{ order.payment_method ?? 'Not recorded' }}
                            </span>
                        </p>
                    </div>

                    <dl class="grid gap-3 text-sm">
                        <div class="flex justify-between gap-6">
                            <dt class="text-[#556171]">Total</dt>
                            <dd class="font-mono">
                                {{ formatCurrency(beforeFinalAdjustments) }}
                            </dd>
                        </div>
                        <div class="flex justify-between gap-6">
                            <dt class="text-[#556171]">Tax</dt>
                            <dd class="font-mono">{{ formatCurrency(0) }}</dd>
                        </div>
                        <div class="flex justify-between gap-6">
                            <dt class="text-[#556171]">
                                Discount
                                <span v-if="order.discount_type">
                                    ({{ order.discount_type }})
                                </span>
                            </dt>
                            <dd class="font-mono">
                                -{{
                                    formatCurrency(
                                        parseMoney(order.discount_amount),
                                    )
                                }}
                            </dd>
                        </div>
                        <div
                            class="mt-2 flex justify-between gap-6 border-t-2 border-[#18202B] bg-[#1F8A70] px-4 py-3 text-white"
                        >
                            <dt class="text-base font-black uppercase">
                                Final
                            </dt>
                            <dd class="font-mono text-base font-black">
                                {{
                                    formatCurrency(
                                        parseMoney(order.order_amount),
                                    )
                                }}
                            </dd>
                        </div>
                    </dl>
                </section>

                <section class="grid gap-8 pt-8 sm:grid-cols-2">
                    <div class="grid gap-10">
                        <p
                            class="text-xs font-semibold text-[#1F8A70] uppercase"
                        >
                            Seller signature
                        </p>
                        <div class="border-t-2 border-[#18202B] pt-3 text-sm">
                            {{ business.name }}
                        </div>
                    </div>

                    <div class="grid gap-10">
                        <p
                            class="text-xs font-semibold text-[#1F8A70] uppercase"
                        >
                            Buyer signature
                        </p>
                        <div class="border-t-2 border-[#18202B] pt-3 text-sm">
                            {{ order.customer_name }}
                        </div>
                    </div>
                </section>
            </div>
        </section>
    </div>
</template>

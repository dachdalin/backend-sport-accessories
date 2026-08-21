<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import OrderController from '@/actions/App/Http/Controllers/Backend/OrderController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { edit, index } from '@/routes/orders';

interface SelectOption {
    value: number | string;
    label: string;
}

interface ProductOption extends SelectOption {
    unit_price: string;
}

type OrderItem = {
    id: number;
    product_id: number | null;
    unit_price: string;
    quantity: number;
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
    order_note: string | null;
    items: OrderItem[];
};

const props = defineProps<{
    order: Order;
    products: ProductOption[];
    orderStatuses: SelectOption[];
    paymentStatuses: SelectOption[];
    discountTypes: SelectOption[];
}>();

defineOptions({
    layout: (pageProps: { order: Order }) => ({
        breadcrumbs: [
            {
                title: 'Orders',
                href: index(),
            },
            {
                title: `Edit order ${pageProps.order.order_number}`,
                href: edit(pageProps.order),
            },
        ],
    }),
});

let nextKey = 0;

function itemFromOrder(item: OrderItem) {
    return {
        key: nextKey++,
        product_id: item.product_id ?? '',
        quantity: item.quantity,
        unit_price: item.unit_price,
    };
}

const items = ref(
    props.order.items.length > 0
        ? props.order.items.map(itemFromOrder)
        : [{ key: nextKey++, product_id: '', quantity: 1, unit_price: '' }],
);

function addItem() {
    items.value.push({ key: nextKey++, product_id: '', quantity: 1, unit_price: '' });
}

function removeItem(index: number) {
    items.value.splice(index, 1);
}

function onProductChange(index: number, productId: string) {
    const product = props.products.find((option) => String(option.value) === productId);

    items.value[index].product_id = productId;

    if (product) {
        items.value[index].unit_price = product.unit_price;
    }
}
</script>

<template>
    <Head :title="`Edit order ${order.order_number}`" />

    <div class="flex flex-col space-y-6">
        <Heading
            :title="`Edit order ${order.order_number}`"
            :description="`Update the details for ${order.customer_name}'s order`"
        />

        <Form
            v-bind="OrderController.update.form(order)"
            class="max-w-2xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="customer_name">Customer name</Label>
                    <Input
                        id="customer_name"
                        name="customer_name"
                        required
                        autofocus
                        :default-value="order.customer_name"
                    />
                    <InputError :message="errors.customer_name" />
                </div>

                <div class="grid gap-2">
                    <Label for="customer_email">Customer email</Label>
                    <Input
                        id="customer_email"
                        name="customer_email"
                        type="email"
                        :default-value="order.customer_email ?? ''"
                    />
                    <InputError :message="errors.customer_email" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="customer_phone">Customer phone</Label>
                <Input
                    id="customer_phone"
                    name="customer_phone"
                    :default-value="order.customer_phone ?? ''"
                />
                <InputError :message="errors.customer_phone" />
            </div>

            <div class="grid gap-2">
                <Label for="shipping_address">Shipping address</Label>
                <Textarea
                    id="shipping_address"
                    name="shipping_address"
                    required
                    rows="3"
                    :default-value="order.shipping_address"
                />
                <InputError :message="errors.shipping_address" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="order_status">Order status</Label>
                    <Select name="order_status" :default-value="order.order_status">
                        <SelectTrigger id="order_status" class="w-full">
                            <SelectValue placeholder="Select status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="option in orderStatuses"
                                :key="option.value"
                                :value="String(option.value)"
                            >
                                {{ option.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.order_status" />
                </div>

                <div class="grid gap-2">
                    <Label for="payment_status">Payment status</Label>
                    <Select name="payment_status" :default-value="order.payment_status">
                        <SelectTrigger id="payment_status" class="w-full">
                            <SelectValue placeholder="Select status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="option in paymentStatuses"
                                :key="option.value"
                                :value="String(option.value)"
                            >
                                {{ option.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.payment_status" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="payment_method">Payment method</Label>
                <Input
                    id="payment_method"
                    name="payment_method"
                    :default-value="order.payment_method ?? ''"
                />
                <InputError :message="errors.payment_method" />
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="grid gap-2">
                    <Label for="discount_amount">Discount amount</Label>
                    <Input
                        id="discount_amount"
                        name="discount_amount"
                        type="number"
                        step="0.01"
                        min="0"
                        :default-value="order.discount_amount"
                    />
                    <InputError :message="errors.discount_amount" />
                </div>

                <div class="grid gap-2">
                    <Label for="discount_type">Discount type</Label>
                    <Select name="discount_type" :default-value="order.discount_type ?? undefined">
                        <SelectTrigger id="discount_type" class="w-full">
                            <SelectValue placeholder="Select type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="option in discountTypes"
                                :key="option.value"
                                :value="String(option.value)"
                            >
                                {{ option.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.discount_type" />
                </div>

                <div class="grid gap-2">
                    <Label for="shipping_cost">Shipping cost</Label>
                    <Input
                        id="shipping_cost"
                        name="shipping_cost"
                        type="number"
                        step="0.01"
                        min="0"
                        :default-value="order.shipping_cost"
                    />
                    <InputError :message="errors.shipping_cost" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="order_note">Order note</Label>
                <Textarea
                    id="order_note"
                    name="order_note"
                    rows="2"
                    :default-value="order.order_note ?? ''"
                />
                <InputError :message="errors.order_note" />
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <Label>Order items</Label>
                    <Button type="button" variant="outline" size="sm" @click="addItem">
                        Add item
                    </Button>
                </div>
                <InputError :message="errors.items" />

                <div
                    v-for="(item, itemIndex) in items"
                    :key="item.key"
                    class="grid grid-cols-[2fr_1fr_1fr_auto] items-start gap-2 rounded-md border border-sidebar-border/70 p-3 dark:border-sidebar-border"
                >
                    <div class="grid gap-2">
                        <Label :for="`item-product-${item.key}`">Product</Label>
                        <Select
                            :model-value="String(item.product_id)"
                            :name="`items[${itemIndex}][product_id]`"
                            @update:model-value="(value) => onProductChange(itemIndex, String(value))"
                        >
                            <SelectTrigger :id="`item-product-${item.key}`" class="w-full">
                                <SelectValue placeholder="Select product" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="option in products"
                                    :key="option.value"
                                    :value="String(option.value)"
                                >
                                    {{ option.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors[`items.${itemIndex}.product_id`]" />
                    </div>

                    <div class="grid gap-2">
                        <Label :for="`item-quantity-${item.key}`">Qty</Label>
                        <Input
                            :id="`item-quantity-${item.key}`"
                            v-model="item.quantity"
                            :name="`items[${itemIndex}][quantity]`"
                            type="number"
                            min="1"
                        />
                        <InputError :message="errors[`items.${itemIndex}.quantity`]" />
                    </div>

                    <div class="grid gap-2">
                        <Label :for="`item-price-${item.key}`">Unit price</Label>
                        <Input
                            :id="`item-price-${item.key}`"
                            v-model="item.unit_price"
                            :name="`items[${itemIndex}][unit_price]`"
                            type="number"
                            step="0.01"
                            min="0"
                        />
                        <InputError :message="errors[`items.${itemIndex}.unit_price`]" />
                    </div>

                    <Button
                        type="button"
                        variant="destructive"
                        size="sm"
                        class="mt-7"
                        :disabled="items.length === 1"
                        @click="removeItem(itemIndex)"
                    >
                        Remove
                    </Button>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Update order</Button>
            </div>
        </Form>
    </div>
</template>

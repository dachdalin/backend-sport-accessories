<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import RefundRequestController from '@/actions/App/Http/Controllers/Backend/RefundRequestController';
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
import { create, index } from '@/routes/refund-requests';

interface SelectOption {
    value: number | string;
    label: string;
}

interface OrderOption extends SelectOption {
    items: SelectOption[];
}

const props = defineProps<{
    orders: OrderOption[];
    statuses: SelectOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Refund requests',
                href: index(),
            },
            {
                title: 'Add refund request',
                href: create(),
            },
        ],
    },
});

const selectedOrderId = ref('');

const itemOptions = computed(() => {
    const order = props.orders.find((option) => String(option.value) === selectedOrderId.value);

    return order?.items ?? [];
});
</script>

<template>
    <Head title="Add refund request" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add refund request"
            description="Log a refund request against an order"
        />

        <Form
            v-bind="RefundRequestController.store.form()"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="order_id">Order</Label>
                <Select
                    :model-value="selectedOrderId"
                    name="order_id"
                    @update:model-value="(value) => (selectedOrderId = String(value))"
                >
                    <SelectTrigger id="order_id" class="w-full">
                        <SelectValue placeholder="Select order" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="option in orders"
                            :key="option.value"
                            :value="String(option.value)"
                        >
                            {{ option.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors.order_id" />
            </div>

            <div class="grid gap-2">
                <Label for="order_item_id">Order item (optional)</Label>
                <Select name="order_item_id" :disabled="itemOptions.length === 0">
                    <SelectTrigger id="order_item_id" class="w-full">
                        <SelectValue placeholder="Whole order" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="option in itemOptions"
                            :key="option.value"
                            :value="String(option.value)"
                        >
                            {{ option.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors.order_item_id" />
            </div>

            <div class="grid gap-2">
                <Label for="amount">Refund amount</Label>
                <Input
                    id="amount"
                    name="amount"
                    type="number"
                    step="0.01"
                    min="0"
                    required
                    placeholder="0.00"
                />
                <InputError :message="errors.amount" />
            </div>

            <div class="grid gap-2">
                <Label for="reason">Reason</Label>
                <Textarea
                    id="reason"
                    name="reason"
                    required
                    placeholder="Why the customer is requesting a refund"
                    rows="3"
                />
                <InputError :message="errors.reason" />
            </div>

            <div class="grid gap-2">
                <Label for="status">Status</Label>
                <Select name="status" default-value="pending">
                    <SelectTrigger id="status" class="w-full">
                        <SelectValue placeholder="Select status" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="option in statuses"
                            :key="option.value"
                            :value="String(option.value)"
                        >
                            {{ option.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors.status" />
            </div>

            <div class="grid gap-2">
                <Label for="admin_note">Admin note</Label>
                <Textarea
                    id="admin_note"
                    name="admin_note"
                    placeholder="Internal note about this decision"
                    rows="2"
                />
                <InputError :message="errors.admin_note" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create refund request</Button>
            </div>
        </Form>
    </div>
</template>

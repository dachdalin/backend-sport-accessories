<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import {
    BanknoteIcon,
    CircleCheckIcon,
    CircleXIcon,
    ClockIcon,
    FileTextIcon,
    Receipt,
    SendIcon,
    ShieldCheck,
    Undo2,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import RefundRequestController from '@/actions/App/Http/Controllers/Backend/RefundRequestController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
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

type StatusValue = 'pending' | 'approved' | 'rejected';

const STATUS_META: Record<
    StatusValue,
    {
        label: string;
        icon: typeof ClockIcon;
        badge: 'outline' | 'default' | 'destructive';
    }
> = {
    pending: { label: 'Pending', icon: ClockIcon, badge: 'outline' },
    approved: { label: 'Approved', icon: CircleCheckIcon, badge: 'default' },
    rejected: { label: 'Rejected', icon: CircleXIcon, badge: 'destructive' },
};

const selectedOrderId = ref('');
const selectedOrderItemId = ref('');
const amount = ref('');
const status = ref<StatusValue>('pending');

const selectedOrder = computed(() =>
    props.orders.find(
        (option) => String(option.value) === selectedOrderId.value,
    ),
);

const itemOptions = computed(() => selectedOrder.value?.items ?? []);

const selectedItemLabel = computed(() => {
    if (!selectedOrder.value) {
        return null;
    }

    const item = itemOptions.value.find(
        (option) => String(option.value) === selectedOrderItemId.value,
    );

    return item?.label ?? 'Whole order';
});

function onOrderChange(value: string) {
    selectedOrderId.value = value;
    selectedOrderItemId.value = '';
}
</script>

<template>
    <Head title="Add refund request" />

    <div class="flex flex-col gap-6">
        <Heading
            title="Add refund request"
            description="Log a refund decision against an order"
        />

        <Form
            v-bind="RefundRequestController.store.form()"
            v-slot="{ errors, processing }"
        >
            <div
                class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_320px]"
            >
                <div class="flex flex-col gap-6">
                    <Card>
                        <CardHeader>
                            <div class="flex items-center gap-2.5">
                                <Receipt
                                    class="size-4.5 text-muted-foreground"
                                    aria-hidden="true"
                                />
                                <CardTitle>Order</CardTitle>
                            </div>
                            <CardDescription
                                >Find the order this refund applies to, and the
                                item if it's partial.</CardDescription
                            >
                        </CardHeader>
                        <CardContent class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="order_id">Order</Label>
                                <Select
                                    :model-value="selectedOrderId"
                                    name="order_id"
                                    @update:model-value="
                                        (value) => onOrderChange(String(value))
                                    "
                                >
                                    <SelectTrigger id="order_id" class="w-full">
                                        <SelectValue
                                            placeholder="Select order"
                                        />
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
                                <Label for="order_item_id">Order item</Label>
                                <Select
                                    :model-value="selectedOrderItemId"
                                    name="order_item_id"
                                    :disabled="itemOptions.length === 0"
                                    @update:model-value="
                                        (value) =>
                                            (selectedOrderItemId =
                                                String(value))
                                    "
                                >
                                    <SelectTrigger
                                        id="order_item_id"
                                        class="w-full"
                                    >
                                        <SelectValue
                                            placeholder="Whole order"
                                        />
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
                                <p class="text-xs text-muted-foreground">
                                    Leave blank to refund the whole order.
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <div class="flex items-center gap-2.5">
                                <Undo2
                                    class="size-4.5 text-muted-foreground"
                                    aria-hidden="true"
                                />
                                <CardTitle>Refund</CardTitle>
                            </div>
                            <CardDescription
                                >How much goes back to the customer, and
                                why.</CardDescription
                            >
                        </CardHeader>
                        <CardContent class="flex flex-col gap-4">
                            <div class="grid gap-2 sm:max-w-52">
                                <Label for="amount">Refund amount</Label>
                                <div class="relative">
                                    <BanknoteIcon
                                        class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                                        aria-hidden="true"
                                    />
                                    <span
                                        class="pointer-events-none absolute top-1/2 left-8 -translate-y-1/2 text-muted-foreground"
                                        >$</span
                                    >
                                    <Input
                                        id="amount"
                                        v-model="amount"
                                        name="amount"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        required
                                        placeholder="0.00"
                                        class="pl-11"
                                    />
                                </div>
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
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <div class="flex items-center gap-2.5">
                                <ShieldCheck
                                    class="size-4.5 text-muted-foreground"
                                    aria-hidden="true"
                                />
                                <CardTitle>Decision</CardTitle>
                            </div>
                            <CardDescription
                                >Where this refund request currently
                                stands.</CardDescription
                            >
                        </CardHeader>
                        <CardContent class="flex flex-col gap-4">
                            <div class="grid gap-2">
                                <Label>Status</Label>
                                <div
                                    class="grid grid-cols-1 gap-2 sm:grid-cols-3"
                                >
                                    <label
                                        v-for="option in statuses"
                                        :key="option.value"
                                        :for="`status-${option.value}`"
                                        class="flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-input px-3 py-2.5 text-sm font-medium has-[:checked]:border-primary/50 has-[:checked]:bg-primary/5 has-[:checked]:text-primary has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-ring has-[:focus-visible]:ring-offset-2"
                                    >
                                        <input
                                            :id="`status-${option.value}`"
                                            v-model="status"
                                            type="radio"
                                            name="status"
                                            :value="option.value"
                                            class="sr-only"
                                        />
                                        <component
                                            :is="
                                                STATUS_META[
                                                    option.value as StatusValue
                                                ].icon
                                            "
                                            class="size-4"
                                            aria-hidden="true"
                                        />
                                        {{ option.label }}
                                    </label>
                                </div>
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
                        </CardContent>
                    </Card>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <Button class="w-full sm:w-auto" :disabled="processing">
                            <Spinner v-if="processing" />
                            <SendIcon
                                v-else
                                class="size-4"
                                aria-hidden="true"
                            />
                            Create refund request
                        </Button>
                        <Button
                            class="w-full sm:w-auto"
                            variant="outline"
                            as-child
                        >
                            <Link :href="index()">Cancel</Link>
                        </Button>
                    </div>
                </div>

                <Card class="lg:sticky lg:top-6">
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <FileTextIcon
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Refund summary</CardTitle>
                        </div>
                        <CardDescription
                            >What gets logged when you submit.</CardDescription
                        >
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4 text-sm">
                        <div class="flex flex-col gap-1">
                            <span class="text-xs text-muted-foreground"
                                >Order</span
                            >
                            <span
                                class="font-medium"
                                :class="{
                                    'text-muted-foreground italic':
                                        !selectedOrder,
                                }"
                            >
                                {{
                                    selectedOrder?.label ??
                                    'No order selected yet'
                                }}
                            </span>
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="text-xs text-muted-foreground"
                                >Covers</span
                            >
                            <span
                                class="font-medium"
                                :class="{
                                    'text-muted-foreground italic':
                                        !selectedOrder,
                                }"
                            >
                                {{
                                    selectedItemLabel ?? 'Select an order first'
                                }}
                            </span>
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="text-xs text-muted-foreground"
                                >Amount</span
                            >
                            <span
                                class="text-lg font-semibold"
                                :class="{
                                    'text-muted-foreground italic': !amount,
                                }"
                            >
                                {{ amount ? `$${amount}` : 'Not set' }}
                            </span>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <span class="text-xs text-muted-foreground"
                                >Status</span
                            >
                            <Badge
                                :variant="STATUS_META[status].badge"
                                class="w-fit gap-1"
                            >
                                <component
                                    :is="STATUS_META[status].icon"
                                    class="size-3.5"
                                    aria-hidden="true"
                                />
                                {{ STATUS_META[status].label }}
                            </Badge>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </Form>
    </div>
</template>

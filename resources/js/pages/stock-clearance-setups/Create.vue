<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import {
    CalendarRangeIcon,
    EyeIcon,
    PackageIcon,
    PercentIcon,
    PlusIcon,
    SendIcon,
    Trash2Icon,
} from '@lucide/vue';
import { ref } from 'vue';
import StockClearanceSetupController from '@/actions/App/Http/Controllers/Backend/StockClearanceSetupController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
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
import { create, index } from '@/routes/stock-clearance-setups';

interface SelectOption {
    value: number | string;
    label: string;
}

defineProps<{
    products: SelectOption[];
    discountTypes: SelectOption[];
    offerActiveTimes: SelectOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Stock clearance',
                href: index(),
            },
            {
                title: 'Add setup',
                href: create(),
            },
        ],
    },
});

const offerActiveTime = ref('always');

let nextKey = 0;

function newItem() {
    return {
        key: nextKey++,
        product_id: '',
        discount_type: 'percent',
        discount_amount: '',
    };
}

const items = ref([newItem()]);

function addItem() {
    items.value.push(newItem());
}

function removeItem(index: number) {
    items.value.splice(index, 1);
}
</script>

<template>
    <Head title="Add stock clearance setup" />

    <div class="flex flex-col gap-6">
        <Heading
            title="Add stock clearance setup"
            description="Configure a stock clearance campaign and the products included in it"
        />

        <Form
            v-bind="StockClearanceSetupController.store.form()"
            class="flex flex-col gap-6"
            v-slot="{ errors, processing }"
        >
            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2.5">
                        <PercentIcon
                            class="size-4.5 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <CardTitle>Discount</CardTitle>
                    </div>
                    <CardDescription
                        >How much shoppers save on every item in this
                        campaign.</CardDescription
                    >
                </CardHeader>
                <CardContent class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="discount_type">Discount type</Label>
                        <Select name="discount_type" default-value="percent">
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
                        <Label for="discount_amount">Discount amount</Label>
                        <Input
                            id="discount_amount"
                            name="discount_amount"
                            type="number"
                            step="0.01"
                            min="0"
                            required
                            placeholder="10.00"
                        />
                        <InputError :message="errors.discount_amount" />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2.5">
                        <CalendarRangeIcon
                            class="size-4.5 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <CardTitle>Duration & availability</CardTitle>
                    </div>
                    <CardDescription
                        >When the campaign runs, and the hours of day it's
                        shown.</CardDescription
                    >
                </CardHeader>
                <CardContent class="flex flex-col gap-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="duration_start_date"
                                >Duration start</Label
                            >
                            <Input
                                id="duration_start_date"
                                name="duration_start_date"
                                type="date"
                                required
                            />
                            <InputError :message="errors.duration_start_date" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="duration_end_date">Duration end</Label>
                            <Input
                                id="duration_end_date"
                                name="duration_end_date"
                                type="date"
                                required
                            />
                            <InputError :message="errors.duration_end_date" />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="offer_active_time">Offer active time</Label>
                        <Select
                            name="offer_active_time"
                            :model-value="offerActiveTime"
                            @update:model-value="
                                (value) => (offerActiveTime = String(value))
                            "
                        >
                            <SelectTrigger
                                id="offer_active_time"
                                class="w-full"
                            >
                                <SelectValue placeholder="Select option" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="option in offerActiveTimes"
                                    :key="option.value"
                                    :value="String(option.value)"
                                >
                                    {{ option.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.offer_active_time" />
                    </div>

                    <div
                        v-if="offerActiveTime === 'specific_time'"
                        class="grid gap-4 sm:grid-cols-2"
                    >
                        <div class="grid gap-2">
                            <Label for="offer_active_range_start"
                                >Active from</Label
                            >
                            <Input
                                id="offer_active_range_start"
                                name="offer_active_range_start"
                                type="time"
                            />
                            <InputError
                                :message="errors.offer_active_range_start"
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="offer_active_range_end"
                                >Active until</Label
                            >
                            <Input
                                id="offer_active_range_end"
                                name="offer_active_range_end"
                                type="time"
                            />
                            <InputError
                                :message="errors.offer_active_range_end"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <div class="flex items-center gap-2.5">
                        <EyeIcon
                            class="size-4.5 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <CardTitle>Visibility</CardTitle>
                    </div>
                    <CardDescription
                        >Where this campaign appears, and whether it's live
                        right away.</CardDescription
                    >
                </CardHeader>
                <CardContent class="grid gap-3 sm:grid-cols-2">
                    <label
                        for="show_in_homepage"
                        class="flex items-center gap-2.5 rounded-lg border border-input px-3 py-2.5 has-[:checked]:border-primary/50 has-[:checked]:bg-primary/5"
                    >
                        <Checkbox
                            id="show_in_homepage"
                            name="show_in_homepage"
                            :default-value="true"
                        />
                        <span class="grid gap-0.5">
                            <span class="text-sm font-medium"
                                >Show on homepage</span
                            >
                            <span class="text-xs text-muted-foreground"
                                >Feature this campaign on the storefront
                                homepage.</span
                            >
                        </span>
                    </label>

                    <label
                        for="show_in_homepage_once"
                        class="flex items-center gap-2.5 rounded-lg border border-input px-3 py-2.5 has-[:checked]:border-primary/50 has-[:checked]:bg-primary/5"
                    >
                        <Checkbox
                            id="show_in_homepage_once"
                            name="show_in_homepage_once"
                        />
                        <span class="grid gap-0.5">
                            <span class="text-sm font-medium"
                                >Show on homepage once</span
                            >
                            <span class="text-xs text-muted-foreground"
                                >Dismiss from the homepage after a shopper's
                                first visit.</span
                            >
                        </span>
                    </label>

                    <label
                        for="show_in_shop"
                        class="flex items-center gap-2.5 rounded-lg border border-input px-3 py-2.5 has-[:checked]:border-primary/50 has-[:checked]:bg-primary/5"
                    >
                        <Checkbox
                            id="show_in_shop"
                            name="show_in_shop"
                            :default-value="true"
                        />
                        <span class="grid gap-0.5">
                            <span class="text-sm font-medium"
                                >Show in shop</span
                            >
                            <span class="text-xs text-muted-foreground"
                                >List the discounted products in the shop
                                catalog.</span
                            >
                        </span>
                    </label>

                    <label
                        for="is_active"
                        class="flex items-center gap-2.5 rounded-lg border border-input px-3 py-2.5 has-[:checked]:border-primary/50 has-[:checked]:bg-primary/5"
                    >
                        <Checkbox
                            id="is_active"
                            name="is_active"
                            :default-value="true"
                        />
                        <span class="grid gap-0.5">
                            <span class="text-sm font-medium">Active</span>
                            <span class="text-xs text-muted-foreground"
                                >Turn the campaign on as soon as it's
                                saved.</span
                            >
                        </span>
                    </label>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <PackageIcon
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Clearance products</CardTitle>
                        </div>
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="addItem"
                        >
                            <PlusIcon class="size-4" aria-hidden="true" />
                            Add product
                        </Button>
                    </div>
                    <CardDescription
                        >Each product can carry its own discount, separate from
                        the campaign default.</CardDescription
                    >
                </CardHeader>
                <CardContent class="flex flex-col gap-3">
                    <InputError :message="errors.items" />

                    <div
                        v-for="(item, itemIndex) in items"
                        :key="item.key"
                        class="grid grid-cols-1 items-start gap-3 rounded-lg border border-sidebar-border/70 p-3 sm:grid-cols-[2fr_1fr_1fr_auto] dark:border-sidebar-border"
                    >
                        <div class="grid gap-2">
                            <Label :for="`item-product-${item.key}`"
                                >Product</Label
                            >
                            <Select
                                :model-value="String(item.product_id)"
                                :name="`items[${itemIndex}][product_id]`"
                                @update:model-value="
                                    (value) => (item.product_id = String(value))
                                "
                            >
                                <SelectTrigger
                                    :id="`item-product-${item.key}`"
                                    class="w-full"
                                >
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
                            <InputError
                                :message="
                                    errors[`items.${itemIndex}.product_id`]
                                "
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label :for="`item-discount-type-${item.key}`"
                                >Discount type</Label
                            >
                            <Select
                                :model-value="item.discount_type"
                                :name="`items[${itemIndex}][discount_type]`"
                                @update:model-value="
                                    (value) =>
                                        (item.discount_type = String(value))
                                "
                            >
                                <SelectTrigger
                                    :id="`item-discount-type-${item.key}`"
                                    class="w-full"
                                >
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
                            <InputError
                                :message="
                                    errors[`items.${itemIndex}.discount_type`]
                                "
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label :for="`item-discount-amount-${item.key}`"
                                >Discount</Label
                            >
                            <Input
                                :id="`item-discount-amount-${item.key}`"
                                v-model="item.discount_amount"
                                :name="`items[${itemIndex}][discount_amount]`"
                                type="number"
                                step="0.01"
                                min="0"
                            />
                            <InputError
                                :message="
                                    errors[`items.${itemIndex}.discount_amount`]
                                "
                            />
                        </div>

                        <Button
                            type="button"
                            variant="destructive"
                            size="sm"
                            class="w-full sm:mt-7 sm:w-auto"
                            :disabled="items.length === 1"
                            @click="removeItem(itemIndex)"
                        >
                            <Trash2Icon class="size-4" aria-hidden="true" />
                            <span class="sm:hidden">Remove product</span>
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <div class="flex flex-col gap-3 sm:flex-row">
                <Button class="w-full sm:w-auto" :disabled="processing">
                    <Spinner v-if="processing" />
                    <SendIcon v-else class="size-4" aria-hidden="true" />
                    Create setup
                </Button>
                <Button class="w-full sm:w-auto" variant="outline" as-child>
                    <Link :href="index()">Cancel</Link>
                </Button>
            </div>
        </Form>
    </div>
</template>

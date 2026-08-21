<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import StockClearanceSetupController from '@/actions/App/Http/Controllers/Backend/StockClearanceSetupController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
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
    return { key: nextKey++, product_id: '', discount_type: 'percent', discount_amount: '' };
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

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add stock clearance setup"
            description="Configure a stock clearance campaign and the products included in it"
        />

        <Form
            v-bind="StockClearanceSetupController.store.form()"
            class="max-w-2xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid grid-cols-2 gap-4">
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
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="duration_start_date">Duration start</Label>
                    <Input id="duration_start_date" name="duration_start_date" type="date" required />
                    <InputError :message="errors.duration_start_date" />
                </div>

                <div class="grid gap-2">
                    <Label for="duration_end_date">Duration end</Label>
                    <Input id="duration_end_date" name="duration_end_date" type="date" required />
                    <InputError :message="errors.duration_end_date" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="offer_active_time">Offer active time</Label>
                <Select
                    name="offer_active_time"
                    :model-value="offerActiveTime"
                    @update:model-value="(value) => (offerActiveTime = String(value))"
                >
                    <SelectTrigger id="offer_active_time" class="w-full">
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

            <div v-if="offerActiveTime === 'specific_time'" class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="offer_active_range_start">Active from</Label>
                    <Input id="offer_active_range_start" name="offer_active_range_start" type="time" />
                    <InputError :message="errors.offer_active_range_start" />
                </div>

                <div class="grid gap-2">
                    <Label for="offer_active_range_end">Active until</Label>
                    <Input id="offer_active_range_end" name="offer_active_range_end" type="time" />
                    <InputError :message="errors.offer_active_range_end" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex items-center gap-2">
                    <Checkbox id="show_in_homepage" name="show_in_homepage" :default-value="true" />
                    <Label for="show_in_homepage">Show on homepage</Label>
                    <InputError :message="errors.show_in_homepage" />
                </div>

                <div class="flex items-center gap-2">
                    <Checkbox id="show_in_homepage_once" name="show_in_homepage_once" />
                    <Label for="show_in_homepage_once">Show on homepage once</Label>
                    <InputError :message="errors.show_in_homepage_once" />
                </div>

                <div class="flex items-center gap-2">
                    <Checkbox id="show_in_shop" name="show_in_shop" :default-value="true" />
                    <Label for="show_in_shop">Show in shop</Label>
                    <InputError :message="errors.show_in_shop" />
                </div>

                <div class="flex items-center gap-2">
                    <Checkbox id="is_active" name="is_active" :default-value="true" />
                    <Label for="is_active">Active</Label>
                    <InputError :message="errors.is_active" />
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <Label>Clearance products</Label>
                    <Button type="button" variant="outline" size="sm" @click="addItem">
                        Add product
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
                            @update:model-value="(value) => (item.product_id = String(value))"
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
                        <Label :for="`item-discount-type-${item.key}`">Discount type</Label>
                        <Select
                            :model-value="item.discount_type"
                            :name="`items[${itemIndex}][discount_type]`"
                            @update:model-value="(value) => (item.discount_type = String(value))"
                        >
                            <SelectTrigger :id="`item-discount-type-${item.key}`" class="w-full">
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
                        <InputError :message="errors[`items.${itemIndex}.discount_type`]" />
                    </div>

                    <div class="grid gap-2">
                        <Label :for="`item-discount-amount-${item.key}`">Discount</Label>
                        <Input
                            :id="`item-discount-amount-${item.key}`"
                            v-model="item.discount_amount"
                            :name="`items[${itemIndex}][discount_amount]`"
                            type="number"
                            step="0.01"
                            min="0"
                        />
                        <InputError :message="errors[`items.${itemIndex}.discount_amount`]" />
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
                <Button :disabled="processing">Create setup</Button>
            </div>
        </Form>
    </div>
</template>

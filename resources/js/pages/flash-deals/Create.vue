<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import FlashDealController from '@/actions/App/Http/Controllers/Backend/FlashDealController';
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
import { create, index } from '@/routes/flash-deals';

interface SelectOption {
    value: number | string;
    label: string;
}

defineProps<{
    products: SelectOption[];
    discountTypes: SelectOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Flash deals',
                href: index(),
            },
            {
                title: 'Add flash deal',
                href: create(),
            },
        ],
    },
});

let nextKey = 0;

function newItem() {
    return { key: nextKey++, product_id: '', discount: '', discount_type: 'percent' };
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
    <Head title="Add flash deal" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add flash deal"
            description="Schedule a time-limited discount on selected products"
        />

        <Form
            v-bind="FlashDealController.store.form()"
            class="max-w-2xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="title">Title</Label>
                <Input
                    id="title"
                    name="title"
                    required
                    autofocus
                    placeholder="Weekend Sports Sale"
                />
                <InputError :message="errors.title" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="start_date">Start date</Label>
                    <Input
                        id="start_date"
                        name="start_date"
                        type="date"
                        required
                    />
                    <InputError :message="errors.start_date" />
                </div>

                <div class="grid gap-2">
                    <Label for="end_date">End date</Label>
                    <Input
                        id="end_date"
                        name="end_date"
                        type="date"
                        required
                    />
                    <InputError :message="errors.end_date" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="background_color">Background color</Label>
                    <Input
                        id="background_color"
                        name="background_color"
                        placeholder="#ff0000"
                    />
                    <InputError :message="errors.background_color" />
                </div>

                <div class="grid gap-2">
                    <Label for="text_color">Text color</Label>
                    <Input
                        id="text_color"
                        name="text_color"
                        placeholder="#ffffff"
                    />
                    <InputError :message="errors.text_color" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="banner">Banner</Label>
                <Input id="banner" name="banner" type="file" accept="image/*" />
                <InputError :message="errors.banner" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="featured" name="featured" />
                <Label for="featured">Featured</Label>
                <InputError :message="errors.featured" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" :default-value="true" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <Label>Deal products</Label>
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
                            v-model="item.product_id"
                            :name="`items[${itemIndex}][product_id]`"
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
                        <Label :for="`item-discount-${item.key}`">Discount</Label>
                        <Input
                            :id="`item-discount-${item.key}`"
                            v-model="item.discount"
                            :name="`items[${itemIndex}][discount]`"
                            type="number"
                            step="0.01"
                            min="0"
                        />
                        <InputError :message="errors[`items.${itemIndex}.discount`]" />
                    </div>

                    <div class="grid gap-2">
                        <Label :for="`item-discount-type-${item.key}`">Type</Label>
                        <Select
                            v-model="item.discount_type"
                            :name="`items[${itemIndex}][discount_type]`"
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
                <Button :disabled="processing">Create flash deal</Button>
            </div>
        </Form>
    </div>
</template>

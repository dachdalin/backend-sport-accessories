<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import DealOfTheDayController from '@/actions/App/Http/Controllers/Backend/DealOfTheDayController';
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
import { create, index } from '@/routes/deal-of-the-days';

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
                title: 'Deal of the day',
                href: index(),
            },
            {
                title: 'Add deal',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add deal" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add deal"
            description="Highlight a discounted product for a limited time"
        />

        <Form
            v-bind="DealOfTheDayController.store.form()"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="title">Title</Label>
                <Input
                    id="title"
                    name="title"
                    required
                    autofocus
                    placeholder="Weekend flash offer"
                />
                <InputError :message="errors.title" />
            </div>

            <div class="grid gap-2">
                <Label for="product_id">Product</Label>
                <Select name="product_id">
                    <SelectTrigger id="product_id" class="w-full">
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
                <InputError :message="errors.product_id" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="discount">Discount</Label>
                    <Input
                        id="discount"
                        name="discount"
                        type="number"
                        step="0.01"
                        min="0"
                        placeholder="0.00"
                    />
                    <InputError :message="errors.discount" />
                </div>

                <div class="grid gap-2">
                    <Label for="discount_type">Discount type</Label>
                    <Select name="discount_type" default-value="amount">
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
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create deal</Button>
            </div>
        </Form>
    </div>
</template>

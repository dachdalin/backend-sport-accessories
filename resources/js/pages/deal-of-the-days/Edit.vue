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
import { edit, index } from '@/routes/deal-of-the-days';

interface SelectOption {
    value: number | string;
    label: string;
}

type Deal = {
    id: number;
    title: string;
    product_id: number;
    discount: string;
    discount_type: string;
    status: boolean;
};

defineProps<{
    deal: Deal;
    products: SelectOption[];
    discountTypes: SelectOption[];
}>();

defineOptions({
    layout: (pageProps: { deal: Deal }) => ({
        breadcrumbs: [
            {
                title: 'Deal of the day',
                href: index(),
            },
            {
                title: 'Edit deal',
                href: edit(pageProps.deal),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit deal" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit deal"
            :description="`Update the details for ${deal.title}`"
        />

        <Form
            v-bind="DealOfTheDayController.update.form(deal)"
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
                    :default-value="deal.title"
                />
                <InputError :message="errors.title" />
            </div>

            <div class="grid gap-2">
                <Label for="product_id">Product</Label>
                <Select name="product_id" :default-value="String(deal.product_id)">
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
                        :default-value="deal.discount"
                    />
                    <InputError :message="errors.discount" />
                </div>

                <div class="grid gap-2">
                    <Label for="discount_type">Discount type</Label>
                    <Select name="discount_type" :default-value="deal.discount_type">
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
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="deal.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Update deal</Button>
            </div>
        </Form>
    </div>
</template>

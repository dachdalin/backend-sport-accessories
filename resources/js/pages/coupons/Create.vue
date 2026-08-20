<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import CouponController from '@/actions/App/Http/Controllers/Backend/CouponController';
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
import { create, index } from '@/routes/coupons';

interface TypeOption {
    value: string;
    label: string;
}

defineProps<{
    types: TypeOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Coupons',
                href: index(),
            },
            {
                title: 'Add coupon',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add coupon" />

    <div class="flex flex-col space-y-6">
        <Heading title="Add coupon" description="Create a new discount coupon" />

        <Form
            v-bind="CouponController.store.form()"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="code">Code</Label>
                <Input
                    id="code"
                    name="code"
                    required
                    autofocus
                    placeholder="SAVE20"
                />
                <InputError :message="errors.code" />
            </div>

            <div class="grid gap-2">
                <Label for="type">Type</Label>
                <Select name="type" default-value="fixed">
                    <SelectTrigger id="type" class="w-full">
                        <SelectValue placeholder="Select a discount type" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="option in types"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors.type" />
            </div>

            <div class="grid gap-2">
                <Label for="value">Value</Label>
                <Input
                    id="value"
                    name="value"
                    type="number"
                    step="0.01"
                    min="0.01"
                    required
                    placeholder="20"
                />
                <InputError :message="errors.value" />
            </div>

            <div class="grid gap-2">
                <Label for="min_order_amount">Minimum order amount</Label>
                <Input
                    id="min_order_amount"
                    name="min_order_amount"
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="Optional"
                />
                <InputError :message="errors.min_order_amount" />
            </div>

            <div class="grid gap-2">
                <Label for="usage_limit">Usage limit</Label>
                <Input
                    id="usage_limit"
                    name="usage_limit"
                    type="number"
                    min="1"
                    placeholder="Optional"
                />
                <InputError :message="errors.usage_limit" />
            </div>

            <div class="grid gap-2">
                <Label for="expires_at">Expires at</Label>
                <Input id="expires_at" name="expires_at" type="date" />
                <InputError :message="errors.expires_at" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" :default-value="true" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create coupon</Button>
            </div>
        </Form>
    </div>
</template>

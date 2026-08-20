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
import { edit, index } from '@/routes/coupons';

interface TypeOption {
    value: string;
    label: string;
}

type Coupon = {
    id: number;
    code: string;
    type: string;
    value: string;
    min_order_amount: string | null;
    usage_limit: number | null;
    expires_at: string | null;
    status: boolean;
};

const props = defineProps<{
    coupon: Coupon;
    types: TypeOption[];
}>();

defineOptions({
    layout: (pageProps: { coupon: Coupon }) => ({
        breadcrumbs: [
            {
                title: 'Coupons',
                href: index(),
            },
            {
                title: 'Edit coupon',
                href: edit(pageProps.coupon),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit coupon" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit coupon"
            :description="`Update the details for ${props.coupon.code}`"
        />

        <Form
            v-bind="CouponController.update.form(props.coupon)"
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
                    :default-value="props.coupon.code"
                />
                <InputError :message="errors.code" />
            </div>

            <div class="grid gap-2">
                <Label for="type">Type</Label>
                <Select name="type" :default-value="props.coupon.type">
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
                    :default-value="props.coupon.value"
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
                    :default-value="props.coupon.min_order_amount ?? ''"
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
                    :default-value="props.coupon.usage_limit ?? ''"
                    placeholder="Optional"
                />
                <InputError :message="errors.usage_limit" />
            </div>

            <div class="grid gap-2">
                <Label for="expires_at">Expires at</Label>
                <Input
                    id="expires_at"
                    name="expires_at"
                    type="date"
                    :default-value="props.coupon.expires_at ?? ''"
                />
                <InputError :message="errors.expires_at" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="props.coupon.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save coupon</Button>
            </div>
        </Form>
    </div>
</template>

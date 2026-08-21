<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import ReviewController from '@/actions/App/Http/Controllers/Backend/ReviewController';
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
import { edit, index } from '@/routes/reviews';

interface SelectOption {
    value: number | string;
    label: string;
}

type Review = {
    id: number;
    product_id: number;
    customer_name: string;
    customer_email: string | null;
    rating: number;
    comment: string;
    admin_reply: string | null;
    status: string;
};

defineProps<{
    review: Review;
    products: SelectOption[];
    statuses: SelectOption[];
}>();

defineOptions({
    layout: (pageProps: { review: Review }) => ({
        breadcrumbs: [
            {
                title: 'Reviews',
                href: index(),
            },
            {
                title: 'Edit review',
                href: edit(pageProps.review),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit review" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit review"
            :description="`Update the review from ${review.customer_name}`"
        />

        <Form
            v-bind="ReviewController.update.form(review)"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="product_id">Product</Label>
                <Select
                    name="product_id"
                    :default-value="String(review.product_id)"
                >
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
                    <Label for="customer_name">Customer name</Label>
                    <Input
                        id="customer_name"
                        name="customer_name"
                        required
                        autofocus
                        :default-value="review.customer_name"
                    />
                    <InputError :message="errors.customer_name" />
                </div>

                <div class="grid gap-2">
                    <Label for="customer_email">Customer email</Label>
                    <Input
                        id="customer_email"
                        name="customer_email"
                        type="email"
                        :default-value="review.customer_email ?? ''"
                    />
                    <InputError :message="errors.customer_email" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="rating">Rating (1-5)</Label>
                <Input
                    id="rating"
                    name="rating"
                    type="number"
                    min="1"
                    max="5"
                    required
                    :default-value="String(review.rating)"
                />
                <InputError :message="errors.rating" />
            </div>

            <div class="grid gap-2">
                <Label for="comment">Comment</Label>
                <Textarea
                    id="comment"
                    name="comment"
                    required
                    :default-value="review.comment"
                    rows="4"
                />
                <InputError :message="errors.comment" />
            </div>

            <div class="grid gap-2">
                <Label for="admin_reply">Admin reply</Label>
                <Textarea
                    id="admin_reply"
                    name="admin_reply"
                    :default-value="review.admin_reply ?? ''"
                    placeholder="Optional public reply from the store"
                    rows="3"
                />
                <InputError :message="errors.admin_reply" />
            </div>

            <div class="grid gap-2">
                <Label for="status">Status</Label>
                <Select name="status" :default-value="review.status">
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

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save review</Button>
            </div>
        </Form>
    </div>
</template>

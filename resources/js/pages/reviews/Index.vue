<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import ReviewController from '@/actions/App/Http/Controllers/Backend/ReviewController';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { create, edit, index } from '@/routes/reviews';

type Review = {
    id: number;
    customer_name: string;
    rating: number;
    comment: string;
    status: string;
    product: { id: number; name: string };
};

defineProps<{
    reviews: Review[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Reviews',
                href: index(),
            },
        ],
    },
});

const statusVariant: Record<string, 'default' | 'secondary' | 'destructive'> = {
    pending: 'secondary',
    approved: 'default',
    rejected: 'destructive',
};
</script>

<template>
    <Head title="Reviews" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Reviews"
                description="Moderate customer reviews left on products"
            />
            <Button as-child>
                <Link :href="create()">Add review</Link>
            </Button>
        </div>

        <div
            class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
        >
            <table class="w-full text-left text-sm">
                <thead
                    class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border"
                >
                    <tr>
                        <th class="p-3 font-medium">Product</th>
                        <th class="p-3 font-medium">Customer</th>
                        <th class="p-3 font-medium">Rating</th>
                        <th class="p-3 font-medium">Comment</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="review in reviews"
                        :key="review.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">{{ review.product.name }}</td>
                        <td class="p-3">{{ review.customer_name }}</td>
                        <td class="p-3">{{ review.rating }} / 5</td>
                        <td class="p-3 max-w-xs truncate">{{ review.comment }}</td>
                        <td class="p-3">
                            <Badge :variant="statusVariant[review.status] ?? 'secondary'">
                                {{ review.status }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(review)">Edit</Link>
                                </Button>

                                <Dialog>
                                    <DialogTrigger as-child>
                                        <Button variant="destructive" size="sm"
                                            >Delete</Button
                                        >
                                    </DialogTrigger>
                                    <DialogContent>
                                        <Form
                                            v-bind="
                                                ReviewController.destroy.form(
                                                    review,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete review by "{{
                                                        review.customer_name
                                                    }}"?</DialogTitle
                                                >
                                            </DialogHeader>

                                            <DialogFooter class="mt-6 gap-2">
                                                <DialogClose as-child>
                                                    <Button variant="secondary"
                                                        >Cancel</Button
                                                    >
                                                </DialogClose>
                                                <Button
                                                    type="submit"
                                                    variant="destructive"
                                                    :disabled="processing"
                                                >
                                                    Delete
                                                </Button>
                                            </DialogFooter>
                                        </Form>
                                    </DialogContent>
                                </Dialog>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="reviews.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="6"
                        >
                            No reviews yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import DealOfTheDayController from '@/actions/App/Http/Controllers/Backend/DealOfTheDayController';
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
import { create, edit, index } from '@/routes/deal-of-the-days';

type Deal = {
    id: number;
    title: string;
    discount: string;
    discount_type: 'percent' | 'amount';
    status: boolean;
    product: { id: number; name: string } | null;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type Paginated<T> = {
    data: T[];
    links: PaginationLink[];
};

defineProps<{
    deals: Paginated<Deal>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Deal of the day',
                href: index(),
            },
        ],
    },
});

function formatDiscount(deal: Deal) {
    return deal.discount_type === 'percent' ? `${deal.discount}%` : `$${deal.discount}`;
}
</script>

<template>
    <Head title="Deal of the day" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Deal of the day"
                description="Highlight a discounted product for a limited time"
            />
            <Button as-child>
                <Link :href="create()">Add deal</Link>
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
                        <th class="p-3 font-medium">Title</th>
                        <th class="p-3 font-medium">Product</th>
                        <th class="p-3 font-medium">Discount</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="deal in deals.data"
                        :key="deal.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">{{ deal.title }}</td>
                        <td class="p-3">{{ deal.product?.name ?? '—' }}</td>
                        <td class="p-3">{{ formatDiscount(deal) }}</td>
                        <td class="p-3">
                            <Badge :variant="deal.status ? 'default' : 'secondary'">
                                {{ deal.status ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(deal)">Edit</Link>
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
                                                DealOfTheDayController.destroy.form(
                                                    deal,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        deal.title
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

                    <tr v-if="deals.data.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="5"
                        >
                            No deals yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-if="deals.links.length > 3"
            class="flex flex-wrap items-center justify-center gap-1"
        >
            <template v-for="(link, index) in deals.links" :key="index">
                <span
                    v-if="!link.url"
                    class="rounded-md px-3 py-1.5 text-sm text-muted-foreground"
                    v-html="link.label"
                />
                <Link
                    v-else
                    :href="link.url"
                    preserve-scroll
                    class="rounded-md px-3 py-1.5 text-sm"
                    :class="
                        link.active
                            ? 'bg-primary text-primary-foreground'
                            : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground'
                    "
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>

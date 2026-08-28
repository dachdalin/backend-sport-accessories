<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import FlashDealController from '@/actions/App/Http/Controllers/Backend/FlashDealController';
import Heading from '@/components/Heading.vue';
import Pagination from '@/components/Pagination.vue';
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
import { create, edit, index } from '@/routes/flash-deals';

type FlashDeal = {
    id: number;
    title: string;
    start_date: string;
    end_date: string;
    status: boolean;
    featured: boolean;
    items_count: number;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type Paginated<T> = {
    data: T[];
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
    current_page: number;
    last_page: number;
};

defineProps<{
    flashDeals: Paginated<FlashDeal>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Flash deals',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Flash deals" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Flash deals"
                description="Run time-limited discounts on selected products"
            />
            <Button as-child>
                <Link :href="create()">Add flash deal</Link>
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
                        <th class="p-3 font-medium">Duration</th>
                        <th class="p-3 font-medium">Products</th>
                        <th class="p-3 font-medium">Featured</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="flashDeal in flashDeals.data"
                        :key="flashDeal.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">{{ flashDeal.title }}</td>
                        <td class="p-3">
                            {{ flashDeal.start_date }} – {{ flashDeal.end_date }}
                        </td>
                        <td class="p-3">{{ flashDeal.items_count }}</td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    flashDeal.featured ? 'default' : 'secondary'
                                "
                            >
                                {{ flashDeal.featured ? 'Featured' : 'Regular' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    flashDeal.status ? 'default' : 'secondary'
                                "
                            >
                                {{ flashDeal.status ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(flashDeal)">Edit</Link>
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
                                                FlashDealController.destroy.form(
                                                    flashDeal,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete flash deal
                                                    "{{
                                                        flashDeal.title
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

                    <tr v-if="flashDeals.data.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="6"
                        >
                            No flash deals yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :meta="flashDeals" label="flash deals" />
    </div>
</template>

<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import StockClearanceSetupController from '@/actions/App/Http/Controllers/Backend/StockClearanceSetupController';
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
import { create, edit, index } from '@/routes/stock-clearance-setups';

type StockClearanceSetup = {
    id: number;
    discount_type: string;
    discount_amount: string;
    is_active: boolean;
    show_in_homepage: boolean;
    show_in_shop: boolean;
    duration_start_date: string;
    duration_end_date: string;
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
    stockClearanceSetups: Paginated<StockClearanceSetup>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Stock clearance',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Stock clearance" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Stock clearance"
                description="Manage stock clearance campaigns and the products included in each"
            />
            <Button as-child>
                <Link :href="create()">Add setup</Link>
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
                        <th class="p-3 font-medium">Discount</th>
                        <th class="p-3 font-medium">Products</th>
                        <th class="p-3 font-medium">Duration</th>
                        <th class="p-3 font-medium">Visibility</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="setup in stockClearanceSetups.data"
                        :key="setup.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">
                            {{ setup.discount_amount }}{{ setup.discount_type === 'percent' ? '%' : '' }}
                        </td>
                        <td class="p-3 text-muted-foreground">
                            {{ setup.items_count }}
                        </td>
                        <td class="p-3 text-muted-foreground">
                            {{ setup.duration_start_date }} – {{ setup.duration_end_date }}
                        </td>
                        <td class="p-3">
                            <div class="flex flex-wrap gap-1">
                                <Badge v-if="setup.show_in_homepage" variant="secondary">Homepage</Badge>
                                <Badge v-if="setup.show_in_shop" variant="secondary">Shop</Badge>
                            </div>
                        </td>
                        <td class="p-3">
                            <Badge :variant="setup.is_active ? 'default' : 'secondary'">
                                {{ setup.is_active ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(setup)">Edit</Link>
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
                                                StockClearanceSetupController.destroy.form(
                                                    setup,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete setup #{{
                                                        setup.id
                                                    }}?</DialogTitle
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

                    <tr v-if="stockClearanceSetups.data.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="6"
                        >
                            No stock clearance setups yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :meta="stockClearanceSetups" label="stock clearance setups" />
    </div>
</template>

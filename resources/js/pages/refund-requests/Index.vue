<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import RefundRequestController from '@/actions/App/Http/Controllers/Backend/RefundRequestController';
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
import { usePermissions } from '@/composables/usePermissions';
import { create, edit, index } from '@/routes/refund-requests';

type RefundRequest = {
    id: number;
    amount: string;
    reason: string;
    status: 'pending' | 'approved' | 'rejected';
    order: { id: number; order_number: string; customer_name: string } | null;
    order_item: { id: number; product_name: string } | null;
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
    refundRequests: Paginated<RefundRequest>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Refund requests',
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

const { can } = usePermissions();
</script>

<template>
    <Head title="Refund requests" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Refund requests"
                description="Review and manage customer refund requests"
            />
            <Button v-if="can('create refund requests')" as-child>
                <Link :href="create()">Add refund request</Link>
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
                        <th class="p-3 font-medium">Order</th>
                        <th class="p-3 font-medium">Item</th>
                        <th class="p-3 font-medium">Amount</th>
                        <th class="p-3 font-medium">Reason</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="refundRequest in refundRequests.data"
                        :key="refundRequest.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">
                            {{ refundRequest.order?.order_number ?? '—' }}
                            <div class="text-xs font-normal text-muted-foreground">
                                {{ refundRequest.order?.customer_name }}
                            </div>
                        </td>
                        <td class="p-3">
                            {{ refundRequest.order_item?.product_name ?? 'Whole order' }}
                        </td>
                        <td class="p-3">${{ refundRequest.amount }}</td>
                        <td class="p-3 max-w-xs truncate text-muted-foreground">
                            {{ refundRequest.reason }}
                        </td>
                        <td class="p-3">
                            <Badge :variant="statusVariant[refundRequest.status] ?? 'secondary'">
                                {{ refundRequest.status }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button
                                    v-if="can('edit refund requests')"
                                    variant="outline"
                                    size="sm"
                                    as-child
                                >
                                    <Link :href="edit(refundRequest)">Edit</Link>
                                </Button>

                                <Dialog v-if="can('delete refund requests')">
                                    <DialogTrigger as-child>
                                        <Button variant="destructive" size="sm"
                                            >Delete</Button
                                        >
                                    </DialogTrigger>
                                    <DialogContent>
                                        <Form
                                            v-bind="
                                                RefundRequestController.destroy.form(
                                                    refundRequest,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete this refund
                                                    request?</DialogTitle
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

                    <tr v-if="refundRequests.data.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="6"
                        >
                            No refund requests yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :meta="refundRequests" label="refund requests" />
    </div>
</template>

<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import SupportTicketController from '@/actions/App/Http/Controllers/Backend/SupportTicketController';
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
import { create, edit, index } from '@/routes/support-tickets';

type SupportTicket = {
    id: number;
    subject: string;
    type: string | null;
    priority: string;
    status: string;
    customer: { id: number; name: string };
};

defineProps<{
    supportTickets: SupportTicket[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Support tickets',
                href: index(),
            },
        ],
    },
});

const statusVariant: Record<string, 'default' | 'secondary' | 'destructive'> = {
    open: 'secondary',
    answered: 'default',
    closed: 'destructive',
};

const priorityVariant: Record<string, 'default' | 'secondary' | 'destructive'> = {
    low: 'secondary',
    medium: 'default',
    high: 'destructive',
};
</script>

<template>
    <Head title="Support tickets" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Support tickets"
                description="Respond to customer support requests"
            />
            <Button as-child>
                <Link :href="create()">Add ticket</Link>
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
                        <th class="p-3 font-medium">Subject</th>
                        <th class="p-3 font-medium">Customer</th>
                        <th class="p-3 font-medium">Type</th>
                        <th class="p-3 font-medium">Priority</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="supportTicket in supportTickets"
                        :key="supportTicket.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">
                            {{ supportTicket.subject }}
                        </td>
                        <td class="p-3">{{ supportTicket.customer.name }}</td>
                        <td class="p-3">{{ supportTicket.type ?? '—' }}</td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    priorityVariant[supportTicket.priority] ??
                                    'secondary'
                                "
                            >
                                {{ supportTicket.priority }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    statusVariant[supportTicket.status] ??
                                    'secondary'
                                "
                            >
                                {{ supportTicket.status }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(supportTicket)"
                                        >Edit</Link
                                    >
                                </Button>

                                <Dialog>
                                    <DialogTrigger as-child>
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            >Delete</Button
                                        >
                                    </DialogTrigger>
                                    <DialogContent>
                                        <Form
                                            v-bind="
                                                SupportTicketController.destroy.form(
                                                    supportTicket,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete ticket
                                                    "{{
                                                        supportTicket.subject
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

                    <tr v-if="supportTickets.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="6"
                        >
                            No support tickets yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

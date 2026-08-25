<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import CustomerController from '@/actions/App/Http/Controllers/Backend/CustomerController';
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
import { create, edit, index } from '@/routes/customers';

type Customer = {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    address: string | null;
    status: boolean;
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
    customers: Paginated<Customer>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Customers',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Customers" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Customers"
                description="Manage the customers who shop at your store"
            />
            <Button as-child>
                <Link :href="create()">Add customer</Link>
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
                        <th class="p-3 font-medium">Name</th>
                        <th class="p-3 font-medium">Email</th>
                        <th class="p-3 font-medium">Phone</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="customer in customers.data"
                        :key="customer.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">{{ customer.name }}</td>
                        <td class="p-3 text-muted-foreground">
                            {{ customer.email }}
                        </td>
                        <td class="p-3 text-muted-foreground">
                            {{ customer.phone ?? '—' }}
                        </td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    customer.status ? 'default' : 'secondary'
                                "
                            >
                                {{ customer.status ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(customer)">Edit</Link>
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
                                                CustomerController.destroy.form(
                                                    customer,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        customer.name
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

                    <tr v-if="customers.data.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="5"
                        >
                            No customers yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-if="customers.links.length > 3"
            class="flex flex-wrap items-center justify-center gap-1"
        >
            <template v-for="(link, index) in customers.links" :key="index">
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

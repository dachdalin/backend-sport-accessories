<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import SupplierController from '@/actions/App/Http/Controllers/Backend/SupplierController';
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
import { create, edit, index } from '@/routes/suppliers';

type Supplier = {
    id: number;
    name: string;
    contact_person: string | null;
    email: string | null;
    phone: string | null;
    address: string | null;
    status: boolean;
};

defineProps<{
    suppliers: Supplier[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Suppliers',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Suppliers" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Suppliers"
                description="Manage the suppliers you purchase inventory from"
            />
            <Button as-child>
                <Link :href="create()">Add supplier</Link>
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
                        <th class="p-3 font-medium">Contact person</th>
                        <th class="p-3 font-medium">Email</th>
                        <th class="p-3 font-medium">Phone</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="supplier in suppliers"
                        :key="supplier.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">{{ supplier.name }}</td>
                        <td class="p-3 text-muted-foreground">
                            {{ supplier.contact_person ?? '—' }}
                        </td>
                        <td class="p-3 text-muted-foreground">
                            {{ supplier.email ?? '—' }}
                        </td>
                        <td class="p-3 text-muted-foreground">
                            {{ supplier.phone ?? '—' }}
                        </td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    supplier.status ? 'default' : 'secondary'
                                "
                            >
                                {{ supplier.status ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(supplier)">Edit</Link>
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
                                                SupplierController.destroy.form(
                                                    supplier,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        supplier.name
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

                    <tr v-if="suppliers.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="6"
                        >
                            No suppliers yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

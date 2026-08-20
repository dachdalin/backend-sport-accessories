<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import TaxRateController from '@/actions/App/Http/Controllers/Backend/TaxRateController';
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
import { create, edit, index } from '@/routes/tax-rates';

type TaxRate = {
    id: number;
    name: string;
    region: string | null;
    rate: string;
    is_default: boolean;
    status: boolean;
};

defineProps<{
    taxRates: TaxRate[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Tax rates',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Tax rates" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Tax rates"
                description="Manage the tax rates applied to orders"
            />
            <Button as-child>
                <Link :href="create()">Add tax rate</Link>
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
                        <th class="p-3 font-medium">Region</th>
                        <th class="p-3 font-medium">Rate</th>
                        <th class="p-3 font-medium">Default</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="taxRate in taxRates"
                        :key="taxRate.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">{{ taxRate.name }}</td>
                        <td class="p-3 text-muted-foreground">
                            {{ taxRate.region ?? '—' }}
                        </td>
                        <td class="p-3">{{ Number(taxRate.rate) }}%</td>
                        <td class="p-3">
                            <Badge v-if="taxRate.is_default" variant="outline"
                                >Default</Badge
                            >
                            <span v-else class="text-muted-foreground">—</span>
                        </td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    taxRate.status ? 'default' : 'secondary'
                                "
                            >
                                {{ taxRate.status ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(taxRate)">Edit</Link>
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
                                                TaxRateController.destroy.form(
                                                    taxRate,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        taxRate.name
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

                    <tr v-if="taxRates.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="6"
                        >
                            No tax rates yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

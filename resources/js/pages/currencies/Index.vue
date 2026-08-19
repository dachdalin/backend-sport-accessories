<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import CurrencyController from '@/actions/App/Http/Controllers/Backend/CurrencyController';
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
import { create, edit, index } from '@/routes/currencies';

type Currency = {
    id: number;
    name: string;
    symbol: string;
    code: string;
    exchange_rate: string;
    status: boolean;
};

defineProps<{
    currencies: Currency[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Currencies',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Currencies" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Currencies"
                description="Manage the currencies available for checkout"
            />
            <Button as-child>
                <Link :href="create()">Add currency</Link>
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
                        <th class="p-3 font-medium">Code</th>
                        <th class="p-3 font-medium">Symbol</th>
                        <th class="p-3 font-medium">Exchange rate</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="currency in currencies"
                        :key="currency.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">{{ currency.name }}</td>
                        <td class="p-3">{{ currency.code }}</td>
                        <td class="p-3">{{ currency.symbol }}</td>
                        <td class="p-3">{{ currency.exchange_rate }}</td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    currency.status ? 'default' : 'secondary'
                                "
                            >
                                {{ currency.status ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(currency)">Edit</Link>
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
                                                CurrencyController.destroy.form(
                                                    currency,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        currency.name
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

                    <tr v-if="currencies.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="6"
                        >
                            No currencies yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

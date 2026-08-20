<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import WithdrawalMethodController from '@/actions/App/Http/Controllers/Backend/WithdrawalMethodController';
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
import { create, edit, index } from '@/routes/withdrawal-methods';

type WithdrawalMethod = {
    id: number;
    method_name: string;
    method_fields: string;
    is_default: boolean;
    status: boolean;
};

defineProps<{
    withdrawalMethods: WithdrawalMethod[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Withdrawal methods',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Withdrawal methods" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Withdrawal methods"
                description="Manage how vendors can withdraw their earnings"
            />
            <Button as-child>
                <Link :href="create()">Add withdrawal method</Link>
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
                        <th class="p-3 font-medium">Default</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="method in withdrawalMethods"
                        :key="method.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">
                            {{ method.method_name }}
                        </td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    method.is_default ? 'default' : 'outline'
                                "
                            >
                                {{ method.is_default ? 'Default' : '—' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    method.status ? 'default' : 'secondary'
                                "
                            >
                                {{ method.status ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(method)">Edit</Link>
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
                                                WithdrawalMethodController.destroy.form(
                                                    method,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        method.method_name
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

                    <tr v-if="withdrawalMethods.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="4"
                        >
                            No withdrawal methods yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

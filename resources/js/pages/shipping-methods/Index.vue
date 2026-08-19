<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import ShippingMethodController from '@/actions/App/Http/Controllers/Backend/ShippingMethodController';
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
import { create, edit, index } from '@/routes/shipping-methods';

type ShippingMethod = {
    id: number;
    title: string;
    cost: string;
    duration: string | null;
    status: boolean;
};

defineProps<{
    shippingMethods: ShippingMethod[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Shipping methods',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Shipping methods" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Shipping methods"
                description="Manage the shipping options offered at checkout"
            />
            <Button as-child>
                <Link :href="create()">Add shipping method</Link>
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
                        <th class="p-3 font-medium">Cost</th>
                        <th class="p-3 font-medium">Duration</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="shippingMethod in shippingMethods"
                        :key="shippingMethod.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">
                            {{ shippingMethod.title }}
                        </td>
                        <td class="p-3">${{ shippingMethod.cost }}</td>
                        <td class="p-3 text-muted-foreground">
                            {{ shippingMethod.duration ?? '—' }}
                        </td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    shippingMethod.status
                                        ? 'default'
                                        : 'secondary'
                                "
                            >
                                {{
                                    shippingMethod.status
                                        ? 'Active'
                                        : 'Inactive'
                                }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(shippingMethod)"
                                        >Edit</Link
                                    >
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
                                                ShippingMethodController.destroy.form(
                                                    shippingMethod,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        shippingMethod.title
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

                    <tr v-if="shippingMethods.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="5"
                        >
                            No shipping methods yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

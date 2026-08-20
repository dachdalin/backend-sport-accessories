<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import DeliveryZoneController from '@/actions/App/Http/Controllers/Backend/DeliveryZoneController';
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
import { create, edit, index } from '@/routes/delivery-zones';

type DeliveryZone = {
    id: number;
    zip_code: string;
    city: string | null;
    delivery_charge: string;
    status: boolean;
};

defineProps<{
    deliveryZones: DeliveryZone[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Delivery zones',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Delivery zones" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Delivery zones"
                description="Manage which zip codes you deliver to and their charge"
            />
            <Button as-child>
                <Link :href="create()">Add delivery zone</Link>
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
                        <th class="p-3 font-medium">Zip code</th>
                        <th class="p-3 font-medium">City</th>
                        <th class="p-3 font-medium">Delivery charge</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="zone in deliveryZones"
                        :key="zone.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">{{ zone.zip_code }}</td>
                        <td class="p-3 text-muted-foreground">
                            {{ zone.city ?? '—' }}
                        </td>
                        <td class="p-3">{{ zone.delivery_charge }}</td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    zone.status ? 'default' : 'secondary'
                                "
                            >
                                {{ zone.status ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(zone)">Edit</Link>
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
                                                DeliveryZoneController.destroy.form(
                                                    zone,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        zone.zip_code
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

                    <tr v-if="deliveryZones.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="5"
                        >
                            No delivery zones yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

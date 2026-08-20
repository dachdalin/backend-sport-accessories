<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import StoreLocationController from '@/actions/App/Http/Controllers/Backend/StoreLocationController';
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
import { create, edit, index } from '@/routes/store-locations';

type StoreLocation = {
    id: number;
    name: string;
    address: string;
    city: string;
    phone: string | null;
    opening_hours: string | null;
    status: boolean;
};

defineProps<{
    storeLocations: StoreLocation[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Store locations',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Store locations" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Store locations"
                description="Manage the physical retail locations customers can visit"
            />
            <Button as-child>
                <Link :href="create()">Add store location</Link>
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
                        <th class="p-3 font-medium">Address</th>
                        <th class="p-3 font-medium">City</th>
                        <th class="p-3 font-medium">Phone</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="storeLocation in storeLocations"
                        :key="storeLocation.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">
                            {{ storeLocation.name }}
                        </td>
                        <td class="p-3 text-muted-foreground">
                            {{ storeLocation.address }}
                        </td>
                        <td class="p-3">{{ storeLocation.city }}</td>
                        <td class="p-3 text-muted-foreground">
                            {{ storeLocation.phone ?? '—' }}
                        </td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    storeLocation.status
                                        ? 'default'
                                        : 'secondary'
                                "
                            >
                                {{
                                    storeLocation.status ? 'Active' : 'Inactive'
                                }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(storeLocation)"
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
                                                StoreLocationController.destroy.form(
                                                    storeLocation,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        storeLocation.name
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

                    <tr v-if="storeLocations.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="6"
                        >
                            No store locations yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import MostDemandedController from '@/actions/App/Http/Controllers/Backend/MostDemandedController';
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
import { create, edit, index } from '@/routes/most-demandeds';

type MostDemanded = {
    id: number;
    banner: string;
    status: boolean;
    product: { id: number; name: string } | null;
};

defineProps<{
    mostDemandeds: MostDemanded[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Most demanded',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Most demanded" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Most demanded"
                description="Feature the products your customers want most"
            />
            <Button as-child>
                <Link :href="create()">Add entry</Link>
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
                        <th class="p-3 font-medium">Banner</th>
                        <th class="p-3 font-medium">Product</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="mostDemanded in mostDemandeds"
                        :key="mostDemanded.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3">
                            <img
                                :src="`/storage/${mostDemanded.banner}`"
                                :alt="mostDemanded.product?.name ?? 'Banner'"
                                class="size-10 rounded object-cover"
                            />
                        </td>
                        <td class="p-3 font-medium">
                            {{ mostDemanded.product?.name ?? '—' }}
                        </td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    mostDemanded.status ? 'default' : 'secondary'
                                "
                            >
                                {{ mostDemanded.status ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(mostDemanded)">Edit</Link>
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
                                                MostDemandedController.destroy.form(
                                                    mostDemanded,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete this entry?</DialogTitle
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

                    <tr v-if="mostDemandeds.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="4"
                        >
                            No entries yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Link2Icon } from '@lucide/vue';
import FeatureDealController from '@/actions/App/Http/Controllers/Backend/FeatureDealController';
import Heading from '@/components/Heading.vue';
import Pagination from '@/components/Pagination.vue';
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
import { create, edit, index } from '@/routes/feature-deals';

type FeatureDeal = {
    id: number;
    photo: string;
    photo_storage_type: string;
    url: string | null;
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
    from: number | null;
    to: number | null;
    total: number;
    current_page: number;
    last_page: number;
};

defineProps<{
    featureDeals: Paginated<FeatureDeal>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Feature deals',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Feature deals" />

    <div class="flex flex-col gap-6">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <Heading
                title="Feature deals"
                description="Manage the featured deal tiles shown to customers"
            />
            <Button as-child class="w-full sm:w-auto">
                <Link :href="create()">Add feature deal</Link>
            </Button>
        </div>

        <!-- Desktop / tablet table -->
        <div
            class="hidden overflow-x-auto rounded-xl border border-sidebar-border/70 md:block dark:border-sidebar-border"
        >
            <table class="w-full text-left text-sm">
                <thead
                    class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border"
                >
                    <tr>
                        <th class="p-3 font-medium">Photo</th>
                        <th class="p-3 font-medium">URL</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="featureDeal in featureDeals.data"
                        :key="featureDeal.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3">
                            <img
                                :src="`/storage/${featureDeal.photo}`"
                                alt="Feature deal photo"
                                class="h-10 w-16 rounded object-cover"
                            />
                        </td>
                        <td class="p-3 text-muted-foreground">
                            {{ featureDeal.url ?? '—' }}
                        </td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    featureDeal.status ? 'default' : 'secondary'
                                "
                            >
                                {{ featureDeal.status ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(featureDeal)">Edit</Link>
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
                                                FeatureDealController.destroy.form(
                                                    featureDeal,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete this feature
                                                    deal?</DialogTitle
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

                    <tr v-if="featureDeals.data.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="4"
                        >
                            No feature deals yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile card list -->
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:hidden">
            <p
                v-if="featureDeals.data.length === 0"
                class="col-span-full rounded-xl border border-sidebar-border/70 p-6 text-center text-sm text-muted-foreground dark:border-sidebar-border"
            >
                No feature deals yet.
            </p>

            <div
                v-for="featureDeal in featureDeals.data"
                :key="featureDeal.id"
                class="flex flex-col overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
            >
                <div class="relative aspect-video w-full bg-muted">
                    <img
                        :src="`/storage/${featureDeal.photo}`"
                        alt="Feature deal photo"
                        class="size-full object-cover"
                    />
                    <Badge
                        :variant="featureDeal.status ? 'default' : 'secondary'"
                        class="absolute top-2 right-2"
                    >
                        {{ featureDeal.status ? 'Active' : 'Inactive' }}
                    </Badge>
                </div>

                <div class="flex flex-1 flex-col gap-3 p-3">
                    <div
                        class="flex min-w-0 items-center gap-1.5 text-sm text-muted-foreground"
                    >
                        <Link2Icon
                            class="size-3.5 shrink-0"
                            aria-hidden="true"
                        />
                        <span class="truncate">{{
                            featureDeal.url ?? 'No destination link'
                        }}</span>
                    </div>

                    <div
                        class="mt-auto flex gap-2 border-t border-sidebar-border/70 pt-3 dark:border-sidebar-border"
                    >
                        <Button
                            variant="outline"
                            size="sm"
                            as-child
                            class="flex-1"
                        >
                            <Link :href="edit(featureDeal)">Edit</Link>
                        </Button>

                        <Dialog>
                            <DialogTrigger as-child>
                                <Button
                                    variant="destructive"
                                    size="sm"
                                    class="flex-1"
                                    >Delete</Button
                                >
                            </DialogTrigger>
                            <DialogContent>
                                <Form
                                    v-bind="
                                        FeatureDealController.destroy.form(
                                            featureDeal,
                                        )
                                    "
                                    :options="{ preserveScroll: true }"
                                    v-slot="{ processing }"
                                >
                                    <DialogHeader class="space-y-3">
                                        <DialogTitle
                                            >Delete this feature
                                            deal?</DialogTitle
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
                </div>
            </div>
        </div>

        <Pagination :meta="featureDeals" label="feature deals" />
    </div>
</template>

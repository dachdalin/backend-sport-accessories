<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import CategoryController from '@/actions/App/Http/Controllers/Backend/CategoryController';
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
import { usePermissions } from '@/composables/usePermissions';
import { create, edit, index } from '@/routes/categories';

type Category = {
    id: number;
    name: string;
    slug: string;
    icon: string;
    icon_storage_type: string;
    parent_id: number | null;
    position: number;
    home_status: boolean;
    parent: { id: number; name: string } | null;
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
    categories: Paginated<Category>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Categories',
                href: index(),
            },
        ],
    },
});

const { can } = usePermissions();
</script>

<template>
    <Head title="Categories" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Categories"
                description="Organize the categories your products are listed under"
            />
            <Button v-if="can('create categories')" as-child>
                <Link :href="create()">Add category</Link>
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
                        <th class="p-3 font-medium">Icon</th>
                        <th class="p-3 font-medium">Name</th>
                        <th class="p-3 font-medium">Parent</th>
                        <th class="p-3 font-medium">Shown on home</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="category in categories.data"
                        :key="category.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3">
                            <img
                                :src="`/storage/${category.icon}`"
                                :alt="category.name"
                                class="size-10 rounded object-cover"
                            />
                        </td>
                        <td class="p-3 font-medium">{{ category.name }}</td>
                        <td class="p-3 text-muted-foreground">
                            {{ category.parent?.name ?? '—' }}
                        </td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    category.home_status
                                        ? 'default'
                                        : 'secondary'
                                "
                            >
                                {{ category.home_status ? 'Yes' : 'No' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button
                                    v-if="can('edit categories')"
                                    variant="outline"
                                    size="sm"
                                    as-child
                                >
                                    <Link :href="edit(category)">Edit</Link>
                                </Button>

                                <Dialog v-if="can('delete categories')">
                                    <DialogTrigger as-child>
                                        <Button variant="destructive" size="sm"
                                            >Delete</Button
                                        >
                                    </DialogTrigger>
                                    <DialogContent>
                                        <Form
                                            v-bind="
                                                CategoryController.destroy.form(
                                                    category,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        category.name
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

                    <tr v-if="categories.data.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="5"
                        >
                            No categories yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-if="categories.links.length > 3"
            class="flex flex-wrap items-center justify-center gap-1"
        >
            <template v-for="(link, index) in categories.links" :key="index">
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

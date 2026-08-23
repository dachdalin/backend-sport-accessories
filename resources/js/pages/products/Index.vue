<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import ProductController from '@/actions/App/Http/Controllers/Backend/ProductController';
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
import { create, edit, index } from '@/routes/products';

type Product = {
    id: number;
    name: string;
    thumbnail: string;
    thumbnail_storage_type: string;
    unit_price: string;
    current_stock: number;
    featured: boolean;
    status: boolean;
    category: { id: number; name: string } | null;
    brand: { id: number; name: string } | null;
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
    products: Paginated<Product>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Products',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Products" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Products"
                description="Manage your product catalog"
            />
            <Button as-child>
                <Link :href="create()">Add product</Link>
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
                        <th class="p-3 font-medium">Image</th>
                        <th class="p-3 font-medium">Name</th>
                        <th class="p-3 font-medium">Category</th>
                        <th class="p-3 font-medium">Brand</th>
                        <th class="p-3 font-medium">Price</th>
                        <th class="p-3 font-medium">Stock</th>
                        <th class="p-3 font-medium">Featured</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="product in products.data"
                        :key="product.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3">
                            <img
                                :src="`/storage/${product.thumbnail}`"
                                :alt="product.name"
                                class="size-10 rounded object-cover"
                            />
                        </td>
                        <td class="p-3 font-medium">{{ product.name }}</td>
                        <td class="p-3">
                            {{ product.category?.name ?? '—' }}
                        </td>
                        <td class="p-3">
                            {{ product.brand?.name ?? '—' }}
                        </td>
                        <td class="p-3">
                            ${{ product.unit_price }}
                        </td>
                        <td class="p-3">{{ product.current_stock }}</td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    product.featured ? 'default' : 'secondary'
                                "
                            >
                                {{ product.featured ? 'Yes' : 'No' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    product.status ? 'default' : 'secondary'
                                "
                            >
                                {{ product.status ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(product)">Edit</Link>
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
                                                ProductController.destroy.form(
                                                    product,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        product.name
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

                    <tr v-if="products.data.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="9"
                        >
                            No products yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-if="products.links.length > 3"
            class="flex flex-wrap items-center justify-center gap-1"
        >
            <template v-for="(link, index) in products.links" :key="index">
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

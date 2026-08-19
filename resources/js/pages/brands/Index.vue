<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import BrandController from '@/actions/App/Http/Controllers/BrandController';
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
import { create, edit, index } from '@/routes/brands';

type Brand = {
    id: number;
    name: string;
    image: string;
    image_storage_type: string;
    image_alt_text: string | null;
    status: boolean;
};

defineProps<{
    brands: Brand[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Brands',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Brands" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Brands"
                description="Manage the brands available for your products"
            />
            <Button as-child>
                <Link :href="create()">Add brand</Link>
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
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="brand in brands"
                        :key="brand.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3">
                            <img
                                :src="`/storage/${brand.image}`"
                                :alt="brand.image_alt_text ?? brand.name"
                                class="size-10 rounded object-cover"
                            />
                        </td>
                        <td class="p-3 font-medium">{{ brand.name }}</td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    brand.status ? 'default' : 'secondary'
                                "
                            >
                                {{ brand.status ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(brand)">Edit</Link>
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
                                                BrandController.destroy.form(
                                                    brand,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        brand.name
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

                    <tr v-if="brands.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="4"
                        >
                            No brands yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

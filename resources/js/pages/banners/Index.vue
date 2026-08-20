<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import BannerController from '@/actions/App/Http/Controllers/Backend/BannerController';
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
import { create, edit, index } from '@/routes/banners';

type Banner = {
    id: number;
    title: string;
    image: string;
    image_storage_type: string;
    image_alt_text: string | null;
    link_url: string | null;
    sort_order: number;
    status: boolean;
};

defineProps<{
    banners: Banner[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Banners',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Banners" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Banners"
                description="Manage the promotional banners shown to customers"
            />
            <Button as-child>
                <Link :href="create()">Add banner</Link>
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
                        <th class="p-3 font-medium">Title</th>
                        <th class="p-3 font-medium">Link</th>
                        <th class="p-3 font-medium">Order</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="banner in banners"
                        :key="banner.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3">
                            <img
                                :src="`/storage/${banner.image}`"
                                :alt="banner.image_alt_text ?? banner.title"
                                class="h-10 w-16 rounded object-cover"
                            />
                        </td>
                        <td class="p-3 font-medium">{{ banner.title }}</td>
                        <td class="p-3 text-muted-foreground">
                            {{ banner.link_url ?? '—' }}
                        </td>
                        <td class="p-3">{{ banner.sort_order }}</td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    banner.status ? 'default' : 'secondary'
                                "
                            >
                                {{ banner.status ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(banner)">Edit</Link>
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
                                                BannerController.destroy.form(
                                                    banner,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        banner.title
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

                    <tr v-if="banners.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="6"
                        >
                            No banners yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

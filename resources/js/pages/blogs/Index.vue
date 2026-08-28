<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import BlogController from '@/actions/App/Http/Controllers/Backend/BlogController';
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
import { create, edit, index } from '@/routes/blogs';

interface Blog {
    id: number;
    title: string;
    writer: string | null;
    image: string;
    image_alt_text: string | null;
    is_published: boolean;
    category: {
        id: number;
        name: string;
    } | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Paginated<T> {
    data: T[];
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
    current_page: number;
    last_page: number;
}

defineProps<{
    blogs: Paginated<Blog>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Blogs',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Blogs" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Blogs"
                description="Manage blog posts for your store"
            />
            <Button as-child>
                <Link :href="create()">Add blog</Link>
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
                        <th class="p-3 font-medium">Category</th>
                        <th class="p-3 font-medium">Writer</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="blog in blogs.data"
                        :key="blog.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3">
                            <img
                                :src="`/storage/${blog.image}`"
                                :alt="blog.image_alt_text ?? blog.title"
                                class="size-10 rounded object-cover"
                            />
                        </td>
                        <td class="p-3 font-medium">{{ blog.title }}</td>
                        <td class="p-3">
                            {{ blog.category?.name ?? '—' }}
                        </td>
                        <td class="p-3">{{ blog.writer ?? '—' }}</td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    blog.is_published ? 'default' : 'secondary'
                                "
                            >
                                {{ blog.is_published ? 'Published' : 'Draft' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(blog)">Edit</Link>
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
                                                BlogController.destroy.form(
                                                    blog,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        blog.title
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
                    <tr v-if="blogs.data.length === 0">
                        <td
                            colspan="6"
                            class="p-6 text-center text-muted-foreground"
                        >
                            No blogs yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :meta="blogs" label="blogs" />
    </div>
</template>

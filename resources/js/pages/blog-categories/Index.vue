<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import BlogCategoryController from '@/actions/App/Http/Controllers/Backend/BlogCategoryController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import Pagination from '@/components/Pagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { index as blogCategoriesIndex } from '@/routes/blog-categories';

interface BlogCategory {
    id: number;
    name: string;
    slug: string;
    status: boolean;
    click_count: number;
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
    blogCategories: Paginated<BlogCategory>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Blog categories',
                href: blogCategoriesIndex(),
            },
        ],
    },
});

const createOpen = ref(false);
const editingBlogCategory = ref<BlogCategory | null>(null);

function openEdit(blogCategory: BlogCategory) {
    editingBlogCategory.value = blogCategory;
}
</script>

<template>
    <Head title="Blog categories" />

    <div class="flex flex-col gap-6">
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="Blog categories"
                description="Manage the categories available for blog posts"
            />

            <Dialog v-model:open="createOpen">
                <DialogTrigger as-child>
                    <Button>
                        <Plus />
                        Add blog category
                    </Button>
                </DialogTrigger>
                <DialogContent>
                    <Form
                        v-bind="BlogCategoryController.store.form()"
                        reset-on-success
                        @success="createOpen = false"
                        class="space-y-6"
                        v-slot="{ errors, processing }"
                    >
                        <DialogHeader>
                            <DialogTitle>Add blog category</DialogTitle>
                            <DialogDescription>
                                Create a new category for blog posts.
                            </DialogDescription>
                        </DialogHeader>

                        <div class="grid gap-2">
                            <Label for="create-name">Name</Label>
                            <Input
                                id="create-name"
                                name="name"
                                placeholder="Training tips"
                                required
                            />
                            <InputError :message="errors.name" />
                        </div>

                        <div class="flex items-center gap-2">
                            <Checkbox
                                id="create-status"
                                name="status"
                                :default-value="true"
                            />
                            <Label for="create-status">Active</Label>
                            <InputError :message="errors.status" />
                        </div>

                        <DialogFooter class="gap-2">
                            <DialogClose as-child>
                                <Button variant="secondary">Cancel</Button>
                            </DialogClose>
                            <Button type="submit" :disabled="processing">
                                Save
                            </Button>
                        </DialogFooter>
                    </Form>
                </DialogContent>
            </Dialog>
        </div>

        <div
            class="relative overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
        >
            <table class="w-full text-left text-sm">
                <thead
                    class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border"
                >
                    <tr>
                        <th class="px-4 py-3 font-medium">Name</th>
                        <th class="px-4 py-3 font-medium">Slug</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Clicks</th>
                        <th class="px-4 py-3 font-medium">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="blogCategory in blogCategories.data"
                        :key="blogCategory.id"
                        class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border"
                    >
                        <td class="px-4 py-3">{{ blogCategory.name }}</td>
                        <td class="px-4 py-3 text-muted-foreground">
                            {{ blogCategory.slug }}
                        </td>
                        <td class="px-4 py-3">
                            <Badge
                                :variant="
                                    blogCategory.status
                                        ? 'default'
                                        : 'secondary'
                                "
                            >
                                {{
                                    blogCategory.status
                                        ? 'Active'
                                        : 'Inactive'
                                }}
                            </Badge>
                        </td>
                        <td class="px-4 py-3">
                            {{ blogCategory.click_count }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <Button
                                    variant="outline"
                                    size="icon-sm"
                                    @click="openEdit(blogCategory)"
                                >
                                    <Pencil />
                                    <span class="sr-only">Edit</span>
                                </Button>

                                <Dialog>
                                    <DialogTrigger as-child>
                                        <Button
                                            variant="destructive"
                                            size="icon-sm"
                                        >
                                            <Trash2 />
                                            <span class="sr-only"
                                                >Delete</span
                                            >
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent>
                                        <Form
                                            v-bind="
                                                BlogCategoryController.destroy.form(
                                                    {
                                                        blog_category:
                                                            blogCategory.id,
                                                    },
                                                )
                                            "
                                            :options="{
                                                preserveScroll: true,
                                            }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete
                                                    "{{
                                                        blogCategory.name
                                                    }}"?</DialogTitle
                                                >
                                                <DialogDescription>
                                                    This cannot be undone.
                                                </DialogDescription>
                                            </DialogHeader>

                                            <DialogFooter class="gap-2">
                                                <DialogClose as-child>
                                                    <Button
                                                        variant="secondary"
                                                    >
                                                        Cancel
                                                    </Button>
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
                    <tr v-if="blogCategories.data.length === 0">
                        <td
                            colspan="5"
                            class="px-4 py-6 text-center text-muted-foreground"
                        >
                            No blog categories yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :meta="blogCategories" label="blog categories" />

        <Dialog
            :open="editingBlogCategory !== null"
            @update:open="(open) => !open && (editingBlogCategory = null)"
        >
            <DialogContent v-if="editingBlogCategory">
                <Form
                    v-bind="
                        BlogCategoryController.update.form({
                            blog_category: editingBlogCategory.id,
                        })
                    "
                    @success="editingBlogCategory = null"
                    class="space-y-6"
                    v-slot="{ errors, processing }"
                >
                    <DialogHeader>
                        <DialogTitle>Edit blog category</DialogTitle>
                        <DialogDescription>
                            Update the blog category's name and status.
                        </DialogDescription>
                    </DialogHeader>

                    <div class="grid gap-2">
                        <Label for="edit-name">Name</Label>
                        <Input
                            id="edit-name"
                            name="name"
                            :default-value="editingBlogCategory.name"
                            required
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="flex items-center gap-2">
                        <Checkbox
                            id="edit-status"
                            name="status"
                            :default-value="editingBlogCategory.status"
                        />
                        <Label for="edit-status">Active</Label>
                        <InputError :message="errors.status" />
                    </div>

                    <DialogFooter class="gap-2">
                        <DialogClose as-child>
                            <Button variant="secondary">Cancel</Button>
                        </DialogClose>
                        <Button type="submit" :disabled="processing">
                            Save
                        </Button>
                    </DialogFooter>
                </Form>
            </DialogContent>
        </Dialog>
    </div>
</template>

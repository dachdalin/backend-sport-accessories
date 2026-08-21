<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import BlogController from '@/actions/App/Http/Controllers/Backend/BlogController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { edit, index } from '@/routes/blogs';

interface SelectOption {
    value: number;
    label: string;
}

interface Blog {
    id: number;
    blog_category_id: number | null;
    title: string;
    writer: string | null;
    description: string;
    image: string;
    image_alt_text: string | null;
    is_published: boolean;
    published_at: string | null;
}

defineProps<{
    blog: Blog;
    categories: SelectOption[];
}>();

defineOptions({
    layout: (pageProps: { blog: Blog }) => ({
        breadcrumbs: [
            {
                title: 'Blogs',
                href: index(),
            },
            {
                title: 'Edit blog',
                href: edit(pageProps.blog),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit blog" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit blog"
            :description="`Update the details for ${blog.title}`"
        />

        <Form
            v-bind="BlogController.update.form(blog)"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="title">Title</Label>
                <Input
                    id="title"
                    name="title"
                    required
                    autofocus
                    :default-value="blog.title"
                />
                <InputError :message="errors.title" />
            </div>

            <div class="grid gap-2">
                <Label for="blog_category_id">Category</Label>
                <Select
                    name="blog_category_id"
                    :default-value="
                        blog.blog_category_id
                            ? String(blog.blog_category_id)
                            : undefined
                    "
                >
                    <SelectTrigger id="blog_category_id" class="w-full">
                        <SelectValue placeholder="Select category" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="option in categories"
                            :key="option.value"
                            :value="String(option.value)"
                        >
                            {{ option.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors.blog_category_id" />
            </div>

            <div class="grid gap-2">
                <Label for="writer">Writer</Label>
                <Input
                    id="writer"
                    name="writer"
                    :default-value="blog.writer ?? ''"
                    placeholder="Jane Doe"
                />
                <InputError :message="errors.writer" />
            </div>

            <div class="grid gap-2">
                <Label for="description">Description</Label>
                <Textarea
                    id="description"
                    name="description"
                    required
                    :default-value="blog.description"
                    rows="8"
                />
                <InputError :message="errors.description" />
            </div>

            <div class="grid gap-2">
                <img
                    :src="`/storage/${blog.image}`"
                    :alt="blog.image_alt_text ?? blog.title"
                    class="h-24 w-40 rounded object-cover"
                />
                <Label for="image">Replace image</Label>
                <Input id="image" name="image" type="file" accept="image/*" />
                <InputError :message="errors.image" />
            </div>

            <div class="grid gap-2">
                <Label for="image_alt_text">Image alt text</Label>
                <Input
                    id="image_alt_text"
                    name="image_alt_text"
                    :default-value="blog.image_alt_text ?? ''"
                    placeholder="Describes the image"
                />
                <InputError :message="errors.image_alt_text" />
            </div>

            <div class="grid gap-2">
                <Label for="published_at">Publish date</Label>
                <Input
                    id="published_at"
                    name="published_at"
                    type="date"
                    :default-value="blog.published_at?.slice(0, 10)"
                />
                <InputError :message="errors.published_at" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="is_published"
                    name="is_published"
                    :default-value="blog.is_published"
                />
                <Label for="is_published">Published</Label>
                <InputError :message="errors.is_published" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save blog</Button>
            </div>
        </Form>
    </div>
</template>

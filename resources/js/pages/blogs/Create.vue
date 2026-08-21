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
import { create, index } from '@/routes/blogs';

interface SelectOption {
    value: number;
    label: string;
}

defineProps<{
    categories: SelectOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Blogs',
                href: index(),
            },
            {
                title: 'Add blog',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add blog" />

    <div class="flex flex-col space-y-6">
        <Heading title="Add blog" description="Publish a new blog post" />

        <Form
            v-bind="BlogController.store.form()"
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
                    placeholder="10 tips for choosing running shoes"
                />
                <InputError :message="errors.title" />
            </div>

            <div class="grid gap-2">
                <Label for="blog_category_id">Category</Label>
                <Select name="blog_category_id">
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
                <Input id="writer" name="writer" placeholder="Jane Doe" />
                <InputError :message="errors.writer" />
            </div>

            <div class="grid gap-2">
                <Label for="description">Description</Label>
                <Textarea
                    id="description"
                    name="description"
                    required
                    placeholder="Write the blog content"
                    rows="8"
                />
                <InputError :message="errors.description" />
            </div>

            <div class="grid gap-2">
                <Label for="image">Image</Label>
                <Input id="image" name="image" type="file" accept="image/*" />
                <InputError :message="errors.image" />
            </div>

            <div class="grid gap-2">
                <Label for="image_alt_text">Image alt text</Label>
                <Input
                    id="image_alt_text"
                    name="image_alt_text"
                    placeholder="Describes the image"
                />
                <InputError :message="errors.image_alt_text" />
            </div>

            <div class="grid gap-2">
                <Label for="published_at">Publish date</Label>
                <Input id="published_at" name="published_at" type="date" />
                <InputError :message="errors.published_at" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="is_published" name="is_published" />
                <Label for="is_published">Published</Label>
                <InputError :message="errors.is_published" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create blog</Button>
            </div>
        </Form>
    </div>
</template>

<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import {
    Calendar,
    FileText,
    Image as ImageIcon,
    Info,
    Rocket,
} from '@lucide/vue';
import BlogController from '@/actions/App/Http/Controllers/Backend/BlogController';
import Heading from '@/components/Heading.vue';
import ImageDropzone from '@/components/ImageDropzone.vue';
import InputError from '@/components/InputError.vue';
import RichTextEditor from '@/components/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
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
import { Spinner } from '@/components/ui/spinner';
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

    <div class="flex flex-col gap-6">
        <Heading
            title="Edit blog"
            :description="`Update the details for ${blog.title}`"
        />

        <Form
            v-bind="BlogController.update.form(blog)"
            class="grid gap-6 lg:grid-cols-3 lg:items-start"
            v-slot="{ errors, processing }"
        >
            <div class="flex flex-col gap-6 lg:col-span-2">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Info
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Details</CardTitle>
                        </div>
                        <CardDescription>
                            The title, category, and byline readers see.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
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

                        <div class="grid gap-4 sm:grid-cols-2">
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
                                    <SelectTrigger
                                        id="blog_category_id"
                                        class="w-full"
                                    >
                                        <SelectValue
                                            placeholder="Select category"
                                        />
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
                                <InputError
                                    :message="errors.blog_category_id"
                                />
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
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <FileText
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Content</CardTitle>
                        </div>
                        <CardDescription>
                            What readers see on the blog post page.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <RichTextEditor
                            name="description"
                            required
                            :default-value="blog.description"
                            placeholder="Write the blog content"
                            :error="errors.description"
                        />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <ImageIcon
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Media</CardTitle>
                        </div>
                        <CardDescription>
                            The cover image shown in listings and at the top of
                            the post.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <ImageDropzone
                            name="image"
                            label="Cover image"
                            hint="PNG, JPG or WEBP, up to 2MB. Leave empty to keep the current image."
                            :error="errors.image"
                            :processing="processing"
                            :initial-previews="[`/storage/${blog.image}`]"
                        />

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
                    </CardContent>
                </Card>
            </div>

            <div class="flex flex-col gap-6">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Calendar
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Schedule</CardTitle>
                        </div>
                        <CardDescription>
                            When this post goes live on the storefront.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
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

                        <label
                            for="is_published"
                            class="flex cursor-pointer items-center gap-3 rounded-lg border border-input px-3 py-2.5 transition-colors has-[[data-state=checked]]:border-primary has-[[data-state=checked]]:bg-primary/5"
                        >
                            <Checkbox
                                id="is_published"
                                name="is_published"
                                value="1"
                                :default-value="blog.is_published"
                            />
                            <span class="text-sm font-medium">Published</span>
                        </label>
                        <InputError :message="errors.is_published" />
                    </CardContent>
                </Card>

                <Card class="lg:sticky lg:top-6">
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Rocket
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Publish</CardTitle>
                        </div>
                        <CardDescription>
                            Save your changes to this post.
                        </CardDescription>
                    </CardHeader>
                    <CardFooter class="gap-3 border-t pt-6">
                        <Button :disabled="processing">
                            <Spinner v-if="processing" />
                            Save blog
                        </Button>
                        <Button variant="outline" as-child>
                            <Link :href="index()">Cancel</Link>
                        </Button>
                    </CardFooter>
                </Card>
            </div>
        </Form>
    </div>
</template>

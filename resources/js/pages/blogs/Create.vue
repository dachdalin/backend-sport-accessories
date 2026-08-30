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

    <div class="flex flex-col gap-6">
        <Heading title="Add blog" description="Publish a new blog post" />

        <Form
            v-bind="BlogController.store.form()"
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
                                placeholder="10 tips for choosing running shoes"
                            />
                            <InputError :message="errors.title" />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="blog_category_id">Category</Label>
                                <Select name="blog_category_id">
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
                            hint="PNG, JPG or WEBP, up to 2MB."
                            :error="errors.image"
                            :processing="processing"
                        />

                        <div class="grid gap-2">
                            <Label for="image_alt_text">Image alt text</Label>
                            <Input
                                id="image_alt_text"
                                name="image_alt_text"
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
                            Save to add this post to your blog.
                        </CardDescription>
                    </CardHeader>
                    <CardFooter class="gap-3 border-t pt-6">
                        <Button :disabled="processing">
                            <Spinner v-if="processing" />
                            Create blog
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

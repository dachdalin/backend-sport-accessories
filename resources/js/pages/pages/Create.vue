<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { EyeIcon, Search, SendIcon, Type } from '@lucide/vue';
import { computed, ref } from 'vue';
import PageController from '@/actions/App/Http/Controllers/Backend/PageController';
import Heading from '@/components/Heading.vue';
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
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { create, index } from '@/routes/pages';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Pages',
                href: index(),
            },
            {
                title: 'Add page',
                href: create(),
            },
        ],
    },
});

const title = ref('');
const metaTitle = ref('');
const metaDescription = ref('');
const status = ref(true);

const slugPreview = computed(() =>
    title.value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, ''),
);

const previewTitle = computed(
    () => metaTitle.value || title.value || 'Untitled page',
);
const previewDescription = computed(
    () =>
        metaDescription.value ||
        'No meta description yet — search engines will pick their own excerpt.',
);
</script>

<template>
    <Head title="Add page" />

    <div class="flex flex-col gap-6">
        <Heading
            title="Add page"
            description="Create a new static content page"
        />

        <Form
            v-bind="PageController.store.form()"
            class="grid gap-6 lg:grid-cols-3 lg:items-start"
            v-slot="{ errors, processing }"
        >
            <div class="flex flex-col gap-6 lg:col-span-2">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Type
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Content</CardTitle>
                        </div>
                        <CardDescription
                            >The title and body customers will read on the
                            page.</CardDescription
                        >
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div class="grid gap-2">
                            <Label for="title">Title</Label>
                            <Input
                                id="title"
                                v-model="title"
                                name="title"
                                required
                                autofocus
                                placeholder="About us"
                            />
                            <InputError :message="errors.title" />
                        </div>

                        <RichTextEditor
                            name="content"
                            label="Content"
                            required
                            placeholder="Page content"
                            :error="errors.content"
                        />
                    </CardContent>
                </Card>
            </div>

            <div class="flex flex-col gap-6">
                <Card class="lg:sticky lg:top-6">
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Search
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Search appearance</CardTitle>
                        </div>
                        <CardDescription
                            >How this page can look in search
                            results.</CardDescription
                        >
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div class="grid gap-2">
                            <Label for="meta_title">Meta title</Label>
                            <Input
                                id="meta_title"
                                v-model="metaTitle"
                                name="meta_title"
                                placeholder="SEO title"
                            />
                            <InputError :message="errors.meta_title" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="meta_description"
                                >Meta description</Label
                            >
                            <Textarea
                                id="meta_description"
                                v-model="metaDescription"
                                name="meta_description"
                                rows="3"
                                placeholder="SEO description"
                            />
                            <InputError :message="errors.meta_description" />
                        </div>

                        <div
                            class="rounded-lg border border-input bg-muted/40 p-3"
                        >
                            <p
                                class="truncate text-sm font-medium text-blue-600 dark:text-blue-400"
                            >
                                {{ previewTitle }}
                            </p>
                            <p
                                class="truncate text-xs text-emerald-700 dark:text-emerald-500"
                            >
                                /{{ slugPreview || 'page-url' }}
                            </p>
                            <p
                                class="mt-1 line-clamp-2 text-xs text-muted-foreground"
                            >
                                {{ previewDescription }}
                            </p>
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Illustrative search preview. The exact URL is
                            generated when you save.
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <EyeIcon
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Visibility</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <label
                            for="status"
                            class="flex items-center gap-2.5 rounded-lg border border-input px-3 py-2.5 has-[:checked]:border-primary/50 has-[:checked]:bg-primary/5"
                        >
                            <Checkbox
                                id="status"
                                name="status"
                                v-model="status"
                            />
                            <span class="grid gap-0.5">
                                <span class="text-sm font-medium">Active</span>
                                <span class="text-xs text-muted-foreground"
                                    >Visible to customers as soon as it's
                                    saved.</span
                                >
                            </span>
                        </label>
                        <InputError :message="errors.status" />
                    </CardContent>
                </Card>

                <Card>
                    <CardFooter class="flex-col gap-3 pt-6 sm:flex-row">
                        <Button class="w-full sm:w-auto" :disabled="processing">
                            <Spinner v-if="processing" />
                            <SendIcon
                                v-else
                                class="size-4"
                                aria-hidden="true"
                            />
                            Create page
                        </Button>
                        <Button
                            class="w-full sm:w-auto"
                            variant="outline"
                            as-child
                        >
                            <Link :href="index()">Cancel</Link>
                        </Button>
                    </CardFooter>
                </Card>
            </div>
        </Form>
    </div>
</template>

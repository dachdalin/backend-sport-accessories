<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import PageController from '@/actions/App/Http/Controllers/Backend/PageController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import RichTextEditor from '@/components/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { edit, index } from '@/routes/pages';

type Page = {
    id: number;
    title: string;
    slug: string;
    content: string;
    meta_title: string | null;
    meta_description: string | null;
    status: boolean;
};

const props = defineProps<{
    page: Page;
}>();

defineOptions({
    layout: (pageProps: { page: Page }) => ({
        breadcrumbs: [
            {
                title: 'Pages',
                href: index(),
            },
            {
                title: 'Edit page',
                href: edit(pageProps.page),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit page" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit page"
            :description="`Update the details for ${props.page.title}`"
        />

        <Form
            v-bind="PageController.update.form(props.page)"
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
                    :default-value="props.page.title"
                />
                <InputError :message="errors.title" />
            </div>

            <RichTextEditor
                name="content"
                label="Content"
                required
                :default-value="props.page.content"
                :error="errors.content"
            />

            <div class="grid gap-2">
                <Label for="meta_title">Meta title</Label>
                <Input
                    id="meta_title"
                    name="meta_title"
                    :default-value="props.page.meta_title ?? ''"
                    placeholder="SEO title"
                />
                <InputError :message="errors.meta_title" />
            </div>

            <div class="grid gap-2">
                <Label for="meta_description">Meta description</Label>
                <Textarea
                    id="meta_description"
                    name="meta_description"
                    rows="3"
                    :default-value="props.page.meta_description ?? ''"
                    placeholder="SEO description"
                />
                <InputError :message="errors.meta_description" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="props.page.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save page</Button>
            </div>
        </Form>
    </div>
</template>

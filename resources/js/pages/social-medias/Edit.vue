<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import SocialMediaController from '@/actions/App/Http/Controllers/Backend/SocialMediaController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit, index } from '@/routes/social-medias';

type SocialMedia = {
    id: number;
    name: string;
    link: string;
    icon: string | null;
    status: boolean;
};

defineProps<{
    socialMedia: SocialMedia;
}>();

defineOptions({
    layout: (pageProps: { socialMedia: SocialMedia }) => ({
        breadcrumbs: [
            {
                title: 'Social media',
                href: index(),
            },
            {
                title: 'Edit social link',
                href: edit(pageProps.socialMedia),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit social link" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit social link"
            :description="`Update the details for ${socialMedia.name}`"
        />

        <Form
            v-bind="SocialMediaController.update.form(socialMedia)"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    name="name"
                    required
                    autofocus
                    :default-value="socialMedia.name"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="link">Link</Label>
                <Input
                    id="link"
                    name="link"
                    type="url"
                    required
                    :default-value="socialMedia.link"
                />
                <InputError :message="errors.link" />
            </div>

            <div class="grid gap-2">
                <Label for="icon">Icon</Label>
                <Input
                    id="icon"
                    name="icon"
                    :default-value="socialMedia.icon ?? ''"
                    placeholder="facebook"
                />
                <InputError :message="errors.icon" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="socialMedia.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save social link</Button>
            </div>
        </Form>
    </div>
</template>

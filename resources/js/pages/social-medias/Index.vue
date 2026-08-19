<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import SocialMediaController from '@/actions/App/Http/Controllers/Backend/SocialMediaController';
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
import { create, edit, index } from '@/routes/social-medias';

type SocialMedia = {
    id: number;
    name: string;
    link: string;
    icon: string | null;
    status: boolean;
};

defineProps<{
    socialMedias: SocialMedia[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Social media',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Social media" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Social media"
                description="Manage the social links shown in your storefront"
            />
            <Button as-child>
                <Link :href="create()">Add social link</Link>
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
                        <th class="p-3 font-medium">Name</th>
                        <th class="p-3 font-medium">Link</th>
                        <th class="p-3 font-medium">Icon</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="socialMedia in socialMedias"
                        :key="socialMedia.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">
                            {{ socialMedia.name }}
                        </td>
                        <td class="p-3">
                            <a
                                :href="socialMedia.link"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-muted-foreground underline decoration-neutral-300 underline-offset-4 hover:text-foreground dark:decoration-neutral-500"
                            >
                                {{ socialMedia.link }}
                            </a>
                        </td>
                        <td class="p-3 text-muted-foreground">
                            {{ socialMedia.icon ?? '—' }}
                        </td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    socialMedia.status ? 'default' : 'secondary'
                                "
                            >
                                {{ socialMedia.status ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(socialMedia)">Edit</Link>
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
                                                SocialMediaController.destroy.form(
                                                    socialMedia,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        socialMedia.name
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

                    <tr v-if="socialMedias.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="5"
                        >
                            No social media links yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import SocialMediaController from '@/actions/App/Http/Controllers/Backend/SocialMediaController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
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
import { Spinner } from '@/components/ui/spinner';
import { usePermissions } from '@/composables/usePermissions';
import { index } from '@/routes/social-medias';

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

const { can } = usePermissions();

const createOpen = ref(false);
const editingSocialMedia = ref<SocialMedia | null>(null);
</script>

<template>
    <Head title="Social media" />

    <div class="flex flex-col gap-6">
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="Social media"
                description="Manage the social links shown in your storefront"
            />

            <template v-if="can('create social medias')">
                <Dialog v-model:open="createOpen">
                    <DialogTrigger as-child>
                        <Button>
                            <Plus />
                            Add social link
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <Form
                            v-bind="SocialMediaController.store.form()"
                            reset-on-success
                            @success="createOpen = false"
                            class="space-y-6"
                            v-slot="{ errors, processing }"
                        >
                            <DialogHeader>
                                <DialogTitle>Add social link</DialogTitle>
                                <DialogDescription>
                                    Add a social media link to your
                                    storefront.
                                </DialogDescription>
                            </DialogHeader>

                            <div class="grid gap-2">
                                <Label for="create-name">Name</Label>
                                <Input
                                    id="create-name"
                                    name="name"
                                    required
                                    autofocus
                                    placeholder="Facebook"
                                />
                                <InputError :message="errors.name" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="create-link">Link</Label>
                                <Input
                                    id="create-link"
                                    name="link"
                                    type="url"
                                    required
                                    placeholder="https://facebook.com/yourstore"
                                />
                                <InputError :message="errors.link" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="create-icon">Icon</Label>
                                <Input
                                    id="create-icon"
                                    name="icon"
                                    placeholder="facebook"
                                />
                                <InputError :message="errors.icon" />
                            </div>

                            <div class="flex items-center gap-2">
                                <Checkbox
                                    id="create-status"
                                    name="status"
                                    value="1"
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
                                    <Spinner v-if="processing" />
                                    Save
                                </Button>
                            </DialogFooter>
                        </Form>
                    </DialogContent>
                </Dialog>
            </template>
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
                        <th class="p-3 text-right font-medium">
                            <span class="sr-only">Actions</span>
                        </th>
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
                                <Button
                                    v-if="can('edit social medias')"
                                    variant="outline"
                                    size="icon-sm"
                                    @click="editingSocialMedia = socialMedia"
                                >
                                    <Pencil />
                                    <span class="sr-only">Edit</span>
                                </Button>

                                <Dialog v-if="can('delete social medias')">
                                    <DialogTrigger as-child>
                                        <Button
                                            variant="destructive"
                                            size="icon-sm"
                                        >
                                            <Trash2 />
                                            <span class="sr-only">Delete</span>
                                        </Button>
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
                                                <DialogDescription>
                                                    This cannot be undone.
                                                </DialogDescription>
                                            </DialogHeader>

                                            <DialogFooter class="gap-2">
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
                                                    <Spinner
                                                        v-if="processing"
                                                    />
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

        <Dialog
            :open="editingSocialMedia !== null"
            @update:open="(open) => !open && (editingSocialMedia = null)"
        >
            <DialogContent v-if="editingSocialMedia">
                <Form
                    v-bind="
                        SocialMediaController.update.form(editingSocialMedia)
                    "
                    @success="editingSocialMedia = null"
                    class="space-y-6"
                    v-slot="{ errors, processing }"
                >
                    <DialogHeader>
                        <DialogTitle>Edit social link</DialogTitle>
                        <DialogDescription>
                            Update the details for
                            {{ editingSocialMedia.name }}.
                        </DialogDescription>
                    </DialogHeader>

                    <div class="grid gap-2">
                        <Label for="edit-name">Name</Label>
                        <Input
                            id="edit-name"
                            name="name"
                            required
                            autofocus
                            :default-value="editingSocialMedia.name"
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-link">Link</Label>
                        <Input
                            id="edit-link"
                            name="link"
                            type="url"
                            required
                            :default-value="editingSocialMedia.link"
                        />
                        <InputError :message="errors.link" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-icon">Icon</Label>
                        <Input
                            id="edit-icon"
                            name="icon"
                            :default-value="editingSocialMedia.icon ?? ''"
                            placeholder="facebook"
                        />
                        <InputError :message="errors.icon" />
                    </div>

                    <div class="flex items-center gap-2">
                        <Checkbox
                            id="edit-status"
                            name="status"
                            value="1"
                            :default-value="editingSocialMedia.status"
                        />
                        <Label for="edit-status">Active</Label>
                        <InputError :message="errors.status" />
                    </div>

                    <DialogFooter class="gap-2">
                        <DialogClose as-child>
                            <Button variant="secondary">Cancel</Button>
                        </DialogClose>
                        <Button type="submit" :disabled="processing">
                            <Spinner v-if="processing" />
                            Save
                        </Button>
                    </DialogFooter>
                </Form>
            </DialogContent>
        </Dialog>
    </div>
</template>

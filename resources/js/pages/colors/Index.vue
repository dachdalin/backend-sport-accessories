<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { ref, watch } from 'vue';
import ColorController from '@/actions/App/Http/Controllers/Backend/ColorController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
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
import { index as colorsIndex } from '@/routes/colors';

interface Color {
    id: number;
    name: string;
    code: string;
}

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type Paginated<T> = {
    data: T[];
    links: PaginationLink[];
};

defineProps<{
    colors: Paginated<Color>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Colors',
                href: colorsIndex(),
            },
        ],
    },
});

const { can } = usePermissions();

const createOpen = ref(false);
const createCode = ref('');
const editingColor = ref<Color | null>(null);
const editCode = ref('');

watch(createOpen, (open) => {
    if (open) {
        createCode.value = '';
    }
});

function openEdit(color: Color) {
    editingColor.value = color;
    editCode.value = color.code;
}
</script>

<template>
    <Head title="Colors" />

    <div class="flex flex-col gap-6">
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="Colors"
                description="Manage the colors available for products"
            />

            <template v-if="can('create colors')">
                <Dialog v-model:open="createOpen">
                    <DialogTrigger as-child>
                        <Button>
                            <Plus />
                            Add color
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <Form
                            v-bind="ColorController.store.form()"
                            reset-on-success
                            @success="createOpen = false"
                            class="space-y-6"
                            v-slot="{ errors, processing }"
                        >
                            <DialogHeader>
                                <DialogTitle>Add color</DialogTitle>
                                <DialogDescription>
                                    Create a new color for products.
                                </DialogDescription>
                            </DialogHeader>

                            <div class="grid gap-2">
                                <Label for="create-name">Name</Label>
                                <Input
                                    id="create-name"
                                    name="name"
                                    placeholder="Red"
                                    required
                                />
                                <InputError :message="errors.name" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="create-code">Code</Label>
                                <div class="flex items-center gap-3">
                                    <span
                                        class="size-9 shrink-0 rounded-md border border-input bg-muted"
                                        :style="{ backgroundColor: createCode }"
                                    />
                                    <Input
                                        id="create-code"
                                        name="code"
                                        placeholder="#ff0000"
                                        required
                                        v-model="createCode"
                                    />
                                </div>
                                <InputError :message="errors.code" />
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
            class="relative overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
        >
            <table class="w-full text-left text-sm">
                <thead
                    class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border"
                >
                    <tr>
                        <th class="px-4 py-3 font-medium">Name</th>
                        <th class="px-4 py-3 font-medium">Code</th>
                        <th class="px-4 py-3 font-medium">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="color in colors.data"
                        :key="color.id"
                        class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border"
                    >
                        <td class="px-4 py-3">{{ color.name }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span
                                    class="size-4 shrink-0 rounded-full border"
                                    :style="{ backgroundColor: color.code }"
                                />
                                {{ color.code }}
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <Button
                                    v-if="can('edit colors')"
                                    variant="outline"
                                    size="icon-sm"
                                    @click="openEdit(color)"
                                >
                                    <Pencil />
                                    <span class="sr-only">Edit</span>
                                </Button>

                                <Dialog v-if="can('delete colors')">
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
                                                ColorController.destroy.form({
                                                    color: color.id,
                                                })
                                            "
                                            :options="{
                                                preserveScroll: true,
                                            }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        color.name
                                                    }}"?</DialogTitle
                                                >
                                                <DialogDescription>
                                                    This cannot be undone.
                                                </DialogDescription>
                                            </DialogHeader>

                                            <DialogFooter class="gap-2">
                                                <DialogClose as-child>
                                                    <Button variant="secondary">
                                                        Cancel
                                                    </Button>
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
                    <tr v-if="colors.data.length === 0">
                        <td
                            colspan="3"
                            class="px-4 py-6 text-center text-muted-foreground"
                        >
                            No colors yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-if="colors.links.length > 3"
            class="flex flex-wrap items-center justify-center gap-1"
        >
            <template v-for="(link, index) in colors.links" :key="index">
                <span
                    v-if="!link.url"
                    class="rounded-md px-3 py-1.5 text-sm text-muted-foreground"
                    v-html="link.label"
                />
                <Link
                    v-else
                    :href="link.url"
                    preserve-scroll
                    class="rounded-md px-3 py-1.5 text-sm"
                    :class="
                        link.active
                            ? 'bg-primary text-primary-foreground'
                            : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground'
                    "
                    v-html="link.label"
                />
            </template>
        </div>

        <Dialog
            :open="editingColor !== null"
            @update:open="(open) => !open && (editingColor = null)"
        >
            <DialogContent v-if="editingColor">
                <Form
                    v-bind="
                        ColorController.update.form({
                            color: editingColor.id,
                        })
                    "
                    @success="editingColor = null"
                    class="space-y-6"
                    v-slot="{ errors, processing }"
                >
                    <DialogHeader>
                        <DialogTitle>Edit color</DialogTitle>
                        <DialogDescription>
                            Update the color's name and code.
                        </DialogDescription>
                    </DialogHeader>

                    <div class="grid gap-2">
                        <Label for="edit-name">Name</Label>
                        <Input
                            id="edit-name"
                            name="name"
                            :default-value="editingColor.name"
                            required
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-code">Code</Label>
                        <div class="flex items-center gap-3">
                            <span
                                class="size-9 shrink-0 rounded-md border border-input bg-muted"
                                :style="{ backgroundColor: editCode }"
                            />
                            <Input
                                id="edit-code"
                                name="code"
                                required
                                v-model="editCode"
                            />
                        </div>
                        <InputError :message="errors.code" />
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

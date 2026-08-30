<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import SearchFunctionController from '@/actions/App/Http/Controllers/Backend/SearchFunctionController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { usePermissions } from '@/composables/usePermissions';
import { index } from '@/routes/search-functions';

interface VisibilityOption {
    value: string;
    label: string;
}

type SearchFunction = {
    id: number;
    key: string;
    url: string;
    visible_for: string;
};

defineProps<{
    searchFunctions: SearchFunction[];
    visibilities: VisibilityOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Search functions',
                href: index(),
            },
        ],
    },
});

const { can } = usePermissions();

const createOpen = ref(false);
const editingSearchFunction = ref<SearchFunction | null>(null);

function openEdit(searchFunction: SearchFunction) {
    editingSearchFunction.value = searchFunction;
}
</script>

<template>
    <Head title="Search functions" />

    <div class="flex flex-col gap-6">
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="Search functions"
                description="Manage quick search shortcuts shown across the app"
            />

            <template v-if="can('create search functions')">
                <Dialog v-model:open="createOpen">
                    <DialogTrigger as-child>
                        <Button>
                            <Plus />
                            Add search function
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <Form
                            v-bind="SearchFunctionController.store.form()"
                            reset-on-success
                            @success="createOpen = false"
                            class="space-y-6"
                            v-slot="{ errors, processing }"
                        >
                            <DialogHeader>
                                <DialogTitle>Add search function</DialogTitle>
                                <DialogDescription>
                                    Create a quick search shortcut.
                                </DialogDescription>
                            </DialogHeader>

                            <div class="grid gap-2">
                                <Label for="create-key">Key</Label>
                                <Input
                                    id="create-key"
                                    name="key"
                                    placeholder="New arrivals"
                                    required
                                    autofocus
                                />
                                <InputError :message="errors.key" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="create-url">URL</Label>
                                <Input
                                    id="create-url"
                                    name="url"
                                    placeholder="/products?sort=new"
                                    required
                                />
                                <InputError :message="errors.url" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="create-visible_for"
                                    >Visible for</Label
                                >
                                <Select
                                    name="visible_for"
                                    default-value="admin"
                                >
                                    <SelectTrigger
                                        id="create-visible_for"
                                        class="w-full"
                                    >
                                        <SelectValue
                                            placeholder="Select an audience"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="option in visibilities"
                                            :key="option.value"
                                            :value="option.value"
                                        >
                                            {{ option.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="errors.visible_for" />
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
                        <th class="p-3 font-medium">Key</th>
                        <th class="p-3 font-medium">URL</th>
                        <th class="p-3 font-medium">Visible for</th>
                        <th class="p-3 text-right font-medium">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="searchFunction in searchFunctions"
                        :key="searchFunction.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="p-3 font-medium">
                            {{ searchFunction.key }}
                        </td>
                        <td class="p-3 text-muted-foreground">
                            {{ searchFunction.url }}
                        </td>
                        <td class="p-3">
                            <Badge variant="secondary">
                                {{ searchFunction.visible_for }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button
                                    v-if="can('edit search functions')"
                                    variant="outline"
                                    size="icon-sm"
                                    @click="openEdit(searchFunction)"
                                >
                                    <Pencil />
                                    <span class="sr-only">Edit</span>
                                </Button>

                                <Dialog v-if="can('delete search functions')">
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
                                                SearchFunctionController.destroy.form(
                                                    searchFunction,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        searchFunction.key
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

                    <tr v-if="searchFunctions.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="4"
                        >
                            No search functions yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Dialog
            :open="editingSearchFunction !== null"
            @update:open="(open) => !open && (editingSearchFunction = null)"
        >
            <DialogContent v-if="editingSearchFunction">
                <Form
                    v-bind="
                        SearchFunctionController.update.form(
                            editingSearchFunction,
                        )
                    "
                    @success="editingSearchFunction = null"
                    class="space-y-6"
                    v-slot="{ errors, processing }"
                >
                    <DialogHeader>
                        <DialogTitle>Edit search function</DialogTitle>
                        <DialogDescription>
                            Update the key, URL, and audience.
                        </DialogDescription>
                    </DialogHeader>

                    <div class="grid gap-2">
                        <Label for="edit-key">Key</Label>
                        <Input
                            id="edit-key"
                            name="key"
                            :default-value="editingSearchFunction.key"
                            required
                        />
                        <InputError :message="errors.key" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-url">URL</Label>
                        <Input
                            id="edit-url"
                            name="url"
                            :default-value="editingSearchFunction.url"
                            required
                        />
                        <InputError :message="errors.url" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-visible_for">Visible for</Label>
                        <Select
                            name="visible_for"
                            :default-value="editingSearchFunction.visible_for"
                        >
                            <SelectTrigger id="edit-visible_for" class="w-full">
                                <SelectValue placeholder="Select an audience" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="option in visibilities"
                                    :key="option.value"
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.visible_for" />
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

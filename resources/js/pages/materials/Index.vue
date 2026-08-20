<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import MaterialController from '@/actions/App/Http/Controllers/Backend/MaterialController';
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
import { index as materialsIndex } from '@/routes/materials';

interface Material {
    id: number;
    name: string;
    code: string;
}

defineProps<{
    materials: Material[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Materials',
                href: materialsIndex(),
            },
        ],
    },
});

const createOpen = ref(false);
const editingMaterial = ref<Material | null>(null);

function openEdit(material: Material) {
    editingMaterial.value = material;
}
</script>

<template>
    <Head title="Materials" />

    <div class="flex flex-col gap-6">
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="Materials"
                description="Manage the materials available for products"
            />

            <Dialog v-model:open="createOpen">
                <DialogTrigger as-child>
                    <Button>
                        <Plus />
                        Add material
                    </Button>
                </DialogTrigger>
                <DialogContent>
                    <Form
                        v-bind="MaterialController.store.form()"
                        reset-on-success
                        @success="createOpen = false"
                        class="space-y-6"
                        v-slot="{ errors, processing }"
                    >
                        <DialogHeader>
                            <DialogTitle>Add material</DialogTitle>
                            <DialogDescription>
                                Create a new material for products.
                            </DialogDescription>
                        </DialogHeader>

                        <div class="grid gap-2">
                            <Label for="create-name">Name</Label>
                            <Input
                                id="create-name"
                                name="name"
                                placeholder="Nylon"
                                required
                            />
                            <InputError :message="errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="create-code">Code</Label>
                            <Input
                                id="create-code"
                                name="code"
                                placeholder="NYL"
                                required
                            />
                            <InputError :message="errors.code" />
                        </div>

                        <DialogFooter class="gap-2">
                            <DialogClose as-child>
                                <Button variant="secondary">Cancel</Button>
                            </DialogClose>
                            <Button type="submit" :disabled="processing">
                                Save
                            </Button>
                        </DialogFooter>
                    </Form>
                </DialogContent>
            </Dialog>
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
                        v-for="material in materials"
                        :key="material.id"
                        class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border"
                    >
                        <td class="px-4 py-3">{{ material.name }}</td>
                        <td class="px-4 py-3">{{ material.code }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <Button
                                    variant="outline"
                                    size="icon-sm"
                                    @click="openEdit(material)"
                                >
                                    <Pencil />
                                    <span class="sr-only">Edit</span>
                                </Button>

                                <Dialog>
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
                                                MaterialController.destroy.form(
                                                    {
                                                        material: material.id,
                                                    },
                                                )
                                            "
                                            :options="{
                                                preserveScroll: true,
                                            }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        material.name
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
                                                    Delete
                                                </Button>
                                            </DialogFooter>
                                        </Form>
                                    </DialogContent>
                                </Dialog>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="materials.length === 0">
                        <td
                            colspan="3"
                            class="px-4 py-6 text-center text-muted-foreground"
                        >
                            No materials yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Dialog
            :open="editingMaterial !== null"
            @update:open="(open) => !open && (editingMaterial = null)"
        >
            <DialogContent v-if="editingMaterial">
                <Form
                    v-bind="
                        MaterialController.update.form({
                            material: editingMaterial.id,
                        })
                    "
                    @success="editingMaterial = null"
                    class="space-y-6"
                    v-slot="{ errors, processing }"
                >
                    <DialogHeader>
                        <DialogTitle>Edit material</DialogTitle>
                        <DialogDescription>
                            Update the material's name and code.
                        </DialogDescription>
                    </DialogHeader>

                    <div class="grid gap-2">
                        <Label for="edit-name">Name</Label>
                        <Input
                            id="edit-name"
                            name="name"
                            :default-value="editingMaterial.name"
                            required
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-code">Code</Label>
                        <Input
                            id="edit-code"
                            name="code"
                            :default-value="editingMaterial.code"
                            required
                        />
                        <InputError :message="errors.code" />
                    </div>

                    <DialogFooter class="gap-2">
                        <DialogClose as-child>
                            <Button variant="secondary">Cancel</Button>
                        </DialogClose>
                        <Button type="submit" :disabled="processing">
                            Save
                        </Button>
                    </DialogFooter>
                </Form>
            </DialogContent>
        </Dialog>
    </div>
</template>

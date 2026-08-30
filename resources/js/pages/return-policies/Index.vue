<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import ReturnPolicyController from '@/actions/App/Http/Controllers/Backend/ReturnPolicyController';
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
import { Textarea } from '@/components/ui/textarea';
import { usePermissions } from '@/composables/usePermissions';
import { index } from '@/routes/return-policies';

type ReturnPolicy = {
    id: number;
    title: string;
    description: string;
    days_allowed: number;
    status: boolean;
};

defineProps<{
    returnPolicies: ReturnPolicy[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Return policies',
                href: index(),
            },
        ],
    },
});

const { can } = usePermissions();

const createOpen = ref(false);
const editingPolicy = ref<ReturnPolicy | null>(null);

function openEdit(returnPolicy: ReturnPolicy) {
    editingPolicy.value = returnPolicy;
}
</script>

<template>
    <Head title="Return policies" />

    <div class="flex flex-col gap-6">
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="Return policies"
                description="Manage the return policies shown to customers"
            />

            <template v-if="can('create return policies')">
                <Dialog v-model:open="createOpen">
                    <DialogTrigger as-child>
                        <Button>
                            <Plus />
                            Add return policy
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <Form
                            v-bind="ReturnPolicyController.store.form()"
                            reset-on-success
                            @success="createOpen = false"
                            class="space-y-6"
                            v-slot="{ errors, processing }"
                        >
                            <DialogHeader>
                                <DialogTitle>Add return policy</DialogTitle>
                                <DialogDescription>
                                    Define a return window customers can see at
                                    checkout.
                                </DialogDescription>
                            </DialogHeader>

                            <div class="grid gap-2">
                                <Label for="create-title">Title</Label>
                                <Input
                                    id="create-title"
                                    name="title"
                                    placeholder="Standard Returns"
                                    required
                                    autofocus
                                />
                                <InputError :message="errors.title" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="create-description"
                                    >Description</Label
                                >
                                <Textarea
                                    id="create-description"
                                    name="description"
                                    placeholder="Explain what qualifies for a return and how customers start one."
                                    rows="4"
                                    required
                                />
                                <InputError :message="errors.description" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="create-days_allowed"
                                    >Days allowed</Label
                                >
                                <Input
                                    id="create-days_allowed"
                                    name="days_allowed"
                                    type="number"
                                    min="0"
                                    max="365"
                                    default-value="30"
                                    class="max-w-32"
                                />
                                <InputError :message="errors.days_allowed" />
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
            class="relative overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
        >
            <table class="w-full text-left text-sm">
                <thead
                    class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border"
                >
                    <tr>
                        <th class="px-4 py-3 font-medium">Title</th>
                        <th class="px-4 py-3 font-medium">Description</th>
                        <th class="px-4 py-3 font-medium">Days allowed</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="returnPolicy in returnPolicies"
                        :key="returnPolicy.id"
                        class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border"
                    >
                        <td class="px-4 py-3 font-medium">
                            {{ returnPolicy.title }}
                        </td>
                        <td
                            class="max-w-md truncate px-4 py-3 text-muted-foreground"
                            :title="returnPolicy.description"
                        >
                            {{ returnPolicy.description }}
                        </td>
                        <td class="px-4 py-3">
                            {{ returnPolicy.days_allowed }} days
                        </td>
                        <td class="px-4 py-3">
                            <Badge
                                :variant="
                                    returnPolicy.status
                                        ? 'default'
                                        : 'secondary'
                                "
                            >
                                {{
                                    returnPolicy.status ? 'Active' : 'Inactive'
                                }}
                            </Badge>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <Button
                                    v-if="can('edit return policies')"
                                    variant="outline"
                                    size="icon-sm"
                                    @click="openEdit(returnPolicy)"
                                >
                                    <Pencil />
                                    <span class="sr-only">Edit</span>
                                </Button>

                                <Dialog v-if="can('delete return policies')">
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
                                                ReturnPolicyController.destroy.form(
                                                    {
                                                        return_policy:
                                                            returnPolicy.id,
                                                    },
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        returnPolicy.title
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

                    <tr v-if="returnPolicies.length === 0">
                        <td
                            class="px-4 py-6 text-center text-muted-foreground"
                            colspan="5"
                        >
                            No return policies yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Dialog
            :open="editingPolicy !== null"
            @update:open="(open) => !open && (editingPolicy = null)"
        >
            <DialogContent v-if="editingPolicy">
                <Form
                    v-bind="
                        ReturnPolicyController.update.form({
                            return_policy: editingPolicy.id,
                        })
                    "
                    @success="editingPolicy = null"
                    class="space-y-6"
                    v-slot="{ errors, processing }"
                >
                    <DialogHeader>
                        <DialogTitle>Edit return policy</DialogTitle>
                        <DialogDescription>
                            Update the return window customers see at checkout.
                        </DialogDescription>
                    </DialogHeader>

                    <div class="grid gap-2">
                        <Label for="edit-title">Title</Label>
                        <Input
                            id="edit-title"
                            name="title"
                            :default-value="editingPolicy.title"
                            required
                            autofocus
                        />
                        <InputError :message="errors.title" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-description">Description</Label>
                        <Textarea
                            id="edit-description"
                            name="description"
                            :default-value="editingPolicy.description"
                            rows="4"
                            required
                        />
                        <InputError :message="errors.description" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-days_allowed">Days allowed</Label>
                        <Input
                            id="edit-days_allowed"
                            name="days_allowed"
                            type="number"
                            min="0"
                            max="365"
                            :default-value="editingPolicy.days_allowed"
                            class="max-w-32"
                        />
                        <InputError :message="errors.days_allowed" />
                    </div>

                    <div class="flex items-center gap-2">
                        <Checkbox
                            id="edit-status"
                            name="status"
                            value="1"
                            :default-value="editingPolicy.status"
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

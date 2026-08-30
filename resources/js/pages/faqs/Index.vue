<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import FaqController from '@/actions/App/Http/Controllers/Backend/FaqController';
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
import { index } from '@/routes/faqs';

type Faq = {
    id: number;
    question: string;
    answer: string;
    sort_order: number;
    status: boolean;
};

defineProps<{
    faqs: Faq[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'FAQs',
                href: index(),
            },
        ],
    },
});

const { can } = usePermissions();

const createOpen = ref(false);
const editingFaq = ref<Faq | null>(null);
</script>

<template>
    <Head title="FAQs" />

    <div class="flex flex-col gap-6">
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="FAQs"
                description="Manage the frequently asked questions shown to customers"
            />

            <template v-if="can('create faqs')">
                <Dialog v-model:open="createOpen">
                    <DialogTrigger as-child>
                        <Button>
                            <Plus />
                            Add FAQ
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <Form
                            v-bind="FaqController.store.form()"
                            reset-on-success
                            @success="createOpen = false"
                            class="space-y-6"
                            v-slot="{ errors, processing }"
                        >
                            <DialogHeader>
                                <DialogTitle>Add FAQ</DialogTitle>
                                <DialogDescription>
                                    Create a new frequently asked question.
                                </DialogDescription>
                            </DialogHeader>

                            <div class="grid gap-2">
                                <Label for="create-question">Question</Label>
                                <Textarea
                                    id="create-question"
                                    name="question"
                                    required
                                    autofocus
                                    placeholder="What is your return policy?"
                                />
                                <InputError :message="errors.question" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="create-answer">Answer</Label>
                                <Textarea
                                    id="create-answer"
                                    name="answer"
                                    required
                                    placeholder="Explain the answer here"
                                />
                                <InputError :message="errors.answer" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="create-sort_order"
                                    >Sort order</Label
                                >
                                <Input
                                    id="create-sort_order"
                                    name="sort_order"
                                    type="number"
                                    min="0"
                                    default-value="0"
                                />
                                <InputError :message="errors.sort_order" />
                            </div>

                            <div class="flex items-center gap-2">
                                <Checkbox
                                    id="create-status"
                                    name="status"
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
                        <th class="p-3 font-medium">Question</th>
                        <th class="p-3 font-medium">Answer</th>
                        <th class="p-3 font-medium">Order</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="faq in faqs"
                        :key="faq.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="max-w-xs p-3 font-medium">
                            <p class="line-clamp-2">{{ faq.question }}</p>
                        </td>
                        <td class="max-w-md p-3 text-muted-foreground">
                            <p class="line-clamp-2">{{ faq.answer }}</p>
                        </td>
                        <td class="p-3">{{ faq.sort_order }}</td>
                        <td class="p-3">
                            <Badge
                                :variant="faq.status ? 'default' : 'secondary'"
                            >
                                {{ faq.status ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button
                                    v-if="can('edit faqs')"
                                    variant="outline"
                                    size="icon-sm"
                                    @click="editingFaq = faq"
                                >
                                    <Pencil />
                                    <span class="sr-only">Edit</span>
                                </Button>

                                <Dialog v-if="can('delete faqs')">
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
                                                FaqController.destroy.form(faq)
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        faq.question
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

                    <tr v-if="faqs.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="5"
                        >
                            No FAQs yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Dialog
            :open="editingFaq !== null"
            @update:open="(open) => !open && (editingFaq = null)"
        >
            <DialogContent v-if="editingFaq">
                <Form
                    v-bind="FaqController.update.form({ faq: editingFaq.id })"
                    @success="editingFaq = null"
                    class="space-y-6"
                    v-slot="{ errors, processing }"
                >
                    <DialogHeader>
                        <DialogTitle>Edit FAQ</DialogTitle>
                        <DialogDescription>
                            Update the FAQ details.
                        </DialogDescription>
                    </DialogHeader>

                    <div class="grid gap-2">
                        <Label for="edit-question">Question</Label>
                        <Textarea
                            id="edit-question"
                            name="question"
                            required
                            :default-value="editingFaq.question"
                        />
                        <InputError :message="errors.question" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-answer">Answer</Label>
                        <Textarea
                            id="edit-answer"
                            name="answer"
                            required
                            :default-value="editingFaq.answer"
                        />
                        <InputError :message="errors.answer" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-sort_order">Sort order</Label>
                        <Input
                            id="edit-sort_order"
                            name="sort_order"
                            type="number"
                            min="0"
                            :default-value="editingFaq.sort_order"
                        />
                        <InputError :message="errors.sort_order" />
                    </div>

                    <div class="flex items-center gap-2">
                        <Checkbox
                            id="edit-status"
                            name="status"
                            :default-value="editingFaq.status"
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

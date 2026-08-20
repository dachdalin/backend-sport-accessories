<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import FaqController from '@/actions/App/Http/Controllers/Backend/FaqController';
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
import { create, edit, index } from '@/routes/faqs';

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
</script>

<template>
    <Head title="FAQs" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="FAQs"
                description="Manage the frequently asked questions shown to customers"
            />
            <Button as-child>
                <Link :href="create()">Add FAQ</Link>
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
                        <th class="p-3 font-medium">Question</th>
                        <th class="p-3 font-medium">Answer</th>
                        <th class="p-3 font-medium">Order</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="faq in faqs"
                        :key="faq.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="max-w-xs p-3 font-medium">
                            {{ faq.question }}
                        </td>
                        <td class="max-w-md truncate p-3 text-muted-foreground">
                            {{ faq.answer }}
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
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(faq)">Edit</Link>
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
    </div>
</template>

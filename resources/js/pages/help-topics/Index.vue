<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import HelpTopicController from '@/actions/App/Http/Controllers/Backend/HelpTopicController';
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
import { create, edit, index } from '@/routes/help-topics';

type HelpTopic = {
    id: number;
    type: string;
    question: string;
    answer: string;
    ranking: number;
    status: boolean;
};

defineProps<{
    helpTopics: HelpTopic[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Help topics',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Help topics" />

    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <Heading
                title="Help topics"
                description="Manage the frequently asked questions shown to customers"
            />
            <Button as-child>
                <Link :href="create()">Add help topic</Link>
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
                        <th class="p-3 font-medium">Type</th>
                        <th class="p-3 font-medium">Ranking</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="helpTopic in helpTopics"
                        :key="helpTopic.id"
                        class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                    >
                        <td class="max-w-md truncate p-3 font-medium">
                            {{ helpTopic.question }}
                        </td>
                        <td class="p-3">{{ helpTopic.type }}</td>
                        <td class="p-3">{{ helpTopic.ranking }}</td>
                        <td class="p-3">
                            <Badge
                                :variant="
                                    helpTopic.status ? 'default' : 'secondary'
                                "
                            >
                                {{ helpTopic.status ? 'Active' : 'Inactive' }}
                            </Badge>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(helpTopic)">Edit</Link>
                                </Button>

                                <Dialog>
                                    <DialogTrigger as-child>
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            >Delete</Button
                                        >
                                    </DialogTrigger>
                                    <DialogContent>
                                        <Form
                                            v-bind="
                                                HelpTopicController.destroy.form(
                                                    helpTopic,
                                                )
                                            "
                                            :options="{ preserveScroll: true }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete this help
                                                    topic?</DialogTitle
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

                    <tr v-if="helpTopics.length === 0">
                        <td
                            class="p-6 text-center text-muted-foreground"
                            colspan="5"
                        >
                            No help topics yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

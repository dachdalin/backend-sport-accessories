<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ChevronDownIcon } from '@lucide/vue';
import { computed, reactive } from 'vue';
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

const props = defineProps<{
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

const openIds = reactive(new Set<number>());

function toggle(id: number): void {
    if (openIds.has(id)) {
        openIds.delete(id);
    } else {
        openIds.add(id);
    }
}

function formatType(type: string): string {
    const label = type.trim() || 'general';

    return label.charAt(0).toUpperCase() + label.slice(1);
}

const groups = computed(() => {
    const byType = new Map<string, HelpTopic[]>();

    for (const topic of props.helpTopics) {
        const key = formatType(topic.type);

        if (!byType.has(key)) {
            byType.set(key, []);
        }

        byType.get(key)!.push(topic);
    }

    return Array.from(byType.entries()).map(([type, topics]) => ({
        type,
        topics,
    }));
});
</script>

<template>
    <Head title="Help topics" />

    <div class="flex flex-col gap-6">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <Heading
                title="Help topics"
                description="Manage the frequently asked questions shown to customers"
            />
            <Button as-child class="w-full sm:w-auto">
                <Link :href="create()">Add help topic</Link>
            </Button>
        </div>

        <p
            v-if="helpTopics.length === 0"
            class="rounded-xl border border-sidebar-border/70 p-6 text-center text-sm text-muted-foreground dark:border-sidebar-border"
        >
            No help topics yet.
        </p>

        <div
            v-else
            class="rounded-xl border border-sidebar-border/70 p-4 sm:p-6 dark:border-sidebar-border"
        >
            <section
                v-for="(group, groupIndex) in groups"
                :key="group.type"
                :class="groupIndex > 0 ? 'mt-6' : ''"
            >
                <div class="mb-3 flex items-center gap-2">
                    <h2
                        class="text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                    >
                        {{ group.type }}
                    </h2>
                    <span class="text-xs text-muted-foreground"
                        >({{ group.topics.length }})</span
                    >
                </div>

                <div class="flex flex-col gap-2">
                    <div
                        v-for="topic in group.topics"
                        :key="topic.id"
                        class="overflow-hidden rounded-lg border border-sidebar-border/70 dark:border-sidebar-border"
                    >
                        <div
                            class="flex flex-wrap items-start justify-between gap-3 p-3"
                        >
                            <button
                                type="button"
                                class="flex min-w-0 flex-1 items-start gap-2 rounded-sm text-left focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                                :aria-expanded="openIds.has(topic.id)"
                                @click="toggle(topic.id)"
                            >
                                <ChevronDownIcon
                                    class="mt-0.5 size-4 shrink-0 text-muted-foreground transition-transform"
                                    :class="{
                                        'rotate-180': openIds.has(topic.id),
                                    }"
                                    aria-hidden="true"
                                />
                                <span class="flex flex-wrap items-center gap-2">
                                    <span class="font-medium">{{
                                        topic.question
                                    }}</span>
                                    <Badge variant="outline" class="shrink-0"
                                        >#{{ topic.ranking }}</Badge
                                    >
                                </span>
                            </button>

                            <div class="flex shrink-0 items-center gap-2">
                                <Badge
                                    :variant="
                                        topic.status ? 'default' : 'secondary'
                                    "
                                >
                                    {{ topic.status ? 'Active' : 'Inactive' }}
                                </Badge>
                                <Button variant="outline" size="sm" as-child>
                                    <Link :href="edit(topic)">Edit</Link>
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
                                                HelpTopicController.destroy.form(
                                                    topic,
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
                        </div>

                        <div
                            v-if="openIds.has(topic.id)"
                            class="border-t border-sidebar-border/70 px-3 py-3 pl-9 text-sm text-muted-foreground dark:border-sidebar-border"
                        >
                            {{ topic.answer }}
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

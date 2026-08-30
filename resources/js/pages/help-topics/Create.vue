<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import {
    ChevronDownIcon,
    EyeIcon,
    FolderIcon,
    MessageCircleQuestionIcon,
    SendIcon,
} from '@lucide/vue';
import { ref } from 'vue';
import HelpTopicController from '@/actions/App/Http/Controllers/Backend/HelpTopicController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import { create, index } from '@/routes/help-topics';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Help topics',
                href: index(),
            },
            {
                title: 'Add help topic',
                href: create(),
            },
        ],
    },
});

const question = ref('');
const answer = ref('');
const status = ref(true);
const previewOpen = ref(true);
</script>

<template>
    <Head title="Add help topic" />

    <div class="flex flex-col gap-6">
        <Heading
            title="Add help topic"
            description="Create a new frequently asked question"
        />

        <Form
            v-bind="HelpTopicController.store.form()"
            class="grid gap-6 lg:grid-cols-3 lg:items-start"
            v-slot="{ errors, processing }"
        >
            <div class="flex flex-col gap-6 lg:col-span-2">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <MessageCircleQuestionIcon
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Question &amp; answer</CardTitle>
                        </div>
                        <CardDescription
                            >What customers ask, and the answer they'll see when
                            they expand it.</CardDescription
                        >
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div class="grid gap-2">
                            <Label for="question">Question</Label>
                            <Textarea
                                id="question"
                                v-model="question"
                                name="question"
                                required
                                autofocus
                                rows="2"
                                placeholder="How do I track my order?"
                            />
                            <InputError :message="errors.question" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="answer">Answer</Label>
                            <Textarea
                                id="answer"
                                v-model="answer"
                                name="answer"
                                required
                                rows="6"
                                placeholder="You can track your order from..."
                            />
                            <InputError :message="errors.answer" />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <FolderIcon
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Organization</CardTitle>
                        </div>
                        <CardDescription
                            >How this topic is grouped and ordered among the
                            others.</CardDescription
                        >
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="type">Type</Label>
                                <Input
                                    id="type"
                                    name="type"
                                    default-value="default"
                                    placeholder="shipping, returns, account…"
                                />
                                <p class="text-xs text-muted-foreground">
                                    Groups related questions together.
                                </p>
                                <InputError :message="errors.type" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="ranking">Ranking</Label>
                                <Input
                                    id="ranking"
                                    name="ranking"
                                    type="number"
                                    min="1"
                                    default-value="1"
                                />
                                <p class="text-xs text-muted-foreground">
                                    Lower numbers show first.
                                </p>
                                <InputError :message="errors.ranking" />
                            </div>
                        </div>

                        <label
                            for="status"
                            class="flex items-center gap-2.5 rounded-lg border border-input px-3 py-2.5 has-[:checked]:border-primary/50 has-[:checked]:bg-primary/5"
                        >
                            <Checkbox
                                id="status"
                                name="status"
                                v-model="status"
                            />
                            <span class="grid gap-0.5">
                                <span class="text-sm font-medium">Active</span>
                                <span class="text-xs text-muted-foreground"
                                    >Shown to customers as soon as it's
                                    saved.</span
                                >
                            </span>
                        </label>
                        <InputError :message="errors.status" />
                    </CardContent>
                </Card>
            </div>

            <div class="flex flex-col gap-6">
                <Card class="lg:sticky lg:top-6">
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <EyeIcon
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Preview</CardTitle>
                        </div>
                        <CardDescription
                            >How this reads on the customer-facing
                            FAQ.</CardDescription
                        >
                    </CardHeader>
                    <CardContent>
                        <div
                            class="overflow-hidden rounded-lg border border-input"
                        >
                            <button
                                type="button"
                                class="flex w-full items-center justify-between gap-3 p-3 text-left text-sm font-medium focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                                :aria-expanded="previewOpen"
                                @click="previewOpen = !previewOpen"
                            >
                                <span
                                    :class="{
                                        'text-muted-foreground italic':
                                            !question,
                                    }"
                                >
                                    {{
                                        question || 'Your question appears here'
                                    }}
                                </span>
                                <ChevronDownIcon
                                    class="size-4 shrink-0 text-muted-foreground transition-transform"
                                    :class="{ 'rotate-180': previewOpen }"
                                    aria-hidden="true"
                                />
                            </button>
                            <div
                                v-if="previewOpen"
                                class="border-t border-input px-3 py-3 text-sm text-muted-foreground"
                            >
                                {{ answer || 'Your answer appears here.' }}
                            </div>
                        </div>
                        <Badge
                            :variant="status ? 'default' : 'secondary'"
                            class="mt-3"
                        >
                            {{ status ? 'Active' : 'Inactive' }}
                        </Badge>
                    </CardContent>
                </Card>

                <Card>
                    <CardFooter class="flex-col gap-3 pt-6 sm:flex-row">
                        <Button class="w-full sm:w-auto" :disabled="processing">
                            <Spinner v-if="processing" />
                            <SendIcon
                                v-else
                                class="size-4"
                                aria-hidden="true"
                            />
                            Create help topic
                        </Button>
                        <Button
                            class="w-full sm:w-auto"
                            variant="outline"
                            as-child
                        >
                            <Link :href="index()">Cancel</Link>
                        </Button>
                    </CardFooter>
                </Card>
            </div>
        </Form>
    </div>
</template>

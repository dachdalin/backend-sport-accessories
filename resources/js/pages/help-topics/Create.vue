<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import HelpTopicController from '@/actions/App/Http/Controllers/Backend/HelpTopicController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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

const textareaClass =
    'placeholder:text-muted-foreground dark:bg-input/30 border-input min-h-24 w-full rounded-md border bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] md:text-sm';
</script>

<template>
    <Head title="Add help topic" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add help topic"
            description="Create a new frequently asked question"
        />

        <Form
            v-bind="HelpTopicController.store.form()"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="question">Question</Label>
                <textarea
                    id="question"
                    name="question"
                    required
                    autofocus
                    :class="textareaClass"
                    placeholder="How do I track my order?"
                />
                <InputError :message="errors.question" />
            </div>

            <div class="grid gap-2">
                <Label for="answer">Answer</Label>
                <textarea
                    id="answer"
                    name="answer"
                    required
                    :class="textareaClass"
                    placeholder="You can track your order from..."
                />
                <InputError :message="errors.answer" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="type">Type</Label>
                    <Input
                        id="type"
                        name="type"
                        default-value="default"
                        placeholder="default"
                    />
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
                    <InputError :message="errors.ranking" />
                </div>
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" :default-value="true" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create help topic</Button>
            </div>
        </Form>
    </div>
</template>

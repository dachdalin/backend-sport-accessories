<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import FaqController from '@/actions/App/Http/Controllers/Backend/FaqController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { create, index } from '@/routes/faqs';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'FAQs',
                href: index(),
            },
            {
                title: 'Add FAQ',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add FAQ" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add FAQ"
            description="Create a new frequently asked question"
        />

        <Form
            v-bind="FaqController.store.form()"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="question">Question</Label>
                <Textarea
                    id="question"
                    name="question"
                    required
                    autofocus
                    placeholder="What is your return policy?"
                />
                <InputError :message="errors.question" />
            </div>

            <div class="grid gap-2">
                <Label for="answer">Answer</Label>
                <Textarea
                    id="answer"
                    name="answer"
                    required
                    placeholder="Explain the answer here"
                />
                <InputError :message="errors.answer" />
            </div>

            <div class="grid gap-2">
                <Label for="sort_order">Sort order</Label>
                <Input
                    id="sort_order"
                    name="sort_order"
                    type="number"
                    min="0"
                    default-value="0"
                />
                <InputError :message="errors.sort_order" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" :default-value="true" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create FAQ</Button>
            </div>
        </Form>
    </div>
</template>

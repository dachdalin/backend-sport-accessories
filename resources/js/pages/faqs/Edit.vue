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
import { edit, index } from '@/routes/faqs';

type Faq = {
    id: number;
    question: string;
    answer: string;
    sort_order: number;
    status: boolean;
};

const props = defineProps<{
    faq: Faq;
}>();

defineOptions({
    layout: (pageProps: { faq: Faq }) => ({
        breadcrumbs: [
            {
                title: 'FAQs',
                href: index(),
            },
            {
                title: 'Edit FAQ',
                href: edit(pageProps.faq),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit FAQ" />

    <div class="flex flex-col space-y-6">
        <Heading title="Edit FAQ" description="Update the FAQ details" />

        <Form
            v-bind="FaqController.update.form(props.faq)"
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
                    :default-value="props.faq.question"
                />
                <InputError :message="errors.question" />
            </div>

            <div class="grid gap-2">
                <Label for="answer">Answer</Label>
                <Textarea
                    id="answer"
                    name="answer"
                    required
                    :default-value="props.faq.answer"
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
                    :default-value="props.faq.sort_order"
                />
                <InputError :message="errors.sort_order" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="props.faq.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save FAQ</Button>
            </div>
        </Form>
    </div>
</template>

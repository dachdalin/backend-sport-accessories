<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import EmailTemplateController from '@/actions/App/Http/Controllers/Backend/EmailTemplateController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { edit, index } from '@/routes/email-templates';

type EmailTemplate = {
    id: number;
    name: string;
    subject: string;
    body: string;
    status: boolean;
};

const props = defineProps<{
    emailTemplate: EmailTemplate;
}>();

defineOptions({
    layout: (pageProps: { emailTemplate: EmailTemplate }) => ({
        breadcrumbs: [
            {
                title: 'Email templates',
                href: index(),
            },
            {
                title: 'Edit email template',
                href: edit(pageProps.emailTemplate),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit email template" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit email template"
            :description="`Update the details for ${props.emailTemplate.name}`"
        />

        <Form
            v-bind="EmailTemplateController.update.form(props.emailTemplate)"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    name="name"
                    required
                    autofocus
                    :default-value="props.emailTemplate.name"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="subject">Subject</Label>
                <Input
                    id="subject"
                    name="subject"
                    required
                    :default-value="props.emailTemplate.subject"
                />
                <InputError :message="errors.subject" />
            </div>

            <div class="grid gap-2">
                <Label for="body">Body</Label>
                <Textarea
                    id="body"
                    name="body"
                    required
                    :default-value="props.emailTemplate.body"
                    rows="10"
                />
                <InputError :message="errors.body" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="props.emailTemplate.status"
                />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save email template</Button>
            </div>
        </Form>
    </div>
</template>

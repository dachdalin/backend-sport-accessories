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
import { create, index } from '@/routes/email-templates';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Email templates',
                href: index(),
            },
            {
                title: 'Add email template',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add email template" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add email template"
            description="Create a new transactional email template"
        />

        <Form
            v-bind="EmailTemplateController.store.form()"
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
                    placeholder="Order confirmation"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="subject">Subject</Label>
                <Input
                    id="subject"
                    name="subject"
                    required
                    placeholder="Your order has been confirmed"
                />
                <InputError :message="errors.subject" />
            </div>

            <div class="grid gap-2">
                <Label for="body">Body</Label>
                <Textarea
                    id="body"
                    name="body"
                    required
                    placeholder="Write the email content"
                    rows="10"
                />
                <InputError :message="errors.body" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" :default-value="true" />
                <Label for="status">Active</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create email template</Button>
            </div>
        </Form>
    </div>
</template>

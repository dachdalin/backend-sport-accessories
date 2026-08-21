<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import ContactController from '@/actions/App/Http/Controllers/Backend/ContactController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { edit, index } from '@/routes/contacts';

type Contact = {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    subject: string;
    message: string;
    reply: string | null;
    status: boolean;
};

const props = defineProps<{
    contact: Contact;
}>();

defineOptions({
    layout: (pageProps: { contact: Contact }) => ({
        breadcrumbs: [
            {
                title: 'Contacts',
                href: index(),
            },
            {
                title: 'Edit message',
                href: edit(pageProps.contact),
            },
        ],
    }),
});
</script>

<template>
    <Head title="Edit message" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Edit message"
            :description="`Update the message from ${props.contact.name}`"
        />

        <Form
            v-bind="ContactController.update.form(props.contact)"
            class="max-w-xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        name="name"
                        required
                        autofocus
                        :default-value="props.contact.name"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        name="email"
                        type="email"
                        required
                        :default-value="props.contact.email"
                    />
                    <InputError :message="errors.email" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="phone">Phone</Label>
                <Input
                    id="phone"
                    name="phone"
                    :default-value="props.contact.phone ?? ''"
                    placeholder="+1 555 000 0000"
                />
                <InputError :message="errors.phone" />
            </div>

            <div class="grid gap-2">
                <Label for="subject">Subject</Label>
                <Input
                    id="subject"
                    name="subject"
                    required
                    :default-value="props.contact.subject"
                />
                <InputError :message="errors.subject" />
            </div>

            <div class="grid gap-2">
                <Label for="message">Message</Label>
                <Textarea
                    id="message"
                    name="message"
                    required
                    :default-value="props.contact.message"
                    rows="4"
                />
                <InputError :message="errors.message" />
            </div>

            <div class="grid gap-2">
                <Label for="reply">Reply</Label>
                <Textarea
                    id="reply"
                    name="reply"
                    :default-value="props.contact.reply ?? ''"
                    placeholder="Optional reply sent back to the customer"
                    rows="3"
                />
                <InputError :message="errors.reply" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox
                    id="status"
                    name="status"
                    :default-value="props.contact.status"
                />
                <Label for="status">Read</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save message</Button>
            </div>
        </Form>
    </div>
</template>

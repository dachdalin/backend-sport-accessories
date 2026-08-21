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
import { create, index } from '@/routes/contacts';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Contacts',
                href: index(),
            },
            {
                title: 'Add message',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add message" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add message"
            description="Record a message received from a customer"
        />

        <Form
            v-bind="ContactController.store.form()"
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
                        placeholder="Jane Doe"
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
                        placeholder="jane@example.com"
                    />
                    <InputError :message="errors.email" />
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="phone">Phone</Label>
                <Input
                    id="phone"
                    name="phone"
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
                    placeholder="Question about an order"
                />
                <InputError :message="errors.subject" />
            </div>

            <div class="grid gap-2">
                <Label for="message">Message</Label>
                <Textarea
                    id="message"
                    name="message"
                    required
                    placeholder="What the customer wrote"
                    rows="4"
                />
                <InputError :message="errors.message" />
            </div>

            <div class="grid gap-2">
                <Label for="reply">Reply</Label>
                <Textarea
                    id="reply"
                    name="reply"
                    placeholder="Optional reply sent back to the customer"
                    rows="3"
                />
                <InputError :message="errors.reply" />
            </div>

            <div class="flex items-center gap-2">
                <Checkbox id="status" name="status" />
                <Label for="status">Read</Label>
                <InputError :message="errors.status" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Create message</Button>
            </div>
        </Form>
    </div>
</template>

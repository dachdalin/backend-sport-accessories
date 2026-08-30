<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import SupportTicketController from '@/actions/App/Http/Controllers/Backend/SupportTicketController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { create, index } from '@/routes/support-tickets';

interface SelectOption {
    value: number | string;
    label: string;
}

defineProps<{
    customers: SelectOption[];
    priorities: SelectOption[];
    statuses: SelectOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Support tickets',
                href: index(),
            },
            {
                title: 'Add ticket',
                href: create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Add support ticket" />

    <div class="flex flex-col space-y-6">
        <Heading
            title="Add support ticket"
            description="Log a support request on behalf of a customer"
        />

        <Form
            v-bind="SupportTicketController.store.form()"
            class="max-w-2xl space-y-6"
            v-slot="{ errors, processing }"
        >
            <div
                class="flex flex-col gap-4 rounded-xl border border-sidebar-border/70 p-4 sm:p-5 dark:border-sidebar-border"
            >
                <h2 class="text-sm font-semibold text-foreground">
                    Ticket details
                </h2>

                <div class="grid gap-2">
                    <Label for="customer_id">Customer</Label>
                    <Select name="customer_id">
                        <SelectTrigger id="customer_id" class="w-full">
                            <SelectValue placeholder="Select customer" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="option in customers"
                                :key="option.value"
                                :value="String(option.value)"
                            >
                                {{ option.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.customer_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="subject">Subject</Label>
                    <Input
                        id="subject"
                        name="subject"
                        required
                        autofocus
                        placeholder="Order hasn't arrived"
                    />
                    <InputError :message="errors.subject" />
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="type">Type</Label>
                        <Input
                            id="type"
                            name="type"
                            placeholder="billing, technical, general..."
                        />
                        <InputError :message="errors.type" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="priority">Priority</Label>
                        <Select name="priority" default-value="low">
                            <SelectTrigger id="priority" class="w-full">
                                <SelectValue placeholder="Select priority" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="option in priorities"
                                    :key="option.value"
                                    :value="String(option.value)"
                                >
                                    {{ option.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.priority" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="description">Description</Label>
                    <Textarea
                        id="description"
                        name="description"
                        required
                        placeholder="What the customer is reporting"
                        rows="4"
                    />
                    <InputError :message="errors.description" />
                </div>

                <div class="grid gap-2">
                    <Label for="attachment">Attachment</Label>
                    <Input
                        id="attachment"
                        name="attachment"
                        type="file"
                        accept="image/*,application/pdf"
                    />
                    <InputError :message="errors.attachment" />
                </div>
            </div>

            <div
                class="flex flex-col gap-4 rounded-xl border border-sidebar-border/70 p-4 sm:p-5 dark:border-sidebar-border"
            >
                <h2 class="text-sm font-semibold text-foreground">
                    Resolution
                </h2>

                <div class="grid gap-2">
                    <Label for="reply">Reply</Label>
                    <Textarea
                        id="reply"
                        name="reply"
                        placeholder="Optional reply back to the customer"
                        rows="3"
                    />
                    <InputError :message="errors.reply" />
                </div>

                <div class="grid gap-2">
                    <Label for="status">Status</Label>
                    <Select name="status" default-value="open">
                        <SelectTrigger id="status" class="w-full">
                            <SelectValue placeholder="Select status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="option in statuses"
                                :key="option.value"
                                :value="String(option.value)"
                            >
                                {{ option.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.status" />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button class="w-full sm:w-auto" :disabled="processing">
                    Create ticket
                </Button>
            </div>
        </Form>
    </div>
</template>

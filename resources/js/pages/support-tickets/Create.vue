<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { Flag, MessageSquare, Paperclip, Send, Ticket } from '@lucide/vue';
import SupportTicketController from '@/actions/App/Http/Controllers/Backend/SupportTicketController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
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

/**
 * Shared active-state styling for the priority/status pill pickers below,
 * keyed by enum value so it matches the badge colors used on the index page.
 */
const pillStyles: Record<string, string> = {
    low: 'has-[:checked]:border-muted-foreground has-[:checked]:bg-muted has-[:checked]:text-foreground',
    open: 'has-[:checked]:border-muted-foreground has-[:checked]:bg-muted has-[:checked]:text-foreground',
    medium: 'has-[:checked]:border-primary has-[:checked]:bg-primary/5 has-[:checked]:text-primary',
    answered:
        'has-[:checked]:border-primary has-[:checked]:bg-primary/5 has-[:checked]:text-primary',
    high: 'has-[:checked]:border-destructive has-[:checked]:bg-destructive/10 has-[:checked]:text-destructive',
    closed: 'has-[:checked]:border-destructive has-[:checked]:bg-destructive/10 has-[:checked]:text-destructive',
};
</script>

<template>
    <Head title="Add support ticket" />

    <div class="flex flex-col gap-6">
        <Heading
            title="Add support ticket"
            description="Log a support request on behalf of a customer"
        />

        <Form
            v-bind="SupportTicketController.store.form()"
            class="grid gap-6 lg:grid-cols-3 lg:items-start"
            v-slot="{ errors, processing }"
        >
            <div class="flex flex-col gap-6 lg:col-span-2">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Ticket
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Ticket details</CardTitle>
                        </div>
                        <CardDescription>
                            What the customer is reporting.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
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
                                maxlength="150"
                                placeholder="Order hasn't arrived"
                            />
                            <InputError :message="errors.subject" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="type">Type</Label>
                            <Input
                                id="type"
                                name="type"
                                maxlength="50"
                                placeholder="billing, technical, general..."
                            />
                            <InputError :message="errors.type" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="description">Description</Label>
                            <Textarea
                                id="description"
                                name="description"
                                required
                                maxlength="5000"
                                placeholder="What the customer is reporting"
                                rows="5"
                            />
                            <InputError :message="errors.description" />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Paperclip
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Attachment</CardTitle>
                        </div>
                        <CardDescription>
                            A screenshot or document the customer sent in,
                            if any.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex flex-col gap-2">
                        <Label for="attachment">File</Label>
                        <Input
                            id="attachment"
                            name="attachment"
                            type="file"
                            accept="image/jpeg,image/png,image/webp,application/pdf"
                        />
                        <p class="text-xs text-muted-foreground">
                            JPG, PNG, WEBP, or PDF — up to 5MB.
                        </p>
                        <InputError :message="errors.attachment" />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <MessageSquare
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Reply</CardTitle>
                        </div>
                        <CardDescription>
                            Your response to the customer — visible to them
                            as soon as you save.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-2">
                        <Label for="reply">Reply</Label>
                        <Textarea
                            id="reply"
                            name="reply"
                            maxlength="5000"
                            placeholder="Optional reply back to the customer"
                            rows="3"
                        />
                        <InputError :message="errors.reply" />
                    </CardContent>
                </Card>
            </div>

            <div class="flex flex-col gap-6">
                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Flag
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Priority</CardTitle>
                        </div>
                        <CardDescription>
                            How urgently this needs attention.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-3 gap-2">
                            <label
                                v-for="option in priorities"
                                :key="option.value"
                                :class="[
                                    'group relative flex cursor-pointer flex-col items-center gap-1.5 rounded-lg border border-input px-2 py-3 text-center text-muted-foreground transition-colors hover:bg-accent/40 has-[:checked]:font-semibold',
                                    pillStyles[option.value],
                                ]"
                            >
                                <input
                                    type="radio"
                                    name="priority"
                                    class="absolute inset-0 z-10 h-full w-full cursor-pointer appearance-none rounded-lg opacity-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                    :value="option.value"
                                    :checked="option.value === 'low'"
                                />
                                <Flag
                                    class="size-4 transition-colors group-has-[:checked]:text-current"
                                    aria-hidden="true"
                                />
                                <span class="text-xs font-medium sm:text-sm">{{
                                    option.label
                                }}</span>
                            </label>
                        </div>
                        <InputError :message="errors.priority" />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Ticket
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Status</CardTitle>
                        </div>
                        <CardDescription>
                            Where this ticket sits in the queue.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-3 gap-2">
                            <label
                                v-for="option in statuses"
                                :key="option.value"
                                :class="[
                                    'group relative flex cursor-pointer flex-col items-center gap-1.5 rounded-lg border border-input px-2 py-3 text-center text-muted-foreground transition-colors hover:bg-accent/40 has-[:checked]:font-semibold',
                                    pillStyles[option.value],
                                ]"
                            >
                                <input
                                    type="radio"
                                    name="status"
                                    class="absolute inset-0 z-10 h-full w-full cursor-pointer appearance-none rounded-lg opacity-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                    :value="option.value"
                                    :checked="option.value === 'open'"
                                />
                                <span
                                    class="size-2 rounded-full bg-muted-foreground/60 transition-colors group-has-[:checked]:bg-current"
                                    aria-hidden="true"
                                />
                                <span class="text-xs font-medium sm:text-sm">{{
                                    option.label
                                }}</span>
                            </label>
                        </div>
                        <InputError :message="errors.status" />
                    </CardContent>
                </Card>

                <Card class="lg:sticky lg:top-6">
                    <CardHeader>
                        <div class="flex items-center gap-2.5">
                            <Send
                                class="size-4.5 text-muted-foreground"
                                aria-hidden="true"
                            />
                            <CardTitle>Log ticket</CardTitle>
                        </div>
                        <CardDescription>
                            Save to add this ticket to the queue.
                        </CardDescription>
                    </CardHeader>
                    <CardFooter class="gap-3 border-t pt-6">
                        <Button :disabled="processing">
                            <Spinner v-if="processing" />
                            Create ticket
                        </Button>
                        <Button variant="outline" as-child>
                            <Link :href="index()">Cancel</Link>
                        </Button>
                    </CardFooter>
                </Card>
            </div>
        </Form>
    </div>
</template>

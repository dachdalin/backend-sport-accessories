<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { FileText, Flag, MessageSquare, Paperclip, Send, Ticket } from '@lucide/vue';
import { computed } from 'vue';
import SupportTicketController from '@/actions/App/Http/Controllers/Backend/SupportTicketController';
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
import { edit, index } from '@/routes/support-tickets';

interface SelectOption {
    value: number | string;
    label: string;
}

type SupportTicket = {
    id: number;
    customer_id: number;
    subject: string;
    type: string | null;
    priority: string;
    description: string;
    attachment: string | null;
    attachment_url: string | null;
    reply: string | null;
    status: string;
};

const props = defineProps<{
    supportTicket: SupportTicket;
    customers: SelectOption[];
    priorities: SelectOption[];
    statuses: SelectOption[];
}>();

defineOptions({
    layout: (pageProps: { supportTicket: SupportTicket }) => ({
        breadcrumbs: [
            {
                title: 'Support tickets',
                href: index(),
            },
            {
                title: 'Edit ticket',
                href: edit(pageProps.supportTicket),
            },
        ],
    }),
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

const statusVariant: Record<string, 'default' | 'secondary' | 'destructive'> = {
    open: 'secondary',
    answered: 'default',
    closed: 'destructive',
};

const attachmentName = computed(
    () => props.supportTicket.attachment?.split('/').pop() ?? 'Attachment',
);
</script>

<template>
    <Head title="Edit support ticket" />

    <div class="flex flex-col gap-6">
        <div class="flex flex-wrap items-center gap-3">
            <Heading
                title="Edit support ticket"
                :description="`Update the ticket &quot;${supportTicket.subject}&quot;`"
            />
            <Badge :variant="statusVariant[supportTicket.status] ?? 'secondary'">
                {{ supportTicket.status }}
            </Badge>
        </div>

        <Form
            v-bind="SupportTicketController.update.form(supportTicket)"
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
                            <Select
                                name="customer_id"
                                :default-value="String(supportTicket.customer_id)"
                            >
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
                                :default-value="supportTicket.subject"
                            />
                            <InputError :message="errors.subject" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="type">Type</Label>
                            <Input
                                id="type"
                                name="type"
                                maxlength="50"
                                :default-value="supportTicket.type ?? ''"
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
                                :default-value="supportTicket.description"
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
                    <CardContent class="flex flex-col gap-3">
                        <div
                            v-if="supportTicket.attachment_url"
                            class="flex items-center justify-between gap-3 rounded-lg border border-input bg-muted/30 px-3 py-2.5"
                        >
                            <div class="flex min-w-0 items-center gap-2.5">
                                <FileText
                                    class="size-4 shrink-0 text-muted-foreground"
                                    aria-hidden="true"
                                />
                                <span class="truncate text-sm">{{
                                    attachmentName
                                }}</span>
                            </div>
                            <a
                                :href="supportTicket.attachment_url"
                                target="_blank"
                                rel="noopener"
                                class="shrink-0 text-sm font-medium text-primary hover:underline"
                            >
                                View
                            </a>
                        </div>

                        <div class="grid gap-2">
                            <Label for="attachment">{{
                                supportTicket.attachment_url
                                    ? 'Replace attachment'
                                    : 'File'
                            }}</Label>
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
                        </div>
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
                            :default-value="supportTicket.reply ?? ''"
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
                                    :checked="option.value === supportTicket.priority"
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
                                    :checked="option.value === supportTicket.status"
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
                            <CardTitle>Save changes</CardTitle>
                        </div>
                        <CardDescription>
                            Save to update this ticket.
                        </CardDescription>
                    </CardHeader>
                    <CardFooter class="gap-3 border-t pt-6">
                        <Button :disabled="processing">
                            <Spinner v-if="processing" />
                            Save ticket
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

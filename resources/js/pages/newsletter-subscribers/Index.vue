<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Mail, Pencil, Plus, Send, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import NewsletterSubscriberController from '@/actions/App/Http/Controllers/Backend/NewsletterSubscriberController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import Pagination from '@/components/Pagination.vue';
import RichTextEditor from '@/components/RichTextEditor.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { index as newsletterSubscribersIndex } from '@/routes/newsletter-subscribers';

interface Subscriber {
    id: number;
    email: string;
    status: boolean;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Paginated<T> {
    data: T[];
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
    current_page: number;
    last_page: number;
}

defineProps<{
    subscribers: Paginated<Subscriber>;
    subscribedCount: number;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Newsletter subscribers',
                href: newsletterSubscribersIndex(),
            },
        ],
    },
});

const createOpen = ref(false);
const editingSubscriber = ref<Subscriber | null>(null);
const sendAllOpen = ref(false);
const sendingToSubscriber = ref<Subscriber | null>(null);

function openEdit(subscriber: Subscriber) {
    editingSubscriber.value = subscriber;
}

function openSend(subscriber: Subscriber) {
    sendingToSubscriber.value = subscriber;
}
</script>

<template>
    <Head title="Newsletter subscribers" />

    <div class="flex flex-col gap-6">
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="Newsletter subscribers"
                description="Manage the mailing list for product updates"
            />

            <div class="flex items-center gap-2">
                <Dialog v-model:open="sendAllOpen">
                    <DialogTrigger as-child>
                        <Button
                            variant="outline"
                            :disabled="subscribedCount === 0"
                            :title="
                                subscribedCount === 0
                                    ? 'No subscribed recipients to send to yet'
                                    : undefined
                            "
                        >
                            <Send />
                            Send newsletter
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <Form
                            v-bind="
                                NewsletterSubscriberController.sendAll.form()
                            "
                            reset-on-success
                            @success="sendAllOpen = false"
                            class="space-y-6"
                            v-slot="{ errors, processing }"
                        >
                            <DialogHeader>
                                <DialogTitle>Send newsletter</DialogTitle>
                                <DialogDescription>
                                    Sends to all
                                    {{ subscribedCount }} subscribed
                                    {{
                                        subscribedCount === 1
                                            ? 'address'
                                            : 'addresses'
                                    }}.
                                </DialogDescription>
                            </DialogHeader>

                            <div class="grid gap-2">
                                <Label for="send-all-subject">Subject</Label>
                                <Input
                                    id="send-all-subject"
                                    name="subject"
                                    placeholder="What's new this month"
                                    required
                                    autofocus
                                />
                                <InputError :message="errors.subject" />
                            </div>

                            <RichTextEditor
                                name="body"
                                label="Message"
                                required
                                placeholder="Write the newsletter content"
                                :error="errors.body"
                            />

                            <DialogFooter class="gap-2">
                                <DialogClose as-child>
                                    <Button variant="secondary">Cancel</Button>
                                </DialogClose>
                                <Button type="submit" :disabled="processing">
                                    <Spinner v-if="processing" />
                                    Send to all
                                </Button>
                            </DialogFooter>
                        </Form>
                    </DialogContent>
                </Dialog>

                <Dialog v-model:open="createOpen">
                    <DialogTrigger as-child>
                        <Button>
                            <Plus />
                            Add subscriber
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <Form
                            v-bind="NewsletterSubscriberController.store.form()"
                            reset-on-success
                            @success="createOpen = false"
                            class="space-y-6"
                            v-slot="{ errors, processing }"
                        >
                            <DialogHeader>
                                <DialogTitle>Add subscriber</DialogTitle>
                                <DialogDescription>
                                    Add a new email to the mailing list.
                                </DialogDescription>
                            </DialogHeader>

                            <div class="grid gap-2">
                                <Label for="create-email">Email</Label>
                                <Input
                                    id="create-email"
                                    name="email"
                                    type="email"
                                    placeholder="jane@example.com"
                                    required
                                />
                                <InputError :message="errors.email" />
                            </div>

                            <div class="flex items-center gap-2">
                                <Checkbox
                                    id="create-status"
                                    name="status"
                                    value="1"
                                    :default-value="true"
                                />
                                <Label for="create-status">Subscribed</Label>
                                <InputError :message="errors.status" />
                            </div>

                            <DialogFooter class="gap-2">
                                <DialogClose as-child>
                                    <Button variant="secondary">Cancel</Button>
                                </DialogClose>
                                <Button type="submit" :disabled="processing">
                                    Save
                                </Button>
                            </DialogFooter>
                        </Form>
                    </DialogContent>
                </Dialog>
            </div>
        </div>

        <div
            class="relative overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
        >
            <table class="w-full text-left text-sm">
                <thead
                    class="border-b border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border"
                >
                    <tr>
                        <th class="px-4 py-3 font-medium">Email</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="subscriber in subscribers.data"
                        :key="subscriber.id"
                        class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border"
                    >
                        <td class="px-4 py-3">{{ subscriber.email }}</td>
                        <td class="px-4 py-3">
                            <Badge
                                :variant="
                                    subscriber.status ? 'default' : 'secondary'
                                "
                            >
                                {{
                                    subscriber.status
                                        ? 'Subscribed'
                                        : 'Unsubscribed'
                                }}
                            </Badge>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <Button
                                    variant="outline"
                                    size="icon-sm"
                                    :disabled="!subscriber.status"
                                    :title="
                                        subscriber.status
                                            ? undefined
                                            : 'Subscriber has unsubscribed'
                                    "
                                    @click="openSend(subscriber)"
                                >
                                    <Mail />
                                    <span class="sr-only">Send email</span>
                                </Button>

                                <Button
                                    variant="outline"
                                    size="icon-sm"
                                    @click="openEdit(subscriber)"
                                >
                                    <Pencil />
                                    <span class="sr-only">Edit</span>
                                </Button>

                                <Dialog>
                                    <DialogTrigger as-child>
                                        <Button
                                            variant="destructive"
                                            size="icon-sm"
                                        >
                                            <Trash2 />
                                            <span class="sr-only">Delete</span>
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent>
                                        <Form
                                            v-bind="
                                                NewsletterSubscriberController.destroy.form(
                                                    {
                                                        newsletter_subscriber:
                                                            subscriber.id,
                                                    },
                                                )
                                            "
                                            :options="{
                                                preserveScroll: true,
                                            }"
                                            v-slot="{ processing }"
                                        >
                                            <DialogHeader class="space-y-3">
                                                <DialogTitle
                                                    >Delete "{{
                                                        subscriber.email
                                                    }}"?</DialogTitle
                                                >
                                                <DialogDescription>
                                                    This cannot be undone.
                                                </DialogDescription>
                                            </DialogHeader>

                                            <DialogFooter class="gap-2">
                                                <DialogClose as-child>
                                                    <Button variant="secondary">
                                                        Cancel
                                                    </Button>
                                                </DialogClose>
                                                <Button
                                                    type="submit"
                                                    variant="destructive"
                                                    :disabled="processing"
                                                >
                                                    Delete
                                                </Button>
                                            </DialogFooter>
                                        </Form>
                                    </DialogContent>
                                </Dialog>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="subscribers.data.length === 0">
                        <td
                            colspan="3"
                            class="px-4 py-6 text-center text-muted-foreground"
                        >
                            No subscribers yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :meta="subscribers" label="subscribers" />

        <Dialog
            :open="editingSubscriber !== null"
            @update:open="(open) => !open && (editingSubscriber = null)"
        >
            <DialogContent v-if="editingSubscriber">
                <Form
                    v-bind="
                        NewsletterSubscriberController.update.form({
                            newsletter_subscriber: editingSubscriber.id,
                        })
                    "
                    @success="editingSubscriber = null"
                    class="space-y-6"
                    v-slot="{ errors, processing }"
                >
                    <DialogHeader>
                        <DialogTitle>Edit subscriber</DialogTitle>
                        <DialogDescription>
                            Update the subscriber's email and status.
                        </DialogDescription>
                    </DialogHeader>

                    <div class="grid gap-2">
                        <Label for="edit-email">Email</Label>
                        <Input
                            id="edit-email"
                            name="email"
                            type="email"
                            :default-value="editingSubscriber.email"
                            required
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="flex items-center gap-2">
                        <Checkbox
                            id="edit-status"
                            name="status"
                            value="1"
                            :default-value="editingSubscriber.status"
                        />
                        <Label for="edit-status">Subscribed</Label>
                        <InputError :message="errors.status" />
                    </div>

                    <DialogFooter class="gap-2">
                        <DialogClose as-child>
                            <Button variant="secondary">Cancel</Button>
                        </DialogClose>
                        <Button type="submit" :disabled="processing">
                            Save
                        </Button>
                    </DialogFooter>
                </Form>
            </DialogContent>
        </Dialog>

        <Dialog
            :open="sendingToSubscriber !== null"
            @update:open="(open) => !open && (sendingToSubscriber = null)"
        >
            <DialogContent v-if="sendingToSubscriber">
                <Form
                    v-bind="
                        NewsletterSubscriberController.send.form({
                            newsletter_subscriber: sendingToSubscriber.id,
                        })
                    "
                    reset-on-success
                    @success="sendingToSubscriber = null"
                    class="space-y-6"
                    v-slot="{ errors, processing }"
                >
                    <DialogHeader>
                        <DialogTitle>Send email</DialogTitle>
                        <DialogDescription>
                            Sends to
                            {{ sendingToSubscriber.email }}.
                        </DialogDescription>
                    </DialogHeader>

                    <div class="grid gap-2">
                        <Label for="send-subject">Subject</Label>
                        <Input
                            id="send-subject"
                            name="subject"
                            placeholder="What's new this month"
                            required
                            autofocus
                        />
                        <InputError :message="errors.subject" />
                    </div>

                    <RichTextEditor
                        name="body"
                        label="Message"
                        required
                        placeholder="Write the email content"
                        :error="errors.body"
                    />

                    <DialogFooter class="gap-2">
                        <DialogClose as-child>
                            <Button variant="secondary">Cancel</Button>
                        </DialogClose>
                        <Button type="submit" :disabled="processing">
                            <Spinner v-if="processing" />
                            Send
                        </Button>
                    </DialogFooter>
                </Form>
            </DialogContent>
        </Dialog>
    </div>
</template>

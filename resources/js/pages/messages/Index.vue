<script setup lang="ts">
import { Form, Head, Link, usePoll } from '@inertiajs/vue3';
import { Send } from '@lucide/vue';
import { computed, nextTick, ref, watch } from 'vue';
import MessageController from '@/actions/App/Http/Controllers/Backend/MessageController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { index as messagesIndex } from '@/routes/messages';

interface Teammate {
    id: number;
    name: string;
    email: string;
}

interface LastMessage {
    id: number;
    sender_id: number;
    body: string;
    created_at: string;
}

interface Conversation extends Teammate {
    last_message: LastMessage | null;
    unread_count: number;
}

interface ThreadMessage {
    id: number;
    sender_id: number;
    receiver_id: number;
    body: string;
    created_at: string;
    sender: { id: number; name: string };
}

const props = defineProps<{
    conversations: Conversation[];
    selectedUser: Teammate | null;
    messages: ThreadMessage[];
    currentUserId: number;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Messages',
                href: messagesIndex(),
            },
        ],
    },
});

usePoll(4000, { only: ['conversations', 'messages'] });

function initials(name: string): string {
    return name
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase())
        .join('');
}

function relativeTime(iso: string): string {
    const seconds = Math.floor((Date.now() - new Date(iso).getTime()) / 1000);

    if (seconds < 60) {
        return 'just now';
    }
    if (seconds < 3600) {
        return `${Math.floor(seconds / 60)}m ago`;
    }
    if (seconds < 86400) {
        return `${Math.floor(seconds / 3600)}h ago`;
    }
    if (seconds < 604800) {
        return `${Math.floor(seconds / 86400)}d ago`;
    }

    return new Date(iso).toLocaleDateString();
}

function timeOnly(iso: string): string {
    return new Date(iso).toLocaleTimeString([], {
        hour: 'numeric',
        minute: '2-digit',
    });
}

const groupedMessages = computed(() =>
    props.messages.map((message, i) => {
        const next = props.messages[i + 1];

        return {
            ...message,
            showMeta: !next || next.sender_id !== message.sender_id,
        };
    }),
);

const threadEl = ref<HTMLElement | null>(null);

function scrollToBottom() {
    nextTick(() => {
        if (threadEl.value) {
            threadEl.value.scrollTop = threadEl.value.scrollHeight;
        }
    });
}

watch(() => props.messages, scrollToBottom, { immediate: true });
watch(() => props.selectedUser?.id, scrollToBottom);
</script>

<template>
    <Head title="Messages" />

    <div class="flex flex-col gap-6">
        <Heading
            title="Messages"
            description="Chat with other staff members. Only visible to signed-in admin accounts."
        />

        <div
            class="flex h-[calc(100vh-16rem)] min-h-[28rem] overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
        >
            <aside
                class="w-full shrink-0 overflow-y-auto border-sidebar-border/70 md:w-72 md:border-r dark:border-sidebar-border"
                :class="{ 'hidden md:block': selectedUser }"
            >
                <ul>
                    <li v-if="conversations.length === 0" class="p-4 text-sm text-muted-foreground">
                        No other staff accounts yet.
                    </li>
                    <li v-for="teammate in conversations" :key="teammate.id">
                        <Link
                            :href="messagesIndex({ query: { user: teammate.id } }).url"
                            preserve-scroll
                            class="flex items-center gap-3 border-b border-sidebar-border/70 px-4 py-3 transition-colors hover:bg-accent dark:border-sidebar-border"
                            :class="
                                selectedUser?.id === teammate.id
                                    ? 'bg-accent'
                                    : ''
                            "
                        >
                            <Avatar>
                                <AvatarFallback>{{
                                    initials(teammate.name)
                                }}</AvatarFallback>
                            </Avatar>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="truncate font-medium">{{
                                        teammate.name
                                    }}</span>
                                    <span
                                        v-if="teammate.last_message"
                                        class="shrink-0 text-xs text-muted-foreground"
                                    >
                                        {{
                                            relativeTime(
                                                teammate.last_message
                                                    .created_at,
                                            )
                                        }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <p class="truncate text-sm text-muted-foreground">
                                        <template v-if="teammate.last_message">
                                            <span
                                                v-if="
                                                    teammate.last_message
                                                        .sender_id ===
                                                    currentUserId
                                                "
                                                >You: </span
                                            >{{ teammate.last_message.body }}
                                        </template>
                                        <span v-else class="italic"
                                            >No messages yet</span
                                        >
                                    </p>
                                    <Badge
                                        v-if="teammate.unread_count > 0"
                                        class="shrink-0"
                                    >
                                        {{ teammate.unread_count }}
                                    </Badge>
                                </div>
                            </div>
                        </Link>
                    </li>
                </ul>
            </aside>

            <section
                class="flex min-w-0 flex-1 flex-col"
                :class="{ 'hidden md:flex': !selectedUser }"
            >
                <template v-if="selectedUser">
                    <div
                        class="flex items-center gap-3 border-b border-sidebar-border/70 px-4 py-3 dark:border-sidebar-border"
                    >
                        <Link :href="messagesIndex().url" class="md:hidden">
                            <Button variant="ghost" size="icon-sm">←</Button>
                        </Link>
                        <Avatar>
                            <AvatarFallback>{{
                                initials(selectedUser.name)
                            }}</AvatarFallback>
                        </Avatar>
                        <div>
                            <p class="font-medium">{{ selectedUser.name }}</p>
                            <p class="text-xs text-muted-foreground">
                                {{ selectedUser.email }}
                            </p>
                        </div>
                    </div>

                    <div
                        ref="threadEl"
                        class="flex-1 space-y-1 overflow-y-auto px-4 py-4"
                    >
                        <p
                            v-if="messages.length === 0"
                            class="text-center text-sm text-muted-foreground"
                        >
                            No messages yet — say hello.
                        </p>

                        <div
                            v-for="message in groupedMessages"
                            :key="message.id"
                            class="flex"
                            :class="
                                message.sender_id === currentUserId
                                    ? 'justify-end'
                                    : 'justify-start'
                            "
                        >
                            <div class="max-w-[75%]">
                                <div
                                    class="rounded-2xl px-3.5 py-2 text-sm break-words"
                                    :class="
                                        message.sender_id === currentUserId
                                            ? 'bg-primary text-primary-foreground'
                                            : 'bg-muted'
                                    "
                                >
                                    {{ message.body }}
                                </div>
                                <p
                                    v-if="message.showMeta"
                                    class="mt-0.5 text-xs text-muted-foreground"
                                    :class="
                                        message.sender_id === currentUserId
                                            ? 'text-right'
                                            : 'text-left'
                                    "
                                >
                                    {{ timeOnly(message.created_at) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <Form
                        :key="selectedUser.id"
                        v-bind="MessageController.store.form()"
                        reset-on-success
                        class="flex items-end gap-2 border-t border-sidebar-border/70 p-3 dark:border-sidebar-border"
                        v-slot="{ errors, processing }"
                    >
                        <input
                            type="hidden"
                            name="receiver_id"
                            :value="selectedUser.id"
                        />
                        <div class="flex-1">
                            <Textarea
                                name="body"
                                placeholder="Write a message…"
                                rows="1"
                                class="max-h-32 min-h-10 resize-none"
                                required
                                @keydown.enter.exact.prevent="
                                    ($event.target as HTMLElement)
                                        .closest('form')
                                        ?.requestSubmit()
                                "
                            />
                            <InputError :message="errors.body ?? errors.receiver_id" />
                        </div>
                        <Button type="submit" size="icon" :disabled="processing">
                            <Send />
                            <span class="sr-only">Send</span>
                        </Button>
                    </Form>
                </template>

                <div
                    v-else
                    class="flex flex-1 items-center justify-center p-8 text-center text-sm text-muted-foreground"
                >
                    Select a teammate to start chatting.
                </div>
            </section>
        </div>
    </div>
</template>

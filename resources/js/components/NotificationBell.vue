<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    Bell,
    Inbox,
    LifeBuoy,
    PartyPopper,
    Receipt,
    Star,
    X,
} from '@lucide/vue';
import { computed } from 'vue';
import {
    read,
    readAll,
} from '@/actions/App/Http/Controllers/Backend/NotificationController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useLocale } from '@/composables/useLocale';
import { timeAgo } from '@/lib/timeAgo';
import type { NotificationType } from '@/types/ui';

const page = usePage();
const { locale } = useLocale();

const summary = computed(() => page.props.notifications);
const items = computed(() => summary.value?.items ?? []);
const total = computed(() => summary.value?.total ?? 0);
const markableCount = computed(
    () => items.value.filter((item) => item.id).length,
);
const badgeLabel = computed(() =>
    total.value > 9 ? '9+' : String(total.value),
);

const copy = computed(() =>
    locale.value === 'km'
        ? {
              heading: 'ការជូនដំណឹង',
              empty: 'គ្មានកិច្ចការថ្មីទេ',
              emptyHint: 'ការជូនដំណឹងអំពីហាងរបស់អ្នកនឹងបង្ហាញនៅទីនេះ។',
              markAllRead: 'សម្គាល់ថាបានអានទាំងអស់',
              dismiss: 'សម្គាល់ថាបានអាន',
          }
        : {
              heading: 'Notifications',
              empty: "You're all caught up",
              emptyHint:
                  'Store activity that needs attention will show up here.',
              markAllRead: 'Mark all as read',
              dismiss: 'Mark as read',
          },
);

const typeMeta: Record<
    NotificationType,
    { icon: typeof Receipt; tint: string }
> = {
    order: {
        icon: Receipt,
        tint: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
    },
    review: {
        icon: Star,
        tint: 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
    },
    ticket: {
        icon: LifeBuoy,
        tint: 'bg-sky-500/10 text-sky-600 dark:text-sky-400',
    },
    contact: {
        icon: Inbox,
        tint: 'bg-violet-500/10 text-violet-600 dark:text-violet-400',
    },
};

const markAsRead = (id: string) => {
    router.post(
        read.url(id),
        {},
        { preserveScroll: true, preserveState: true },
    );
};

const markAllAsRead = () => {
    router.post(
        readAll.url(),
        {},
        { preserveScroll: true, preserveState: true },
    );
};
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button
                variant="ghost"
                size="icon"
                class="group relative h-9 w-9 cursor-pointer"
                :aria-label="copy.heading"
            >
                <Bell class="size-5 opacity-80 group-hover:opacity-100" />
                <span
                    v-if="total > 0"
                    class="absolute -top-0.5 -right-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-sidebar-primary px-1 text-[10px] leading-none font-semibold text-sidebar-primary-foreground"
                >
                    {{ badgeLabel }}
                </span>
            </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent
            align="end"
            :side-offset="8"
            class="w-80 p-0 sm:w-96"
        >
            <div class="flex items-center justify-between px-3 py-2.5">
                <span class="text-sm font-semibold">{{ copy.heading }}</span>
                <div class="flex items-center gap-2">
                    <button
                        v-if="markableCount > 0"
                        type="button"
                        class="cursor-pointer text-xs font-medium text-muted-foreground hover:text-foreground"
                        @click="markAllAsRead"
                    >
                        {{ copy.markAllRead }}
                    </button>
                    <Badge
                        v-if="total > 0"
                        variant="secondary"
                        class="rounded-full px-2"
                    >
                        {{ total }}
                    </Badge>
                </div>
            </div>

            <div
                v-if="items.length > 0"
                class="max-h-96 overflow-y-auto border-t px-1.5 py-1.5"
            >
                <div
                    v-for="(item, index) in items"
                    :key="item.id ?? `${item.type}-${index}`"
                    class="group flex items-start gap-2 rounded-md px-2 py-2.5 hover:bg-accent"
                >
                    <Link
                        :href="item.href"
                        class="flex min-w-0 flex-1 items-start gap-3 text-left"
                    >
                        <span
                            class="flex size-8 shrink-0 items-center justify-center rounded-full"
                            :class="typeMeta[item.type].tint"
                        >
                            <component
                                :is="typeMeta[item.type].icon"
                                class="size-4"
                            />
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-medium">{{
                                item.title
                            }}</span>
                            <span
                                class="block truncate text-xs text-muted-foreground"
                                >{{ item.subtitle }}</span
                            >
                        </span>
                    </Link>
                    <span class="flex shrink-0 flex-col items-end gap-1 pt-0.5">
                        <span class="text-xs text-muted-foreground">
                            {{ timeAgo(item.timestamp, locale) }}
                        </span>
                        <button
                            v-if="item.id"
                            type="button"
                            class="cursor-pointer text-muted-foreground opacity-0 transition-opacity group-hover:opacity-100 hover:text-foreground"
                            :aria-label="copy.dismiss"
                            @click.stop.prevent="markAsRead(item.id)"
                        >
                            <X class="size-3.5" />
                        </button>
                    </span>
                </div>
            </div>

            <div
                v-else
                class="flex flex-col items-center gap-2 border-t px-6 py-8 text-center"
            >
                <PartyPopper class="size-5 text-muted-foreground" />
                <p class="text-sm font-medium">{{ copy.empty }}</p>
                <p class="text-xs text-muted-foreground">
                    {{ copy.emptyHint }}
                </p>
            </div>
        </DropdownMenuContent>
    </DropdownMenu>
</template>

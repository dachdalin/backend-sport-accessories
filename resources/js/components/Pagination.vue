<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from '@lucide/vue';
import { computed } from 'vue';

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type PaginationMeta = {
    links: PaginationLink[];
    from: number | null;
    to: number | null;
    total: number;
    current_page: number;
    last_page: number;
};

const props = withDefaults(
    defineProps<{
        meta: PaginationMeta;
        label?: string;
    }>(),
    {
        label: 'results',
    },
);

// Laravel's paginator always shapes `links` as [previous, ...page/dots, next].
const previous = computed(() => props.meta.links[0]);
const next = computed(
    () => props.meta.links[props.meta.links.length - 1],
);
const pages = computed(() => props.meta.links.slice(1, -1));

const hasMultiplePages = computed(() => props.meta.links.length > 3);
</script>

<template>
    <div
        v-if="hasMultiplePages"
        class="flex flex-col items-center justify-between gap-3 sm:flex-row"
    >
        <p class="text-sm text-muted-foreground tabular-nums">
            Showing {{ meta.from }}–{{ meta.to }} of {{ meta.total }}
            {{ label }}
        </p>

        <div
            class="flex items-stretch overflow-hidden rounded-lg border border-sidebar-border/70 dark:border-sidebar-border"
        >
            <Link
                v-if="previous.url"
                :href="previous.url"
                preserve-scroll
                aria-label="Go to previous page"
                class="flex items-center border-r border-sidebar-border/70 px-2.5 py-1.5 text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground dark:border-sidebar-border"
            >
                <ChevronLeft class="size-4" />
            </Link>
            <span
                v-else
                aria-hidden="true"
                class="flex items-center border-r border-sidebar-border/70 px-2.5 py-1.5 text-muted-foreground/40 dark:border-sidebar-border"
            >
                <ChevronLeft class="size-4" />
            </span>

            <template v-for="(page, index) in pages" :key="index">
                <span
                    v-if="!page.url"
                    class="hidden items-center px-3 py-1.5 text-sm text-muted-foreground sm:flex"
                    >{{ page.label }}</span
                >
                <Link
                    v-else
                    :href="page.url"
                    preserve-scroll
                    class="hidden items-center px-3 py-1.5 text-sm transition-colors sm:flex"
                    :class="
                        page.active
                            ? 'bg-primary font-medium text-primary-foreground'
                            : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground'
                    "
                    :aria-current="page.active ? 'page' : undefined"
                    >{{ page.label }}</Link
                >
            </template>

            <span
                class="flex items-center border-l border-sidebar-border/70 px-3 py-1.5 text-sm font-medium tabular-nums sm:hidden dark:border-sidebar-border"
            >
                Page {{ meta.current_page }} of {{ meta.last_page }}
            </span>

            <Link
                v-if="next.url"
                :href="next.url"
                preserve-scroll
                aria-label="Go to next page"
                class="flex items-center border-l border-sidebar-border/70 px-2.5 py-1.5 text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground dark:border-sidebar-border"
            >
                <ChevronRight class="size-4" />
            </Link>
            <span
                v-else
                aria-hidden="true"
                class="flex items-center border-l border-sidebar-border/70 px-2.5 py-1.5 text-muted-foreground/40 dark:border-sidebar-border"
            >
                <ChevronRight class="size-4" />
            </span>
        </div>
    </div>
</template>

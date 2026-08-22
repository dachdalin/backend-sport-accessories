<script setup lang="ts">
import type { Component } from 'vue';

export type StatAccent = 'chart-1' | 'chart-2' | 'chart-3' | 'chart-4';

const props = withDefaults(
    defineProps<{
        label: string;
        value: string;
        caption?: string;
        icon: Component;
        accent?: StatAccent;
    }>(),
    {
        accent: 'chart-1',
    },
);

const barClasses: Record<StatAccent, string> = {
    'chart-1': 'bg-chart-1',
    'chart-2': 'bg-chart-2',
    'chart-3': 'bg-chart-3',
    'chart-4': 'bg-chart-4',
};

const tileClasses: Record<StatAccent, string> = {
    'chart-1': 'bg-chart-1/12 text-chart-1',
    'chart-2': 'bg-chart-2/12 text-chart-2',
    'chart-3': 'bg-chart-3/12 text-chart-3',
    'chart-4': 'bg-chart-4/12 text-chart-4',
};

const barClass = barClasses[props.accent];
const tileClass = tileClasses[props.accent];
</script>

<template>
    <div
        class="group relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-card p-5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-sm dark:border-sidebar-border"
    >
        <span
            class="absolute inset-x-0 top-0 h-0.5"
            :class="barClass"
            aria-hidden="true"
        />

        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p
                    class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
                >
                    {{ label }}
                </p>
                <p
                    class="mt-2 text-2xl font-semibold tracking-tight tabular-nums"
                >
                    {{ value }}
                </p>
                <p v-if="caption" class="mt-1 text-xs text-muted-foreground">
                    {{ caption }}
                </p>
            </div>

            <div
                class="flex size-9 shrink-0 items-center justify-center rounded-lg"
                :class="tileClass"
            >
                <component :is="icon" class="size-4" aria-hidden="true" />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { TriangleAlert, Zap } from '@lucide/vue';
import { computed } from 'vue';
import { contrastRatio, expandHex } from '@/lib/colors';

const props = defineProps<{
    title: string;
    startDate: string;
    endDate: string;
    backgroundColor: string;
    textColor: string;
}>();

const resolvedBackground = computed(() => expandHex(props.backgroundColor));
const resolvedText = computed(() => expandHex(props.textColor));
const hasCustomColors = computed(
    () => resolvedBackground.value !== null || resolvedText.value !== null,
);

const badgeStyle = computed(() => ({
    backgroundColor: resolvedBackground.value ?? 'var(--color-muted)',
    color: resolvedText.value ?? 'var(--color-foreground)',
}));

const isLowContrast = computed(() => {
    const ratio = contrastRatio(props.backgroundColor, props.textColor);

    return ratio !== null && ratio < 3;
});

function formatDate(value: string): string {
    const date = new Date(`${value}T00:00:00`);

    return Number.isNaN(date.getTime())
        ? ''
        : date.toLocaleDateString(undefined, {
              month: 'short',
              day: 'numeric',
          });
}

const scheduleLabel = computed(() => {
    if (!props.startDate || !props.endDate) {
        return 'Set a start and end date to schedule this deal.';
    }

    const start = new Date(`${props.startDate}T00:00:00`);
    const end = new Date(`${props.endDate}T23:59:59`);

    if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime())) {
        return '';
    }

    const range = `${formatDate(props.startDate)} – ${formatDate(props.endDate)}`;
    const now = new Date();

    if (now < start) {
        const days = Math.ceil((start.getTime() - now.getTime()) / 86_400_000);

        return `Starts in ${days} day${days === 1 ? '' : 's'} · ${range}`;
    }

    if (now > end) {
        return `Ended ${formatDate(props.endDate)} · ${range}`;
    }

    const daysLeft = Math.ceil((end.getTime() - now.getTime()) / 86_400_000);

    return `Live now, ends in ${daysLeft} day${daysLeft === 1 ? '' : 's'} · ${range}`;
});
</script>

<template>
    <div class="space-y-3">
        <div
            class="flex flex-wrap items-center gap-2 rounded-lg px-4 py-3 text-sm font-semibold break-words transition-colors"
            :style="badgeStyle"
        >
            <Zap class="size-4 shrink-0" aria-hidden="true" />
            <span>{{ props.title || 'Your flash deal title' }}</span>
        </div>

        <p class="text-xs text-muted-foreground">{{ scheduleLabel }}</p>

        <p v-if="!hasCustomColors" class="text-xs text-muted-foreground">
            No custom colors set yet — shown here with default styling.
        </p>

        <p
            v-else-if="isLowContrast"
            class="flex items-center gap-1.5 text-xs text-amber-600 dark:text-amber-500"
        >
            <TriangleAlert class="size-3.5 shrink-0" aria-hidden="true" />
            Background and text colors are close in tone — the title may be hard
            to read.
        </p>
    </div>
</template>

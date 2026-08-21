<script setup lang="ts">
defineProps<{
    label: string;
    value: string;
    caption?: string;
}>();
</script>

<template>
    <div
        class="rounded-xl border border-sidebar-border/70 bg-card p-5 dark:border-sidebar-border"
    >
        <p class="text-xs font-medium tracking-wide text-muted-foreground uppercase">
            {{ label }}
        </p>
        <p class="mt-2 text-2xl font-semibold tabular-nums tracking-tight">
            {{ value }}
        </p>
        <p v-if="caption" class="mt-1 text-xs text-muted-foreground">
            {{ caption }}
        </p>
    </div>
</template>

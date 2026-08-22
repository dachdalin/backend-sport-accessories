<script setup lang="ts">
type TopProduct = {
    id: number;
    name: string;
    thumbnail: string;
    thumbnailStorageType: string;
    unitsSold: number;
    revenue: string;
    share: number;
};

defineProps<{
    products: TopProduct[];
}>();

const rankChip = [
    'bg-chart-1 text-white',
    'bg-chart-2/15 text-chart-2',
    'bg-chart-3/15 text-chart-3',
    'bg-chart-4/15 text-chart-4',
    'bg-chart-5/15 text-chart-5',
];

const rankBar = [
    'bg-chart-1',
    'bg-chart-2',
    'bg-chart-3',
    'bg-chart-4',
    'bg-chart-5',
];
</script>

<template>
    <div
        v-if="products.length"
        class="divide-y divide-sidebar-border/70 dark:divide-sidebar-border"
    >
        <div
            v-for="(product, index) in products"
            :key="product.id"
            class="flex items-center gap-3 px-5 py-3 transition-colors first:pt-4 last:pb-4 hover:bg-muted/40 motion-safe:animate-in motion-safe:fill-mode-backwards motion-safe:fade-in motion-safe:slide-in-from-bottom-1"
            :style="{ animationDelay: `${index * 60}ms` }"
        >
            <span
                class="flex size-6 shrink-0 items-center justify-center rounded-full text-[11px] font-bold tabular-nums"
                :class="rankChip[index % rankChip.length]"
            >
                {{ index + 1 }}
            </span>

            <img
                :src="`/storage/${product.thumbnail}`"
                :alt="product.name"
                class="size-9 shrink-0 rounded-md object-cover"
            />

            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium">{{ product.name }}</p>
                <div class="mt-1.5 flex items-center gap-2">
                    <div
                        class="h-1 min-w-0 flex-1 overflow-hidden rounded-full bg-muted"
                    >
                        <div
                            class="h-full rounded-full"
                            :class="rankBar[index % rankBar.length]"
                            :style="{ width: `${Math.max(product.share, 4)}%` }"
                        />
                    </div>
                    <span
                        class="shrink-0 text-[11px] text-nowrap text-muted-foreground"
                    >
                        {{ product.unitsSold }} sold
                    </span>
                </div>
            </div>

            <p class="shrink-0 text-sm font-semibold tabular-nums">
                ${{ product.revenue }}
            </p>
        </div>
    </div>

    <p v-else class="px-5 py-8 text-center text-sm text-muted-foreground">
        No product sales in this period yet.
    </p>
</template>

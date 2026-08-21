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

const rankFill = [
    'bg-chart-1/15',
    'bg-chart-2/15',
    'bg-chart-3/15',
    'bg-chart-4/15',
    'bg-chart-5/15',
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
            class="relative overflow-hidden px-5 py-3 first:pt-4 last:pb-4"
        >
            <div
                class="absolute inset-y-0 left-0"
                :class="rankFill[index % rankFill.length]"
                :style="{ width: `${Math.max(product.share, 4)}%` }"
            />

            <div class="relative flex items-center gap-3">
                <span
                    class="w-5 shrink-0 text-right text-sm font-semibold tabular-nums text-muted-foreground"
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
                    <p class="text-xs text-muted-foreground">
                        {{ product.unitsSold }} sold
                    </p>
                </div>

                <p class="shrink-0 text-sm font-semibold tabular-nums">
                    ${{ product.revenue }}
                </p>
            </div>
        </div>
    </div>

    <p v-else class="px-5 py-8 text-center text-sm text-muted-foreground">
        No product sales in this period yet.
    </p>
</template>

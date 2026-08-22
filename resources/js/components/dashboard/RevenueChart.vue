<script setup lang="ts">
import {
    CategoryScale,
    Chart as ChartJS,
    Filler,
    LinearScale,
    LineElement,
    PointElement,
    Tooltip,
} from 'chart.js';
import type { ChartData, ChartOptions, TooltipItem } from 'chart.js';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Line } from 'vue-chartjs';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Filler,
    Tooltip,
);

const props = defineProps<{
    points: { label: string; revenue: string }[];
}>();

const themeTick = ref(0);
let observer: MutationObserver | null = null;

onMounted(() => {
    observer = new MutationObserver(() => {
        themeTick.value++;
    });
    observer.observe(document.documentElement, { attributeFilter: ['class'] });
});

onBeforeUnmount(() => {
    observer?.disconnect();
});

function cssVar(name: string): string {
    return getComputedStyle(document.documentElement)
        .getPropertyValue(name)
        .trim();
}

const chartData = computed<ChartData<'line'>>(() => {
    // Re-evaluated whenever the theme toggles, so the line and fill track light/dark tokens.
    void themeTick.value;

    const accent = cssVar('--chart-1') || 'hsl(220 70% 50%)';
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    let fill: CanvasGradient | string = accent;

    if (ctx) {
        fill = ctx.createLinearGradient(0, 0, 0, 240);
        fill.addColorStop(0, accent.replace(')', ' / 0.28)'));
        fill.addColorStop(1, accent.replace(')', ' / 0)'));
    }

    return {
        labels: props.points.map((point) => point.label),
        datasets: [
            {
                label: 'Revenue',
                data: props.points.map((point) => Number(point.revenue)),
                borderColor: accent,
                backgroundColor: fill,
                borderWidth: 2,
                pointRadius: 0,
                pointHoverRadius: 4,
                pointHoverBackgroundColor: accent,
                tension: 0.35,
                fill: true,
            },
        ],
    };
});

const chartOptions = computed<ChartOptions<'line'>>(() => {
    void themeTick.value;

    const gridColor = cssVar('--sidebar-border') || 'hsl(0 0% 91%)';
    const textColor = cssVar('--muted-foreground') || 'hsl(0 0% 45%)';

    return {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: cssVar('--popover') || '#fff',
                titleColor: cssVar('--popover-foreground') || '#000',
                bodyColor: cssVar('--popover-foreground') || '#000',
                borderColor: gridColor,
                borderWidth: 1,
                padding: 10,
                displayColors: false,
                callbacks: {
                    label: (context: TooltipItem<'line'>) =>
                        `$${(context.parsed.y ?? 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`,
                },
            },
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { color: textColor, font: { size: 11 } },
            },
            y: {
                beginAtZero: true,
                grid: { color: gridColor },
                border: { display: false },
                ticks: {
                    color: textColor,
                    font: { size: 11 },
                    callback: (value) => `$${value}`,
                },
            },
        },
    };
});
</script>

<template>
    <div class="h-64">
        <Line :data="chartData" :options="chartOptions" />
    </div>
</template>

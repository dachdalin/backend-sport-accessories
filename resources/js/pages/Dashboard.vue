<script setup lang="ts">
import { Deferred, Head, router } from '@inertiajs/vue3';
import { Receipt, ShoppingBag, Users, Wallet } from '@lucide/vue';
import { computed } from 'vue';
import DateRangeFilter from '@/components/dashboard/DateRangeFilter.vue';
import type { DashboardFilter } from '@/components/dashboard/DateRangeFilter.vue';
import RevenueChart from '@/components/dashboard/RevenueChart.vue';
import StatCard from '@/components/dashboard/StatCard.vue';
import type { StatAccent } from '@/components/dashboard/StatCard.vue';
import TopProductsCard from '@/components/dashboard/TopProductsCard.vue';
import Heading from '@/components/Heading.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import { dashboard } from '@/routes';

type Stats = {
    revenue: string;
    orders: number;
    averageOrderValue: string;
    customers: number;
};

type SalesPoint = { label: string; revenue: string };

type TopProduct = {
    id: number;
    name: string;
    thumbnail: string;
    thumbnailStorageType: string;
    unitsSold: number;
    revenue: string;
    share: number;
};

const props = defineProps<{
    filter: DashboardFilter;
    from: string | null;
    to: string | null;
    stats?: Stats;
    salesChart?: SalesPoint[];
    topProducts?: TopProduct[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

const filterLabels: Record<DashboardFilter, string> = {
    today: 'today',
    this_week: 'this week',
    this_month: 'this month',
    this_year: 'this year',
    custom: 'the selected range',
};

const periodLabel = computed(() => filterLabels[props.filter]);

function parseIsoDate(value: string): Date {
    const [year, month, day] = value.split('-').map(Number);

    return new Date(year, month - 1, day);
}

const dayFormatter = new Intl.DateTimeFormat(undefined, {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
});
const shortDayFormatter = new Intl.DateTimeFormat(undefined, {
    month: 'short',
    day: 'numeric',
});
const monthFormatter = new Intl.DateTimeFormat(undefined, {
    month: 'long',
    year: 'numeric',
});

const periodRange = computed(() => {
    const now = new Date();

    if (props.filter === 'this_month') {
        return monthFormatter.format(now);
    }

    if (props.filter === 'this_year') {
        return String(now.getFullYear());
    }

    if (props.filter === 'this_week') {
        const mondayOffset = (now.getDay() + 6) % 7;
        const start = new Date(now);
        start.setDate(now.getDate() - mondayOffset);
        const end = new Date(start);
        end.setDate(start.getDate() + 6);

        return `${shortDayFormatter.format(start)} – ${dayFormatter.format(end)}`;
    }

    if (props.filter === 'custom' && props.from && props.to) {
        return `${shortDayFormatter.format(parseIsoDate(props.from))} – ${dayFormatter.format(parseIsoDate(props.to))}`;
    }

    return dayFormatter.format(now);
});

const statCards = computed(() => [
    {
        label: 'Revenue',
        value: `$${props.stats?.revenue ?? '0.00'}`,
        icon: Wallet,
        accent: 'chart-1' as const satisfies StatAccent,
    },
    {
        label: 'Orders',
        value: String(props.stats?.orders ?? 0),
        icon: ShoppingBag,
        accent: 'chart-2' as const satisfies StatAccent,
    },
    {
        label: 'Avg. order value',
        value: `$${props.stats?.averageOrderValue ?? '0.00'}`,
        icon: Receipt,
        accent: 'chart-3' as const satisfies StatAccent,
    },
    {
        label: 'Customers',
        value: String(props.stats?.customers ?? 0),
        icon: Users,
        accent: 'chart-4' as const satisfies StatAccent,
    },
]);

function applyFilter(
    filter: DashboardFilter,
    from: string | null,
    to: string | null,
) {
    router.get(
        dashboard.url({
            query: {
                filter,
                ...(from ? { from } : {}),
                ...(to ? { to } : {}),
            },
        }),
        {},
        { preserveScroll: true },
    );
}
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-col gap-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <Heading
                title="Overview"
                :description="`Store performance for ${periodLabel} · ${periodRange}`"
            />

            <DateRangeFilter
                :model-value="filter"
                :from="from"
                :to="to"
                @apply="applyFilter"
            />
        </div>

        <Deferred :data="['stats', 'salesChart', 'topProducts']">
            <template #fallback>
                <div class="flex flex-col gap-6">
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div
                            v-for="i in 4"
                            :key="i"
                            class="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-card p-5 dark:border-sidebar-border"
                        >
                            <Skeleton
                                class="absolute inset-x-0 top-0 h-0.5 rounded-none"
                            />
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <Skeleton class="h-3 w-20" />
                                    <Skeleton class="mt-3 h-7 w-24" />
                                </div>
                                <Skeleton class="size-9 shrink-0 rounded-lg" />
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 lg:grid-cols-3">
                        <div
                            class="rounded-xl border border-sidebar-border/70 bg-card p-5 lg:col-span-2 dark:border-sidebar-border"
                        >
                            <Skeleton class="h-4 w-32" />
                            <Skeleton class="mt-6 h-64 w-full" />
                        </div>

                        <div
                            class="rounded-xl border border-sidebar-border/70 bg-card dark:border-sidebar-border"
                        >
                            <div class="p-5 pb-3">
                                <Skeleton class="h-4 w-28" />
                            </div>
                            <div class="space-y-4 px-5 pb-5">
                                <div
                                    v-for="i in 5"
                                    :key="i"
                                    class="flex items-center gap-3"
                                >
                                    <Skeleton
                                        class="size-6 shrink-0 rounded-full"
                                    />
                                    <Skeleton
                                        class="size-9 shrink-0 rounded-md"
                                    />
                                    <div class="flex-1 space-y-1.5">
                                        <Skeleton class="h-3.5 w-3/4" />
                                        <Skeleton
                                            class="h-1 w-full rounded-full"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <div class="flex flex-col gap-6">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        v-for="(card, index) in statCards"
                        :key="card.label"
                        v-bind="card"
                        class="motion-safe:animate-in motion-safe:fill-mode-backwards motion-safe:fade-in motion-safe:slide-in-from-bottom-1"
                        :style="{ animationDelay: `${index * 60}ms` }"
                    />
                </div>

                <div class="grid gap-4 lg:grid-cols-3">
                    <Card class="lg:col-span-2">
                        <CardHeader>
                            <CardTitle
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Revenue trend
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <RevenueChart :points="salesChart ?? []" />
                        </CardContent>
                    </Card>

                    <Card class="gap-3 py-5">
                        <CardHeader class="px-5">
                            <CardTitle
                                class="text-sm font-medium text-muted-foreground"
                            >
                                Top products
                            </CardTitle>
                        </CardHeader>
                        <TopProductsCard :products="topProducts ?? []" />
                    </Card>
                </div>
            </div>
        </Deferred>
    </div>
</template>

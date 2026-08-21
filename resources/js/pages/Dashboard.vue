<script setup lang="ts">
import { Deferred, Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import DateRangeFilter, {
    type DashboardFilter,
} from '@/components/dashboard/DateRangeFilter.vue';
import RevenueChart from '@/components/dashboard/RevenueChart.vue';
import StatCard from '@/components/dashboard/StatCard.vue';
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

function applyFilter(filter: DashboardFilter, from: string | null, to: string | null) {
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
                :description="`Store performance for ${periodLabel}`"
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
                            class="rounded-xl border border-sidebar-border/70 bg-card p-5 dark:border-sidebar-border"
                        >
                            <Skeleton class="h-3 w-20" />
                            <Skeleton class="mt-3 h-7 w-24" />
                            <Skeleton class="mt-2 h-3 w-16" />
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
                                    <Skeleton class="size-9 shrink-0 rounded-md" />
                                    <div class="flex-1 space-y-1.5">
                                        <Skeleton class="h-3.5 w-3/4" />
                                        <Skeleton class="h-3 w-1/3" />
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
                        label="Revenue"
                        :value="`$${stats?.revenue ?? '0.00'}`"
                    />
                    <StatCard
                        label="Orders"
                        :value="String(stats?.orders ?? 0)"
                    />
                    <StatCard
                        label="Avg. order value"
                        :value="`$${stats?.averageOrderValue ?? '0.00'}`"
                    />
                    <StatCard
                        label="Customers"
                        :value="String(stats?.customers ?? 0)"
                    />
                </div>

                <div class="grid gap-4 lg:grid-cols-3">
                    <Card class="lg:col-span-2">
                        <CardHeader>
                            <CardTitle class="text-sm font-medium text-muted-foreground">
                                Revenue trend
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <RevenueChart :points="salesChart ?? []" />
                        </CardContent>
                    </Card>

                    <Card class="gap-3 py-5">
                        <CardHeader class="px-5">
                            <CardTitle class="text-sm font-medium text-muted-foreground">
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
